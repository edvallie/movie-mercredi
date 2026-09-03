<?php

namespace App\Services;

class HolidayTsvParser
{
    private const HEADER_TITLES = [
        'movie suggestion',
        'title',
        'movie',
        'movie title',
    ];

    public function parse(string $raw): array
    {
        $lines = explode("\n", str_replace("\r", '', $raw));
        $movies = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $fields = explode("\t", $line);
            if (count($fields) < 4) {
                continue;
            }

            $title = trim($fields[0]);
            $imdbLink = trim($fields[1]);
            $synopsis = trim($fields[2]);
            $suggestedBy = trim($fields[3]);

            if ($title === '' || $this->isHeaderTitle($title) || !$this->looksLikeUrl($imdbLink)) {
                continue;
            }

            $movies[] = [
                'watched'      => '',
                'date_added'   => '',
                'title'        => $title,
                'imdb_link'    => $imdbLink,
                'synopsis'     => $synopsis,
                'suggested_by' => $suggestedBy,
            ];
        }

        return $movies;
    }

    private function isHeaderTitle(string $title): bool
    {
        return in_array(strtolower($title), self::HEADER_TITLES, true);
    }

    private function looksLikeUrl(string $value): bool
    {
        return (bool) preg_match('#^https?://#i', $value);
    }
}
