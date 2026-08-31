@php
    $themeKey = $poll['theme'] ?? null;
    $holiday = $themeKey ? (config('holidays')[$themeKey] ?? null) : null;
    $css = array_merge([
        'accent' => '#e5b000',
        'accent_hover' => '#ffc20e',
        'bg' => '#0f0f13',
        'card' => '#1a1a24',
        'border' => '#2e2e42',
        'rank_bg' => '#13131b',
        'cover_bg' => '#111118',
        'on_accent' => '#0f0f13',
        'hover_border' => '#3e3e58',
        'gold' => '#e5b000',
        'btn_bg' => '#e5b000',
        'btn_hover' => '#ffc20e',
    ], $holiday['css'] ?? []);
    $logoEmoji = $holiday['emoji'] ?? '🎬';
@endphp
<style>
    :root {
        --accent: {{ $css['accent'] }};
        --accent-hover: {{ $css['accent_hover'] }};
        --bg: {{ $css['bg'] }};
        --card: {{ $css['card'] }};
        --border: {{ $css['border'] }};
        --rank-bg: {{ $css['rank_bg'] }};
        --cover-bg: {{ $css['cover_bg'] }};
        --on-accent: {{ $css['on_accent'] }};
        --hover-border: {{ $css['hover_border'] }};
        --gold: {{ $css['gold'] }};
        --btn-bg: {{ $css['btn_bg'] }};
        --btn-hover: {{ $css['btn_hover'] }};
    }

    body[data-theme="christmas"] {
        background-color: var(--bg);
        background-image:
            radial-gradient(ellipse 90% 50% at 50% -20%, rgba(180, 20, 40, 0.22), transparent 55%),
            radial-gradient(ellipse 55% 40% at 0% 100%, rgba(18, 110, 62, 0.38), transparent 60%),
            radial-gradient(ellipse 45% 35% at 100% 85%, rgba(232, 197, 71, 0.1), transparent 55%);
    }
    body[data-theme="christmas"] .logo {
        color: var(--accent);
        text-shadow: none;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    body[data-theme="christmas"] .logo .logo-red { color: #e11d2e; }
    body[data-theme="christmas"] .logo .logo-green { color: #16a34a; }
    body[data-theme="christmas"] .movie-card,
    body[data-theme="christmas"] .score-card {
        border-width: 2px;
        border-color: #6b1219;
        box-shadow: none;
    }
    body[data-theme="christmas"] .vote-form,
    body[data-theme="christmas"] .share-box {
        border-color: var(--border);
        box-shadow: inset 0 1px 0 color-mix(in srgb, var(--gold) 16%, transparent);
    }
    body[data-theme="christmas"] .movie-card:hover,
    body[data-theme="christmas"] .score-card:hover {
        border-color: var(--hover-border);
    }
    body[data-theme="christmas"] .movie-title a:hover,
    body[data-theme="christmas"] .movie-title:hover a,
    body[data-theme="christmas"] .movie-title:hover {
        color: var(--gold);
    }
    body[data-theme="christmas"] .vote-limit-note {
        background: color-mix(in srgb, var(--accent) 14%, transparent);
        border-color: var(--border);
    }
    body[data-theme="christmas"] .score-card.is-winner,
    body[data-theme="christmas"] .score-card.rank-1 {
        border-color: var(--accent);
        box-shadow: 0 0 0 1px color-mix(in srgb, var(--accent) 35%, transparent);
    }
    body[data-theme="christmas"] .is-winner .score-position,
    body[data-theme="christmas"] .rank-1 .score-position,
    body[data-theme="christmas"] .rank-1 .score-points,
    body[data-theme="christmas"] .history-table td.pos-1 {
        color: var(--accent);
    }
    body[data-theme="christmas"] .is-winner .score-bar,
    body[data-theme="christmas"] .rank-1 .score-bar {
        background: var(--accent);
    }
    body[data-theme="christmas"] .badge-winner {
        background: color-mix(in srgb, var(--accent) 18%, transparent);
        color: var(--accent);
        border-color: color-mix(in srgb, var(--accent) 45%, transparent);
    }

    body[data-theme="halloween"] {
        background-color: var(--bg);
        background-image:
            radial-gradient(ellipse 90% 50% at 50% -20%, rgba(249, 115, 22, 0.16), transparent 55%),
            radial-gradient(ellipse 50% 40% at 100% 100%, rgba(88, 28, 135, 0.28), transparent 60%);
    }

    body[data-theme] .brand {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
</style>
