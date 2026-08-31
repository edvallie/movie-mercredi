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
        .error a { color: #ff8a8a; }
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

        .field-block { margin-bottom: 1.5rem; }
        .field-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .scoring-row {
            display: flex;
            align-items: flex-start;
            gap: 1.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .winners-field { display: none; }
        body.is-holiday .winners-field { display: block; }
        .winners-field .hint { margin-top: 0.35rem; }
        .vote-limit { margin-top: 0; flex: 1; min-width: 220px; }
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
        .num-input {
            width: 78px;
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
        .num-input::-webkit-outer-spin-button,
        .num-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .num-input:focus { border-color: #e5b000; }
        .vote-limit-help {
            color: #555;
            font-size: 0.78rem;
            margin-top: 0.5rem;
        }

        .holiday-fields { display: none; }
        .holiday-fields.active { display: block; }
        .holiday-grid {
            display: grid;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .holiday-year-row {
            display: flex;
            align-items: flex-end;
            gap: 1.25rem;
            flex-wrap: wrap;
        }
        .check-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: #aaa;
            font-size: 0.9rem;
        }
        .check-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #e5b000;
        }
        body.is-holiday .regular-vote-toggle { display: none; }
        body.is-holiday .custom-votes-wrap { display: inline-flex; }
        #vote-limit-help-holiday { display: none; }
        body.is-holiday #vote-limit-help-regular { display: none; }
        body.is-holiday #vote-limit-help-holiday { display: block; }
    </style>
</head>
@php
    $firstHoliday = array_key_first($holidays);
    $existingSlug = request('exists');
    $isHolidayForm = old('poll_type', $existingSlug ? 'holiday' : 'regular') === 'holiday';
    $selectedTheme = old('theme', request('theme', $firstHoliday));
    $selectedYear = old('year', request('year', $currentYear));
    $selectedWinners = old('winner_count', 5);
    $holidayLabels = [];
    foreach ($holidays as $key => $holiday) {
        $holidayLabels[$key] = $holiday['label'];
    }
@endphp
<body class="{{ $isHolidayForm ? 'is-holiday' : '' }}">
    <div class="card">
        <div class="logo">🎬 Movie Mercredi</div>
        <h1>Create a Poll</h1>
        <p class="subtitle">Paste your Google Sheets movie list below. The poll link will be generated and ready to share.</p>

        @if ($errors->any() || $existingSlug)
            <div class="error" style="margin-bottom:1rem;">
                @if ($existingSlug)
                    A poll for that holiday and year already exists.
                    <a href="{{ route('poll.show', $existingSlug) }}">Open {{ str_replace('-', ' ', $existingSlug) }}</a>
                @else
                    {{ $errors->first() }}
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('poll.store') }}" id="create-form">
            @csrf
            <input type="hidden" name="poll_type" id="poll-type" value="{{ old('poll_type', $isHolidayForm ? 'holiday' : 'regular') }}">
            <input type="hidden" name="theme" id="theme-input" value="{{ $selectedTheme }}">

            <div class="field-block">
                <label>Poll Type</label>
                <div class="seg-toggle" role="tablist">
                    <button type="button" class="seg-btn poll-type-btn {{ !$isHolidayForm ? 'active' : '' }}" data-type="regular">Regular</button>
                    <button type="button" class="seg-btn poll-type-btn {{ $isHolidayForm ? 'active' : '' }}" data-type="holiday">Holiday</button>
                </div>
            </div>

            <div class="holiday-fields {{ $isHolidayForm ? 'active' : '' }}" id="holiday-fields">
                <div class="holiday-grid">
                    <div class="holiday-year-row">
                        <div>
                            <label>Holiday</label>
                            <div class="seg-toggle" role="tablist">
                                @foreach ($holidays as $key => $holiday)
                                    <button
                                        type="button"
                                        class="seg-btn theme-btn {{ $selectedTheme === $key ? 'active' : '' }}"
                                        data-theme="{{ $key }}"
                                        data-label="{{ $holiday['label'] }}"
                                    >{{ $holiday['emoji'] }} {{ $holiday['label'] }}</button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label for="year">Year</label>
                            <input
                                type="number"
                                class="num-input"
                                id="year"
                                name="year"
                                min="2015"
                                max="{{ $currentYear + 1 }}"
                                value="{{ $selectedYear }}"
                            >
                        </div>
                    </div>
                    <label class="check-row">
                        <input type="checkbox" name="closed" value="1" {{ old('closed') ? 'checked' : '' }}>
                        Past poll (close voting)
                    </label>
                </div>
            </div>

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
            <p class="hint" id="paste-hint">Copy all cells from your Google Sheet (Ctrl+A, Ctrl+C) and paste here.</p>

            <div class="scoring-row">
                <div class="winners-field">
                    <label for="winner-count">Number of winners</label>
                    <div class="field-row">
                        <input
                            type="number"
                            class="num-input"
                            id="winner-count"
                            name="winner_count"
                            min="1"
                            value="{{ $selectedWinners }}"
                        >
                    </div>
                    <p class="hint">Declared winners on results</p>
                </div>
                <div class="vote-limit">
                    <label>Max Votes Per Voter</label>
                    <div class="vote-limit-row">
                        <div class="seg-toggle regular-vote-toggle" role="tablist">
                            <button type="button" class="seg-btn vote-mode-btn {{ old('vote_limit_mode', 'half') === 'half' ? 'active' : '' }}" data-mode="half">Half (auto)</button>
                            <button type="button" class="seg-btn vote-mode-btn {{ old('vote_limit_mode') === 'custom' ? 'active' : '' }}" data-mode="custom">Custom</button>
                        </div>
                        <input type="hidden" name="vote_limit_mode" id="vote-limit-mode" value="{{ old('vote_limit_mode', 'half') }}">
                        <div class="custom-votes-wrap {{ old('vote_limit_mode') === 'custom' || old('poll_type') === 'holiday' ? 'active' : '' }}" id="custom-votes-wrap">
                            <input
                                type="number"
                                class="num-input"
                                name="max_votes"
                                id="max-votes"
                                min="1"
                                placeholder="3"
                                value="{{ old('max_votes', old('poll_type') === 'holiday' ? $selectedWinners : '') }}"
                            >
                            <span>votes</span>
                        </div>
                    </div>
                    <p class="vote-limit-help" id="vote-limit-help-regular">How many of each voter's top-ranked movies actually award points. Movies ranked below this don't count.</p>
                    <p class="vote-limit-help" id="vote-limit-help-holiday">How many of each voter's top-ranked movies actually award points. Movies ranked below this don't count.</p>
                </div>
            </div>

            <button type="submit" class="submit-btn">Create Poll &rarr;</button>
        </form>
    </div>

    <script>
        (function () {
            const holidays = @json($holidayLabels);
            const regularDefaultTitle = @json($defaultTitle);
            const typeInput = document.getElementById('poll-type');
            const themeInput = document.getElementById('theme-input');
            const holidayFields = document.getElementById('holiday-fields');
            const titleInput = document.getElementById('title');
            const yearInput = document.getElementById('year');
            const winnerInput = document.getElementById('winner-count');
            const modeInput = document.getElementById('vote-limit-mode');
            const customWrap = document.getElementById('custom-votes-wrap');
            const customInput = document.getElementById('max-votes');
            const pasteHint = document.getElementById('paste-hint');
            const voteModeBtns = document.querySelectorAll('.vote-mode-btn');

            let lastAutoTitle = titleInput.value;

            function holidayTitle() {
                const label = holidays[themeInput.value] || 'Holiday';
                return label + ' ' + yearInput.value;
            }

            function syncTitle() {
                if (typeInput.value !== 'holiday') return;
                const next = holidayTitle();
                if (titleInput.value === '' || titleInput.value === lastAutoTitle) {
                    titleInput.value = next;
                    lastAutoTitle = next;
                }
            }

            function setType(type) {
                typeInput.value = type;
                document.body.classList.toggle('is-holiday', type === 'holiday');
                holidayFields.classList.toggle('active', type === 'holiday');
                document.querySelectorAll('.poll-type-btn').forEach(b => {
                    b.classList.toggle('active', b.dataset.type === type);
                });

                if (type === 'holiday') {
                    modeInput.value = 'custom';
                    customWrap.classList.add('active');
                    if (!customInput.value) customInput.value = winnerInput.value || '5';
                    pasteHint.textContent = 'Copy the four columns: Movie Suggestion, imdb link, quick synopse, who suggested.';
                    if (titleInput.value === '' || titleInput.value === regularDefaultTitle || titleInput.value === lastAutoTitle) {
                        lastAutoTitle = holidayTitle();
                        titleInput.value = lastAutoTitle;
                    }
                } else {
                    pasteHint.textContent = 'Copy all cells from your Google Sheet (Ctrl+A, Ctrl+C) and paste here.';
                    if (titleInput.value === lastAutoTitle) {
                        titleInput.value = regularDefaultTitle;
                        lastAutoTitle = regularDefaultTitle;
                    }
                    if (modeInput.value !== 'custom') {
                        customWrap.classList.remove('active');
                    }
                }
            }

            document.querySelectorAll('.poll-type-btn').forEach(btn => {
                btn.addEventListener('click', () => setType(btn.dataset.type));
            });

            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.theme-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    themeInput.value = btn.dataset.theme;
                    syncTitle();
                });
            });

            yearInput.addEventListener('input', syncTitle);
            yearInput.addEventListener('change', syncTitle);

            winnerInput.addEventListener('input', () => {
                if (typeInput.value === 'holiday' && (!customInput.dataset.touched || customInput.value === customInput.dataset.prevWinner)) {
                    customInput.value = winnerInput.value;
                }
                customInput.dataset.prevWinner = winnerInput.value;
            });
            customInput.addEventListener('input', () => { customInput.dataset.touched = '1'; });
            customInput.dataset.prevWinner = winnerInput.value;

            voteModeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    voteModeBtns.forEach(b => b.classList.remove('active'));
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

            titleInput.addEventListener('input', () => {
                lastAutoTitle = titleInput.value === holidayTitle() || titleInput.value === regularDefaultTitle
                    ? titleInput.value
                    : lastAutoTitle;
            });

            if (typeInput.value === 'holiday') {
                setType('holiday');
            }
        })();
    </script>
</body>
</html>
