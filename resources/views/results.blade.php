<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movie Mercredi — Results</title>
    <style>
        *, ::before, ::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f0f13;
            color: #e2e2e2;
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .container { max-width: 900px; margin: 0 auto; }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .logo { font-size: 1.1rem; letter-spacing: 0.15em; text-transform: uppercase; color: #e5b000; font-weight: 700; }
        h1 { font-size: 1.6rem; font-weight: 700; color: #fff; }
        .meta { color: #666; font-size: 0.9rem; margin-bottom: 2rem; }
        .meta strong { color: #aaa; }

        .score-list { display: grid; gap: 0.75rem; }

        .score-card {
            background: #1a1a24;
            border: 1px solid #2e2e42;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            align-items: stretch;
        }
        .score-card.rank-1 { border-color: #e5b000; }
        .score-card.rank-2 { border-color: #8888aa; }
        .score-card.rank-3 { border-color: #8b5e2e; }

        .score-position {
            width: 52px;
            flex-shrink: 0;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            background: #13131b;
            border-right: 1px solid #2e2e42;
            font-weight: 800;
            gap: 1px;
        }
        .rank-1 .score-position { color: #e5b000; }
        .rank-2 .score-position { color: #9999bb; }
        .rank-3 .score-position { color: #a0713a; }
        .score-position .pos-num { font-size: 1.2rem; }
        .score-position .pos-suffix { font-size: 0.65rem; color: inherit; opacity: 0.7; }

        .movie-cover {
            width: 70px;
            flex-shrink: 0;
            background: #111118;
        }
        .movie-cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .movie-cover-placeholder {
            width: 100%;
            height: 100%;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #2e2e42;
        }

        .score-body { flex: 1; padding: 1rem 1.25rem; min-width: 0; }
        .score-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }
        .movie-title { font-size: 1rem; font-weight: 700; color: #fff; }
        .movie-title a { color: inherit; text-decoration: none; }
        .movie-title a:hover { color: #e5b000; }

        .score-points { font-size: 1rem; font-weight: 700; color: #e5b000; white-space: nowrap; flex-shrink: 0; }
        .score-points .pts-label { font-size: 0.72rem; color: #666; font-weight: 400; margin-left: 3px; }

        .score-bar-wrap { margin-top: 0.5rem; background: #0f0f13; border-radius: 3px; height: 4px; overflow: hidden; }
        .score-bar { height: 100%; border-radius: 3px; background: #e5b000; transition: width 0.4s ease; }
        .rank-2 .score-bar { background: #9999bb; }
        .rank-3 .score-bar { background: #a0713a; }

        .score-meta { margin-top: 0.4rem; color: #555; font-size: 0.78rem; }

        .badges { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.4rem; }
        .badge {
            font-size: 0.72rem;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-watched { background: #1a2a1a; color: #6ddb6d; border: 1px solid #2a3a2a; }

        .no-votes {
            text-align: center;
            color: #555;
            padding: 3rem 0;
            font-size: 1rem;
        }

        .history-section { margin-top: 2.5rem; }
        .history-section h2 { font-size: 1rem; font-weight: 700; color: #aaa; margin-bottom: 1rem; }
        .history-wrap { overflow-x: auto; }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            white-space: nowrap;
        }
        .history-table th, .history-table td {
            padding: 0.5rem 0.75rem;
            text-align: center;
            border-bottom: 1px solid #1e1e2e;
        }
        .history-table th { color: #666; font-weight: 600; }
        .history-table th.voter-col, .history-table td.voter-col {
            text-align: left;
            color: #aaa;
            font-weight: 600;
        }
        .history-table td { color: #777; }
        .history-table td.pos-1 { color: #e5b000; font-weight: 700; }
        .history-table td.pos-2 { color: #e2e2e2; }
        .history-table td.pos-3 { color: #999; }
        .history-table tbody tr:hover td { background: #1a1a24; }

        footer {
            margin-top: 3rem;
            text-align: center;
            color: #444;
            font-size: 0.8rem;
        }
        footer a { color: #666; text-decoration: none; }
        footer a:hover { color: #e5b000; }

        .admin-link {
            color: #2e2e42;
            text-decoration: none;
            line-height: 0;
            transition: color 0.2s;
        }
        .admin-link:hover { color: #666; }
        .admin-link svg { width: 18px; height: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <div class="logo">🎬 Movie Mercredi</div>
                <h1>{{ !empty($poll['title']) ? $poll['title'] : 'Results' }}</h1>
            </div>
            <a href="{{ route('admin.results', $poll['slug']) }}" class="admin-link" title="Admin">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </a>
        </header>

        @php
            $voteCount  = count($votes);
            $maxPoints  = !empty($scores) ? $scores[0]['points'] : 1;
        @endphp

        <p class="meta">
            <strong>{{ $voteCount }}</strong> {{ Str::plural('vote', $voteCount) }} cast
        </p>

        @if ($voteCount === 0)
            <div class="no-votes">No votes yet. <a href="{{ route('poll.show', $poll['slug']) }}" style="color:#e5b000">Be the first →</a></div>
        @else
            <div class="score-list">
                @foreach ($scores as $i => $entry)
                    @php
                        $pos   = $i + 1;
                        $movie = $entry['movie'];
                        $suffix = match($pos) { 1 => 'st', 2 => 'nd', 3 => 'rd', default => 'th' };
                        $rankClass = $pos <= 3 ? "rank-{$pos}" : '';
                        $barWidth = $maxPoints > 0 ? round(($entry['points'] / $maxPoints) * 100) : 0;
                    @endphp
                    <div class="score-card {{ $rankClass }}">
                        <div class="score-position">
                            <span class="pos-num">{{ $pos }}</span>
                            <span class="pos-suffix">{{ $suffix }}</span>
                        </div>
                        <div class="movie-cover">
                            @if (!empty($movie['cover_url']))
                                <img src="{{ $movie['cover_url'] }}" alt="{{ $movie['title'] }}">
                            @else
                                <div class="movie-cover-placeholder">🎬</div>
                            @endif
                        </div>
                        <div class="score-body">
                            <div class="score-header">
                                <div class="movie-title">
                                    @if (!empty($movie['imdb_link']))
                                        <a href="{{ $movie['imdb_link'] }}" target="_blank" rel="noopener">{{ $movie['title'] }}</a>
                                    @else
                                        {{ $movie['title'] }}
                                    @endif
                                </div>
                                <div class="score-points">
                                    {{ $entry['points'] }}<span class="pts-label">pts</span>
                                </div>
                            </div>
                            <div class="score-bar-wrap">
                                <div class="score-bar" style="width: {{ $barWidth }}%"></div>
                            </div>
                            <div class="score-meta">
                                @if (!empty($entry['first_place_voters']))
                                    <span title="First-place voters">{{ implode(', ', $entry['first_place_voters']) }}</span>
                                @else
                                    No first-place votes
                                @endif
                            </div>
                            @if (!empty($movie['watched']))
                                <div class="badges">
                                    <span class="badge badge-watched">✓ watched</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($voteCount > 0)
            <div class="history-section">
                <h2>All votes</h2>
                <div class="history-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th class="voter-col">Voter</th>
                                @foreach ($scores as $entry)
                                    <th title="{{ $entry['movie']['title'] }}">
                                        {{ Str::limit($entry['movie']['title'], 18) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($votes as $vote)
                                @php
                                    $rankMap = array_flip($vote['ranking'] ?? []);
                                @endphp
                                <tr>
                                    <td class="voter-col">{{ $vote['voter_name'] }}</td>
                                    @foreach ($scores as $entry)
                                        @php
                                            $title = $entry['movie']['title'];
                                            $pos   = isset($rankMap[$title]) ? $rankMap[$title] + 1 : '—';
                                            $posClass = is_int($pos) ? "pos-{$pos}" : '';
                                        @endphp
                                        <td class="{{ $posClass }}">{{ $pos }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <footer>
            <a href="{{ route('poll.show', $poll['slug']) }}">← Back to poll</a>
            &nbsp;·&nbsp;
            <a href="{{ route('poll.create') }}">Create a new poll</a>
        </footer>
    </div>
</body>
</html>
