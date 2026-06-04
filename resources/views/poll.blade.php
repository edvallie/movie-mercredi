<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movie Mercredi — Vote</title>
    <style>
        *, ::before, ::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f0f13;
            color: #e2e2e2;
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .logo {
            font-size: 1.1rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #e5b000;
            font-weight: 700;
        }
        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
        }
        .share-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #1a1a24;
            border: 1px solid #2e2e42;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
        }
        .share-box span { font-size: 0.8rem; color: #888; white-space: nowrap; }
        .share-box code { font-family: 'Courier New', monospace; font-size: 0.8rem; color: #e5b000; word-break: break-all; }
        .copy-btn {
            background: #2e2e42;
            border: none;
            border-radius: 5px;
            color: #aaa;
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            cursor: pointer;
            white-space: nowrap;
        }
        .copy-btn:hover { background: #3e3e58; color: #fff; }
        .instructions {
            color: #888;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }
        .count { color: #666; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .vote-limit-note {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            padding: 0.65rem 0.9rem;
            background: rgba(229, 176, 0, 0.06);
            border: 1px solid rgba(229, 176, 0, 0.2);
            border-radius: 8px;
        }
        .vote-limit-note strong { color: #e5b000; }
        .movie-grid { display: grid; gap: 0.75rem; }

        .movie-card {
            background: #1a1a24;
            border: 1px solid #2e2e42;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            transition: border-color 0.2s, box-shadow 0.2s;
            cursor: grab;
            user-select: none;
        }
        .movie-card:hover { border-color: #3e3e58; }
        .movie-card.sortable-chosen { border-color: #e5b000; box-shadow: 0 0 0 2px rgba(229,176,0,0.2); cursor: grabbing; }
        .movie-card.sortable-ghost { opacity: 0.35; }

        .movie-rank {
            width: 54px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #13131b;
            border-right: 1px solid #2e2e42;
            gap: 1px;
            padding: 4px 0;
            transition: opacity 0.2s;
        }
        .rank-action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #444;
            padding: 2px;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 0;
            transition: color 0.15s;
        }
        .rank-action-btn:hover { color: #e5b000; }
        .rank-num {
            font-size: 1.05rem;
            font-weight: 800;
            color: #e5b000;
            line-height: 1;
            cursor: text;
            min-width: 1.4em;
            text-align: center;
            border-radius: 3px;
            padding: 1px 3px;
            outline: none;
        }
        .rank-num:focus {
            background: rgba(229, 176, 0, 0.1);
            outline: 1px solid rgba(229, 176, 0, 0.5);
        }
        .rank-points {
            font-size: 0.7rem;
            font-weight: 700;
            color: #888;
            letter-spacing: 0.02em;
            line-height: 1;
        }
        .movie-card.below-cutoff { opacity: 0.55; }
        .movie-card.below-cutoff .rank-num { color: #555; }
        .movie-card.below-cutoff .rank-points { color: #444; }

        .cutoff-line {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.25rem 0.25rem;
            user-select: none;
        }
        .cutoff-line::before, .cutoff-line::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, #3e3e58, transparent);
        }
        .cutoff-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            white-space: nowrap;
        }
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
        .movie-body { flex: 1; padding: 1rem 1.25rem; min-width: 0; }
        .movie-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }
        .movie-title { font-size: 1rem; font-weight: 700; color: #fff; }
        .movie-title a { color: inherit; text-decoration: none; }
        .movie-title a:hover { color: #e5b000; }
        .badges { display: flex; gap: 0.4rem; flex-wrap: wrap; flex-shrink: 0; }
        .badge {
            font-size: 0.72rem;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-suggested { background: #2a2020; color: #e5b000; border: 1px solid #3a3020; }
        .badge-watched   { background: #1a2a1a; color: #6ddb6d; border: 1px solid #2a3a2a; }
        .badge-added     { background: #1e1e2e; color: #8888cc; border: 1px solid #2a2a3e; }
        .badge-added.stale { background: #2a1414; color: #ff6b6b; border-color: #4a2020; }
        .synopsis { color: #999; font-size: 0.85rem; line-height: 1.55; }

        .vote-form {
            margin-top: 2rem;
            background: #1a1a24;
            border: 1px solid #2e2e42;
            border-radius: 10px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .vote-form label { color: #aaa; font-size: 0.875rem; white-space: nowrap; }
        .vote-form input[type="text"] {
            flex: 1;
            min-width: 160px;
            background: #0f0f13;
            border: 1px solid #3e3e58;
            border-radius: 6px;
            color: #e2e2e2;
            font-size: 0.95rem;
            padding: 0.55rem 0.8rem;
            outline: none;
        }
        .vote-form input[type="text"]:focus { border-color: #e5b000; }
        .vote-btn {
            background: #e5b000;
            color: #0f0f13;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 700;
            padding: 0.6rem 1.5rem;
            cursor: pointer;
            white-space: nowrap;
        }
        .vote-btn:hover { background: #f5c200; }

        footer {
            margin-top: 3rem;
            text-align: center;
            color: #444;
            font-size: 0.8rem;
        }
        footer a { color: #666; text-decoration: none; }
        footer a:hover { color: #e5b000; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <div class="logo">🎬 Movie Mercredi</div>
                <h1>{{ !empty($poll['title']) ? $poll['title'] : 'Pick a Movie' }}</h1>
            </div>
            <div class="share-box">
                <span>Share:</span>
                <code id="poll-url">{{ url()->current() }}</code>
                <button class="copy-btn" onclick="copyUrl()">Copy</button>
            </div>
        </header>

        @php
            $movieCount = count($poll['movies']);
            $maxVotes = (int) ($poll['max_votes'] ?? max(1, (int) floor($movieCount / 2)));

            $submitterColor = function (string $name): string {
                $hue = hexdec(substr(md5(strtolower(trim($name))), 0, 6)) % 360;
                return "hsl({$hue}, 65%, 22%)";
            };
            $submitterBorder = function (string $name): string {
                $hue = hexdec(substr(md5(strtolower(trim($name))), 0, 6)) % 360;
                return "hsl({$hue}, 55%, 35%)";
            };
            $submitterText = function (string $name): string {
                $hue = hexdec(substr(md5(strtolower(trim($name))), 0, 6)) % 360;
                return "hsl({$hue}, 80%, 75%)";
            };

            $sixMonthsAgo = new \DateTimeImmutable('-6 months');
            $isStale = function (string $date) use ($sixMonthsAgo): bool {
                $d = \DateTimeImmutable::createFromFormat('!n/j/Y', $date);
                return $d !== false && $d < $sixMonthsAgo;
            };
        @endphp

        <p class="count">{{ $movieCount }} movies on the list</p>
        <div class="vote-limit-note">
            Your top <strong>{{ $maxVotes }}</strong> of {{ $movieCount }} count toward the score. Movies ranked below the cutoff award 0 points.
        </div>
        <p class="instructions">Drag to rank from most to least preferred, then submit your vote.</p>

        <form method="POST" action="{{ route('poll.vote', $poll['slug']) }}" id="vote-form">
            @csrf
            <input type="hidden" name="ranking" id="ranking-input">

            <div class="movie-grid" id="movie-list" data-max-votes="{{ $maxVotes }}">
                @foreach ($poll['movies'] as $movie)
                    <div class="movie-card" data-title="{{ $movie['title'] }}">
                        <div class="movie-rank">
                            <button type="button" class="rank-action-btn" onclick="sendCardToTop(this)" title="Send to top">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l7.5-7.5 7.5 7.5m-15 6l7.5-7.5 7.5 7.5" /></svg>
                            </button>
                            <span class="rank-num" contenteditable="true" spellcheck="false">{{ $loop->iteration }}</span>
                            <span class="rank-points">0 pts</span>
                            <button type="button" class="rank-action-btn" onclick="sendCardToBottom(this)" title="Send to bottom">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 5.25l-7.5 7.5-7.5-7.5m15 6l-7.5 7.5-7.5-7.5" /></svg>
                            </button>
                        </div>
                        <div class="movie-cover">
                            @if (!empty($movie['cover_url']))
                                <img src="{{ $movie['cover_url'] }}" alt="{{ $movie['title'] }}">
                            @else
                                <div class="movie-cover-placeholder">🎬</div>
                            @endif
                        </div>
                        <div class="movie-body">
                            <div class="movie-header">
                                <div class="movie-title">
                                    @if (!empty($movie['imdb_link']))
                                        <a href="{{ $movie['imdb_link'] }}" target="_blank" rel="noopener">
                                            {{ $movie['title'] }}
                                        </a>
                                    @else
                                        {{ $movie['title'] }}
                                    @endif
                                </div>
                                <div class="badges">
                                    @if (!empty($movie['suggested_by']))
                                        <span class="badge badge-suggested" style="background: {{ $submitterColor($movie['suggested_by']) }}; color: {{ $submitterText($movie['suggested_by']) }}; border-color: {{ $submitterBorder($movie['suggested_by']) }};">{{ $movie['suggested_by'] }}</span>
                                    @endif
                                    @if (!empty($movie['watched']))
                                        <span class="badge badge-watched">✓ watched {{ $movie['watched'] }}</span>
                                    @endif
                                    @if (!empty($movie['date_added']))
                                        <span class="badge badge-added {{ $isStale($movie['date_added']) ? 'stale' : '' }}">added {{ $movie['date_added'] }}</span>
                                    @endif
                                </div>
                            </div>
                            @if (!empty($movie['synopsis']))
                                <p class="synopsis">{{ $movie['synopsis'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="vote-form">
                <label for="voter-name">Your name:</label>
                <input
                    type="text"
                    id="voter-name"
                    name="voter_name"
                    placeholder="Enter your name"
                    value="{{ old('voter_name', request()->cookie('voter_name', '')) }}"
                    autocomplete="off"
                    required
                >
                <button type="submit" class="vote-btn">Submit Vote</button>
            </div>

            @if ($errors->any())
                <p style="color:#e55; font-size:0.85rem; margin-top:0.75rem;">{{ $errors->first() }}</p>
            @endif
        </form>

        <footer>
            <a href="{{ route('poll.results', $poll['slug']) }}">View results →</a>
            &nbsp;·&nbsp;
            <a href="{{ route('poll.create') }}">← Create a new poll</a>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
    <script>
        const list = document.getElementById('movie-list');
        const rankingInput = document.getElementById('ranking-input');
        const maxVotes = parseInt(list.dataset.maxVotes, 10) || 0;

        function ensureCutoffLine() {
            let line = list.querySelector('.cutoff-line');
            if (!line) {
                line = document.createElement('div');
                line.className = 'cutoff-line';
                line.innerHTML = '<span class="cutoff-label">↑ counts toward score &nbsp;·&nbsp; no points below ↓</span>';
                line.setAttribute('data-cutoff', '');
            }
            return line;
        }

        function updateRanksAndPoints() {
            const cards = list.querySelectorAll('.movie-card');
            cards.forEach((card, i) => {
                const points = Math.max(maxVotes - i, 0);
                card.querySelector('.rank-num').textContent = i + 1;
                card.querySelector('.rank-points').textContent = points + ' pts';
                card.classList.toggle('below-cutoff', points === 0);
            });

            const existing = list.querySelector('.cutoff-line');
            if (existing) existing.remove();

            if (maxVotes > 0 && maxVotes < cards.length) {
                const line = ensureCutoffLine();
                cards[maxVotes - 1].after(line);
            }

            rankingInput.value = JSON.stringify([...cards].map(c => c.dataset.title));
        }

        Sortable.create(list, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            filter: '.cutoff-line, .rank-action-btn, .rank-num',
            preventOnFilter: false,
            onEnd: updateRanksAndPoints,
        });

        updateRanksAndPoints();

        document.getElementById('vote-form').addEventListener('submit', updateRanksAndPoints);

        function sendCardToTop(btn) {
            const card = btn.closest('.movie-card');
            const first = list.querySelector('.movie-card');
            if (first && first !== card) {
                list.insertBefore(card, first);
                updateRanksAndPoints();
            }
        }

        function sendCardToBottom(btn) {
            const card = btn.closest('.movie-card');
            const cards = list.querySelectorAll('.movie-card');
            const last = cards[cards.length - 1];
            if (last && last !== card) {
                last.after(card);
                updateRanksAndPoints();
            }
        }

        list.addEventListener('focusin', function(e) {
            if (!e.target.classList.contains('rank-num')) return;
            const range = document.createRange();
            range.selectNodeContents(e.target);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        });

        list.addEventListener('keydown', function(e) {
            if (!e.target.classList.contains('rank-num')) return;
            if (e.key === 'Enter') {
                e.preventDefault();
                e.target.blur();
            }
        });

        list.addEventListener('focusout', function(e) {
            if (!e.target.classList.contains('rank-num')) return;
            const card = e.target.closest('.movie-card');
            const cards = [...list.querySelectorAll('.movie-card')];
            const rawPos = parseInt(e.target.textContent.trim(), 10);
            if (!isNaN(rawPos)) {
                const targetIdx = Math.max(0, Math.min(rawPos - 1, cards.length - 1));
                const currentIdx = cards.indexOf(card);
                if (currentIdx !== targetIdx) {
                    if (targetIdx < currentIdx) {
                        list.insertBefore(card, cards[targetIdx]);
                    } else {
                        cards[targetIdx].after(card);
                    }
                }
            }
            updateRanksAndPoints();
        });

        function copyUrl() {
            const url = document.getElementById('poll-url').textContent;
            navigator.clipboard.writeText(url).then(() => {
                const btn = document.querySelector('.copy-btn');
                btn.textContent = 'Copied!';
                setTimeout(() => btn.textContent = 'Copy', 2000);
            });
        }
    </script>
</body>
</html>
