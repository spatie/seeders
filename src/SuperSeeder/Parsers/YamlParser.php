<?php

namespace Spatie\Seeders\Superseeder\Parsers;

use Symfony\Component\Yaml\Yaml;

class YamlParser implements Parser
{
    public function __construct()
    {
        $this->yaml = new Yaml;
    }

    /**
     * Parse a yaml string to an associative array.
     *
     * @param $data
     * @return array
     */
    public function parse($data)
    {
        return $this->yaml->parse($data);
    }
}
