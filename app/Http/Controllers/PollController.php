<?php

namespace App\Http\Controllers;

use App\Services\CoverService;
use App\Services\DynamoDbService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PollController extends Controller
{
    public function __construct(
        private DynamoDbService $dynamo,
        private CoverService $covers,
    ) {}

    public function create()
    {
        $today = now();
        $dayOfWeek = (int) $today->format('N'); // 1=Mon … 7=Sun
        $daysUntilWednesday = $dayOfWeek <= 3 ? 3 - $dayOfWeek : 10 - $dayOfWeek;
        $defaultTitle = $today->copy()->addDays($daysUntilWednesday)->format('n/j') . ' Poll';

        return view('home', compact('defaultTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'nullable|string|max:100',
            'input'           => 'required|string',
            'vote_limit_mode' => 'nullable|in:half,custom',
            'max_votes'       => 'nullable|integer|min:1',
        ]);

        $movies = $this->parseTsv($request->input('input'));

        if (empty($movies)) {
            return back()->withInput()->withErrors(['input' => 'No valid movie rows found. Make sure you paste the full spreadsheet export.']);
        }

        $movies = $this->covers->attachCovers($movies);

        $movieCount = count($movies);
        $mode = $request->input('vote_limit_mode', 'half');
        if ($mode === 'custom' && $request->filled('max_votes')) {
            $maxVotes = max(1, min((int) $request->input('max_votes'), $movieCount));
        } else {
            $maxVotes = max(1, (int) floor($movieCount / 2));
        }

        $slug = Str::random(8);
        $title = trim($request->input('title', ''));
        $this->dynamo->putPoll($slug, $movies, $maxVotes, $title);

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
