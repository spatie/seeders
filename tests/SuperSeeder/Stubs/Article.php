<?php

namespace Spatie\Seeders\Test\Superseeder\Stubs;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function save(array $options = [])
    {
        return true;
    }
}
