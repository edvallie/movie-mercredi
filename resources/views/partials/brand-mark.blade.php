@php
    $brandMark = '<span class="logo-emoji">' . e($logoEmoji) . '</span>';
    if (($poll['theme'] ?? '') === 'christmas') {
        $letterIndex = 0;
        foreach (str_split('Movie Mercredi') as $ch) {
            if ($ch === ' ') {
                $brandMark .= '<span class="logo-space">&nbsp;</span>';
                continue;
            }
            $cls = $letterIndex % 2 === 0 ? 'logo-red' : 'logo-green';
            $brandMark .= '<span class="logo-letter ' . $cls . '">' . e($ch) . '</span>';
            $letterIndex++;
        }
    } else {
        $brandMark .= ' Movie Mercredi';
    }
@endphp
{!! $brandMark !!}
