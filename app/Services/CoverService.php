<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CoverService
{
    public function __construct(private DynamoDbService $dynamo) {}

    public function attachCovers(array $movies): array
    {
        return array_map(function (array $movie) {
            $movie['cover_url'] = $this->resolveCover($movie['imdb_link'] ?? '');
            return $movie;
        }, $movies);
    }

    private function resolveCover(string $url): ?string
    {
        if ($imdbId = $this->extractImdbId($url)) {
            return $this->fetchCover("imdb:{$imdbId}", fn() => $this->fetchFromTmdbByImdbId($imdbId));
        }

        if ($slug = $this->extractLetterboxdSlug($url)) {
            return $this->fetchCover("lb:{$slug}", fn() => $this->fetchFromLetterboxd($slug));
        }

        return null;
    }

    private function fetchCover(string $cacheKey, callable $fetcher): ?string
    {
        $cached = $this->dynamo->getCover($cacheKey);
        if ($cached !== null) {
            return $cached === '' ? null : $cached;
        }

        $posterUrl = $fetcher();
        $this->dynamo->putCover($cacheKey, $posterUrl ?? '');
        return $posterUrl;
    }

    private function extractImdbId(string $url): ?string
    {
        if (preg_match('#imdb\.com/title/(tt\d+)#', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    private function extractLetterboxdSlug(string $url): ?string
    {
        if (preg_match('#letterboxd\.com/film/([^/?]+)#', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    private function fetchFromTmdbByImdbId(string $imdbId): ?string
    {
        $response = Http::timeout(5)->get("https://api.themoviedb.org/3/find/{$imdbId}", [
            'api_key' => config('services.tmdb.key'),
            'external_source' => 'imdb_id',
        ]);

        if (!$response->ok()) {
            return null;
        }

        $posterPath = $response->json('movie_results.0.poster_path');
        return $posterPath ? "https://image.tmdb.org/t/p/w300{$posterPath}" : null;
    }

    private function fetchFromLetterboxd(string $slug): ?string
    {
        $page = Http::timeout(10)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; MovieMercredi/1.0)'])
            ->get("https://letterboxd.com/film/{$slug}/");

        if (!$page->ok()) {
            return null;
        }

        if (!preg_match('#themoviedb\.org/movie/(\d+)#', $page->body(), $m)) {
            return null;
        }

        $tmdbId = $m[1];

        $response = Http::timeout(5)->get("https://api.themoviedb.org/3/movie/{$tmdbId}", [
            'api_key' => config('services.tmdb.key'),
        ]);

        if (!$response->ok()) {
            return null;
        }

        $posterPath = $response->json('poster_path');
        return $posterPath ? "https://image.tmdb.org/t/p/w300{$posterPath}" : null;
    }
}
