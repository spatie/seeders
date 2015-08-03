<?php

namespace Spatie\Seeders\Test\Superseeder;

use PHPUnit_Framework_TestCase as TestCase;
use Spatie\Seeders\Superseeder\Factory;
use Spatie\Seeders\Test\Superseeder\Stubs\Person;
use Spatie\Seeders\Test\Superseeder\Stubs\PersonFactory;

class FactoryTest extends TestCase
{
    public function setUp()
    {
        $this->data = [
            ['firstname' => 'Sebastian', 'lastname' => 'De Deyne'],
            ['firstname' => 'Freek', 'lastname' => 'Van der Herten', 'admin' => true],
        ];
    }

    /** @test */
    public function it_is_initializable()
    {
        $factory = new Factory(Person::class);
        $this->assertInstanceOf(Factory::class, $factory);
    }

    /** @test */
    public function it_makes_a_model()
    {
        $factory = new Factory(Person::class);
        $person = $factory->make($this->data[0]);

        $this->assertInstanceOf(Person::class, $person);
    }

    /** @test */
    public function it_sets_attributes_without_custom_setters()
    {
        $factory = new PersonFactory;
        $person = $factory->make($this->data[0]);

        $this->assertEquals('Sebastian', $person->firstname);
        $this->assertEquals('De Deyne', $person->lastname);
    }

    /** @test */
    public function it_sets_attributes_with_custom_setters()
    {
        $factory = new PersonFactory;
        $person = $factory->make($this->data[1]);

        $this->assertEquals('admin', $person->role);
    }

    /** @test */
    public function it_sets_attributes_in_finalize()
    {
        $factory = new PersonFactory;
        $person = $factory->make($this->data[0]);

        $this->assertEquals('sebastian@spatie.be', $person->email);
        $this->assertEquals('user', $person->role);
    }
}
