<?php

namespace Spatie\Seeders\Test\SuperSeeder\Stubs;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function save(array $options = [])
    {
        return true;
    }
}
