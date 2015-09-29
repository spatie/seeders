<?php

use PHPUnit_Framework_TestCase as TestCase;
use Spatie\Seeders\SuperSeeder\Parsers\YamlParser;

class YamlParserTest extends TestCase
{
    /** @test */
    public function it_parses_yaml()
    {
        $yaml = '{ foo: bar, baz: qux }';
        $parser = new YamlParser();

        $data = $parser->parse($yaml);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $data);
    }
}
