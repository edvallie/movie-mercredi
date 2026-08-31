<?php

namespace Tests\Unit;

use App\Services\HolidayTsvParser;
use PHPUnit\Framework\TestCase;

class HolidayTsvParserTest extends TestCase
{
    private HolidayTsvParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new HolidayTsvParser();
    }

    public function test_skips_header_row_and_maps_four_columns(): void
    {
        $tsv = "Movie Suggestion\timdb link\tquick synopse\twho suggested\n"
            . "The Thing\thttps://www.imdb.com/title/tt0084787/\tAntarctic horror\tAlex\n"
            . "Halloween\thttps://www.imdb.com/title/tt0077655/\tBabysitter vs Myers\tSam\n";

        $movies = $this->parser->parse($tsv);

        $this->assertCount(2, $movies);
        $this->assertSame('The Thing', $movies[0]['title']);
        $this->assertSame('https://www.imdb.com/title/tt0084787/', $movies[0]['imdb_link']);
        $this->assertSame('Antarctic horror', $movies[0]['synopsis']);
        $this->assertSame('Alex', $movies[0]['suggested_by']);
        $this->assertSame('', $movies[0]['date_added']);
        $this->assertSame('', $movies[0]['watched']);
        $this->assertSame('Halloween', $movies[1]['title']);
        $this->assertSame('Sam', $movies[1]['suggested_by']);
    }

    public function test_skips_rows_without_a_url_in_the_second_column(): void
    {
        $tsv = "Not a movie\tno-link-here\tsynopsis\tPat\n"
            . "Real Movie\thttps://letterboxd.com/film/real/\tA plot\tPat\n";

        $movies = $this->parser->parse($tsv);

        $this->assertCount(1, $movies);
        $this->assertSame('Real Movie', $movies[0]['title']);
    }

    public function test_empty_paste_returns_no_movies(): void
    {
        $this->assertSame([], $this->parser->parse(''));
        $this->assertSame([], $this->parser->parse("Movie Suggestion\timdb link\tquick synopse\twho suggested\n"));
    }

    public function test_skips_short_rows(): void
    {
        $tsv = "Only two\thttps://www.imdb.com/title/tt0084787/\n"
            . "Full Row\thttps://www.imdb.com/title/tt0077655/\tSynopsis here\tJordan\n";

        $movies = $this->parser->parse($tsv);

        $this->assertCount(1, $movies);
        $this->assertSame('Full Row', $movies[0]['title']);
        $this->assertSame('Jordan', $movies[0]['suggested_by']);
    }
}
