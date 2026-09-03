<?php

namespace App\Http\Controllers;

use App\Services\CoverService;
use App\Services\DynamoDbService;
use App\Services\HolidayTsvParser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PollController extends Controller
{
    public function __construct(
        private DynamoDbService $dynamo,
        private CoverService $covers,
        private HolidayTsvParser $holidayParser,
    ) {}

    public function create()
    {
        $today = now();
        $dayOfWeek = (int) $today->format('N'); // 1=Mon … 7=Sun
        $daysUntilWednesday = $dayOfWeek <= 3 ? 3 - $dayOfWeek : 10 - $dayOfWeek;
        $defaultTitle = $today->copy()->addDays($daysUntilWednesday)->format('n/j') . ' Poll';
        $holidays = config('holidays');
        $currentYear = (int) $today->format('Y');

        return view('home', compact('defaultTitle', 'holidays', 'currentYear'));
    }

    public function store(Request $request)
    {
        $holidayKeys = array_keys(config('holidays'));
        $currentYear = (int) now()->format('Y');

        $request->validate([
            'title'           => 'nullable|string|max:100',
            'input'           => 'required|string',
            'poll_type'       => 'nullable|in:regular,holiday',
            'vote_limit_mode' => 'nullable|in:half,custom',
            'max_votes'       => 'nullable|integer|min:1',
            'theme'           => ['required_if:poll_type,holiday', 'nullable', Rule::in($holidayKeys)],
            'year'            => 'required_if:poll_type,holiday|nullable|integer|min:2015|max:' . ($currentYear + 1),
            'winner_count'    => 'required_if:poll_type,holiday|nullable|integer|min:1',
            'closed'          => 'nullable|boolean',
        ]);

        $isHoliday = $request->input('poll_type') === 'holiday';
        $extra = [];
        $slug = Str::random(8);

        if ($isHoliday) {
            $theme = $request->input('theme');
            $year = (int) $request->input('year');
            $slug = $theme . '-' . $year;

            if ($this->dynamo->getPoll($slug)) {
                $label = config("holidays.{$theme}.label", ucfirst($theme));

                return redirect()
                    ->route('poll.create', [
                        'exists' => $slug,
                        'theme' => $theme,
                        'year' => $year,
                    ])
                    ->withInput($request->except('input'))
                    ->withErrors([
                        'theme' => "A {$label} {$year} poll already exists.",
                    ]);
            }
        }

        $movies = $isHoliday
            ? $this->holidayParser->parse($request->input('input'))
            : $this->parseTsv($request->input('input'));

        if (empty($movies)) {
            return back()->withInput()->withErrors(['input' => 'No valid movie rows found. Make sure you paste the full spreadsheet export.']);
        }

        $movies = $this->covers->attachCovers($movies);
        $movieCount = count($movies);

        if ($isHoliday) {
            $winnerCount = max(1, min((int) $request->input('winner_count'), $movieCount));

            if ($request->filled('max_votes')) {
                $maxVotes = max(1, min((int) $request->input('max_votes'), $movieCount));
            } else {
                $maxVotes = $winnerCount;
            }

            $extra = [
                'poll_type'    => 'holiday',
                'theme'        => $theme,
                'year'         => $year,
                'winner_count' => $winnerCount,
            ];

            if ($request->boolean('closed')) {
                $extra['status'] = 'closed';
            }
        } else {
            $mode = $request->input('vote_limit_mode', 'half');
            if ($mode === 'custom' && $request->filled('max_votes')) {
                $maxVotes = max(1, min((int) $request->input('max_votes'), $movieCount));
            } else {
                $maxVotes = max(1, (int) floor($movieCount / 2));
            }
        }

        $title = trim($request->input('title', ''));
        $this->dynamo->putPoll($slug, $movies, $maxVotes, $title, $extra);

        return redirect()->route('poll.show', $slug);
    }

    public function show(string $slug)
    {
        $poll = $this->dynamo->getPoll($slug);

        if (!$poll) {
            abort(404);
        }

        return view('poll', ['poll' => $poll]);
    }

    private function parseTsv(string $raw): array
    {
        $lines = explode("\n", str_replace("\r", '', $raw));
        $movies = [];

        foreach ($lines as $line) {
            $fields = explode("\t", $line);

            // Data rows have a date column (MM/DD/YYYY). The "watched" column
            // before it is optional: with it the date is in column 1, without
            // it the date is in column 0.
            $datePattern = '#^\d{1,2}/\d{1,2}/\d{4}$#';

            if (count($fields) >= 6 && preg_match($datePattern, trim($fields[1]))) {
                $watched = trim($fields[0]);
                $rest = array_slice($fields, 1);
            } elseif (count($fields) >= 5 && preg_match($datePattern, trim($fields[0]))) {
                $watched = '';
                $rest = $fields;
            } else {
                continue;
            }

            $movies[] = [
                'watched'      => $watched,
                'date_added'   => trim($rest[0]),
                'title'        => trim($rest[1]),
                'imdb_link'    => trim($rest[2]),
                'synopsis'     => trim($rest[3]),
                'suggested_by' => trim($rest[4]),
            ];
        }

        return $movies;
    }
}
