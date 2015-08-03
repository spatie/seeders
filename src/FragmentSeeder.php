<?php

namespace Spatie\Seeders;

use App\Models\Fragment;
use App\Models\Translations\FragmentTranslation;
use Cache;

class FragmentSeeder extends DatabaseSeeder
{
    public function run()
    {
        $this->truncate((new FragmentTranslation())->getTable(), (new Fragment())->getTable());

        $this->superSeeder(new FragmentFactory(Fragment::class), 'fragments');

        Cache::flush();
    }
}
