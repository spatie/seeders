<?php

namespace Spatie\Seeders;

use App\Models\String;
use App\Models\Translations\StringTranslation;
use Cache;

class StringSeeder extends DatabaseSeeder
{
    public function run()
    {
        $this->truncate((new StringTranslation)->getTable(), (new String)->getTable());

        $this->superSeeder(new StringFactory(String::class), 'strings');

        Cache::flush();
    }
}
