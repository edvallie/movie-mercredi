<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movie Mercredi — Create Poll</title>
    <style>
        *, ::before, ::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f0f13;
            color: #e2e2e2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #1a1a24;
            border: 1px solid #2e2e42;
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.5);
        }
        .logo {
            font-size: 1.1rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #e5b000;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        input[type="text"] {
            width: 100%;
            background: #0f0f13;
            border: 1px solid #2e2e42;
            border-radius: 8px;
            color: #e2e2e2;
            font-family: inherit;
            font-size: 0.95rem;
            padding: 0.65rem 1rem;
            outline: none;
            transition: border-color 0.2s;
            margin-bottom: 1.5rem;
        }
        input[type="text"]:focus { border-color: #e5b000; }
        textarea {
            width: 100%;
            height: 220px;
            background: #0f0f13;
            border: 1px solid #2e2e42;
            border-radius: 8px;
            color: #e2e2e2;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            padding: 0.75rem 1rem;
            resize: vertical;
            outline: none;
            transition: border-color 0.2s;
        }
        textarea:focus { border-color: #e5b000; }
        .error {
            color: #ff6b6b;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        .hint {
            color: #555;
            font-size: 0.78rem;
            margin-top: 0.5rem;
        }
        .submit-btn {
            margin-top: 1.5rem;
            width: 100%;
            background: #e5b000;
            color: #0f0f13;
            border: none;
            border-radius: 8px;
            padding: 0.85rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.03em;
            transition: background 0.2s, transform 0.1s;
        }
        .submit-btn:hover { background: #ffc20e; }
        .submit-btn:active { transform: scale(0.98); }

        .vote-limit { margin-top: 1.5rem; }
        .vote-limit-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .seg-toggle {
            display: inline-flex;
            background: #0f0f13;
            border: 1px solid #2e2e42;
            border-radius: 8px;
            padding: 4px;
            gap: 2px;
        }
        .seg-btn {
            background: transparent;
            border: none;
            color: #888;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            transition: background 0.18s, color 0.18s;
            font-family: inherit;
        }
        .seg-btn:hover:not(.active) { color: #fff; }
        .seg-btn.active {
            background: #e5b000;
            color: #0f0f13;
        }
        .custom-votes-wrap {
            display: none;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            font-size: 0.85rem;
        }
        .custom-votes-wrap.active { display: inline-flex; }
        .custom-votes-wrap input[type="number"] {
            width: 70px;
            background: #0f0f13;
            border: 1px solid #2e2e42;
            border-radius: 6px;
            color: #e2e2e2;
            font-size: 0.9rem;
            padding: 0.45rem 0.6rem;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
            -moz-appearance: textfield;
        }
        .custom-votes-wrap input[type="number"]::-webkit-outer-spin-button,
        .custom-votes-wrap input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .custom-votes-wrap input[type="number"]:focus { border-color: #e5b000; }
        .vote-limit-help {
            color: #555;
            font-size: 0.78rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">🎬 Movie Mercredi</div>
        <h1>Create a Poll</h1>
        <p class="subtitle">Paste your Google Sheets movie list below. The poll link will be generated and ready to share.</p>

        @if ($errors->any())
            <div class="error" style="margin-bottom:1rem;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('poll.store') }}">
            @csrf
            <label for="title">Poll Title</label>
            <input
                type="text"
                id="title"
                name="title"
                placeholder="e.g. 6/4 Poll"
                value="{{ old('title', $defaultTitle) }}"
                maxlength="100"
            >

            <label for="input">Paste Spreadsheet Data</label>
            <textarea
                id="input"
                name="input"
                placeholder="Paste your tab-separated movie list here (including header row)..."
                autofocus
            >{{ old('input') }}</textarea>
            <p class="hint">Copy all cells from your Google Sheet (Ctrl+A, Ctrl+C) and paste here.</p>

            <div class="vote-limit">
                <label>Max Votes Per Voter</label>
                <div class="vote-limit-row">
                    <div class="seg-toggle" role="tablist">
                        <button type="button" class="seg-btn {{ old('vote_limit_mode', 'half') === 'half' ? 'active' : '' }}" data-mode="half">Half (auto)</button>
                        <button type="button" class="seg-btn {{ old('vote_limit_mode') === 'custom' ? 'active' : '' }}" data-mode="custom">Custom</button>
                    </div>
                    <input type="hidden" name="vote_limit_mode" id="vote-limit-mode" value="{{ old('vote_limit_mode', 'half') }}">
                    <div class="custom-votes-wrap {{ old('vote_limit_mode') === 'custom' ? 'active' : '' }}" id="custom-votes-wrap">
                        <input
                            type="number"
                            name="max_votes"
                            id="max-votes"
                            min="1"
                            placeholder="3"
                            value="{{ old('max_votes') }}"
                        >
                        <span>votes</span>
                    </div>
                </div>
                <p class="vote-limit-help">How many of each voter's top-ranked movies actually award points. Movies ranked below this don't count.</p>
            </div>

            <button type="submit" class="submit-btn">Create Poll &rarr;</button>
        </form>
    </div>

    <script>
        (function () {
            const btns = document.querySelectorAll('.seg-btn');
            const modeInput = document.getElementById('vote-limit-mode');
            const customWrap = document.getElementById('custom-votes-wrap');
            const customInput = document.getElementById('max-votes');

            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    btns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const mode = btn.dataset.mode;
                    modeInput.value = mode;
                    if (mode === 'custom') {
                        customWrap.classList.add('active');
                        customInput.focus();
                    } else {
                        customWrap.classList.remove('active');
                    }
                });
            });
        })();
    </script>
</body>
</html>
