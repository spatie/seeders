<?php

use Spatie\Seeders\Faker;

function faker() : Faker
{
    return app(Faker::class);
}
