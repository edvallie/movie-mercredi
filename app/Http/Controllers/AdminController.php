<?php

namespace App\Http\Controllers;

use App\Services\DynamoDbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct(private DynamoDbService $dynamo) {}

    public function showLogin()
    {
        return view('admin-login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $hash = env('ADMIN_PASSWORD_HASH');

        if (!$hash || !Hash::check($request->input('password'), $hash)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $request->session()->put('admin_authenticated', true);
        $request->session()->regenerate();

        return redirect()->intended(route('poll.create'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_authenticated');

        $slug = $request->input('slug');
        if ($slug) {
            return redirect()->route('poll.results', $slug);
        }

        return redirect()->route('admin.login');
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

        return view('admin-results', compact('poll', 'votes', 'scores'));
    }

    public function deleteVote(Request $request, string $slug, string $voteId)
    {
        $poll = $this->dynamo->getPoll($slug);
        if (!$poll) {
            abort(404);
        }

        $this->dynamo->deleteVote($voteId);

        return redirect()->route('admin.results', $slug)->with('success', 'Vote deleted.');
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
