<?php

namespace Spatie\Seeders\Test\SuperSeeder;

use PHPUnit_Framework_TestCase as TestCase;
use Spatie\Seeders\SuperSeeder\Factory;
use Spatie\Seeders\SuperSeeder\Parsers\YamlParser;
use Spatie\Seeders\SuperSeeder\SuperSeeder;
use Spatie\Seeders\Test\SuperSeeder\Stubs\ArticleFactory;
use Spatie\Seeders\Test\SuperSeeder\Stubs\Person;
use Spatie\Seeders\Test\SuperSeeder\Stubs\PersonFactory;

class SuperSeederTest extends TestCase
{
    /** @test */
    public function it_is_initializable()
    {
        $superSeeder = new SuperSeeder(new PersonFactory, new YamlParser);

        $this->assertInstanceOf(SuperSeeder::class, $superSeeder);
    }

    /** @test */
    public function it_seeds_from_raw_data()
    {
        $superSeeder = new SuperSeeder(new PersonFactory);

        $data = [
            ['firstname' => 'Sebastian', 'lastname' => 'De Deyne'],
            ['firstname' => 'Freek', 'lastname' => 'Van der Herten', 'admin' => true],
        ];

        $seeded = $superSeeder->seed($data);

        $this->assertInstanceOf(Person::class, $seeded[0]);
        $this->assertInstanceOf(Person::class, $seeded[1]);
        $this->assertCount(2, $seeded);
    }

    /** @test */
    public function it_seeds_from_a_file()
    {
        $superSeeder = new SuperSeeder(new PersonFactory);
        $seeded = $superSeeder->seedFromFile(__DIR__.'/fixtures/people.yaml', new YamlParser);

        $this->assertInstanceOf(Person::class, $seeded[0]);
        $this->assertInstanceOf(Person::class, $seeded[1]);
        $this->assertCount(2, $seeded);
    }

    public function it_seeds_from_a_directory()
    {
        
    }

    /** @test */
    public function it_seeds_recursively_and_carries_values_throughout()
    {
        $superSeeder = new SuperSeeder(new ArticleFactory);
        $seeded = $superSeeder->seedFromFile(__DIR__.'/fixtures/articles.yaml', new YamlParser);

        $this->assertCount(4, $seeded);
        $this->assertEquals('tech', $seeded[0]->category);
        $this->assertEquals('tech', $seeded[1]->category);
        $this->assertEquals('tech', $seeded[2]->category);
        $this->assertEquals('tech', $seeded[3]->category);
        $this->assertEquals('hackernews', $seeded[0]->source);
        $this->assertEquals('hackernews', $seeded[1]->source);
        $this->assertEquals('designernews', $seeded[2]->source);
        $this->assertEquals('designernews', $seeded[3]->source);
    }
}
