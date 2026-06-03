<?php

namespace App\Http\Controllers;

use App\Services\DynamoDbService;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function __construct(private DynamoDbService $dynamo) {}

    public function store(Request $request, string $slug)
    {
        $request->validate([
            'voter_name' => 'required|string|max:100',
            'ranking'    => 'required|string',
        ]);

        $poll = $this->dynamo->getPoll($slug);
        if (!$poll) {
            abort(404);
        }

        $ranking = json_decode($request->input('ranking'), true);
        if (!is_array($ranking) || empty($ranking)) {
            return back()->withErrors(['ranking' => 'Invalid ranking submitted.']);
        }

        $validTitles = array_column($poll['movies'], 'title');
        $ranking = array_values(array_filter($ranking, fn($t) => in_array($t, $validTitles)));

        $this->dynamo->putVote($slug, trim($request->input('voter_name')), $ranking);

        return redirect()->route('poll.results', $slug)
            ->cookie('voter_name', trim($request->input('voter_name')), 60 * 24 * 365);
    }

    public function results(string $slug)
    {
        $poll = $this->dynamo->getPoll($slug);
        if (!$poll) {
            abort(404);
        }

        $maxVotes = (int) ($poll['max_votes'] ?? max(1, (int) floor(count($poll['movies']) / 2)));
        $votes    = $this->dynamo->getVotes($slug);
        $scores   = $this->calculateScores($poll['movies'], $votes, $maxVotes);

        return view('results', compact('poll', 'votes', 'scores'));
    }

    private function calculateScores(array $movies, array $votes, int $maxVotes): array
    {
        $byTitle = [];
        foreach ($movies as $movie) {
            $byTitle[$movie['title']] = ['movie' => $movie, 'points' => 0, 'first_place_voters' => []];
        }

        foreach ($votes as $vote) {
            $ranking = $vote['ranking'] ?? [];
            foreach ($ranking as $rank => $title) {
                if (isset($byTitle[$title])) {
                    $byTitle[$title]['points'] += max($maxVotes - $rank, 0);
                    if ($rank === 0) {
                        $byTitle[$title]['first_place_voters'][] = $vote['voter_name'];
                    }
                }
            }
        }

        $scores = array_values($byTitle);
        usort($scores, fn($a, $b) => $b['points'] <=> $a['points']);
        return $scores;
    }
}
