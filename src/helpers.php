<?php

use Spatie\Seeders\Faker;

/** @return \Faker\Generator|Spatie\Seeders\Faker */
function faker(): Faker
{
    return app(Faker::class);
}
