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
        return view('home');
    }

    public function store(Request $request)
    {
        $request->validate([
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
        $this->dynamo->putPoll($slug, $movies, $maxVotes);

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

            if (count($fields) < 6) {
                continue;
            }

            $dateAdded = trim($fields[1]);

            // Data rows have a date in column 1 (MM/DD/YYYY)
            if (!preg_match('#^\d{1,2}/\d{1,2}/\d{4}$#', $dateAdded)) {
                continue;
            }

            $movies[] = [
                'watched'      => trim($fields[0]),
                'date_added'   => $dateAdded,
                'title'        => trim($fields[2]),
                'imdb_link'    => trim($fields[3]),
                'synopsis'     => trim($fields[4]),
                'suggested_by' => trim($fields[5]),
            ];
        }

        return $movies;
    }
}
