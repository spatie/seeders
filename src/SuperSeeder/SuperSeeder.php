<?php

namespace Spatie\Seeders\Superseeder;

use Spatie\Seeders\Superseeder\Parsers\Parser;

class SuperSeeder
{
    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Seed models from a set of data.
     * If no parser is provided, it is assumed that $data is an associative array.
     *
     * @param $data
     * @param Parser|null $parser
     * @return array
     */
    public function seed($data, Parser $parser = null)
    {
        if (isset($parser)) {
            $data = $parser->parse($data);
        }

        return $this->makeModels($data);
    }

    /**
     * Retrieve the contents of a file and seed with it's contents.
     * If no parser is provided, it is assumed that $data is an associative array.
     *
     * @param $filename
     * @param Parser|null $parser
     * @return array
     */
    public function seedFromFile($filename, Parser $parser = null)
    {
        $data = file_get_contents($filename);

        return $this->seed($data, $parser);
    }

    /**
     * Recursively traverse a set of data and make a model of it if it contains the correct data for a valid model.
     *
     * @param $data
     * @param array $carry  Used to carry data around recursive calls of the function
     * @return array
     */
    protected function makeModels($data, $carry = [])
    {
        $models = [];

        if (! is_array($data)) {
            return $models;
        }

        foreach ($data as $key => $node) {
            $_carry = $carry; 

            $_carry[] = $key;

            if ($this->factory->isModel($node)) {
                $models[] = $this->factory->make($node, $_carry);
                continue;
            }

            $models = array_merge($models, $this->makeModels($node, $_carry));
        }

        return $models;
    }
}
