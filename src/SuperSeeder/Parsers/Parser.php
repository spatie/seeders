<?php

namespace Spatie\Seeders\SuperSeeder\Parsers;

interface Parser
{
    /**
     * Parse a raw data to an associative array
     *
     * @param $data
     * @return array
     */
    public function parse($data);
}
