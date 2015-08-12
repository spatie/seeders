<?php

namespace Spatie\Seeders\Test\SuperSeeder\Stubs;

use Spatie\Seeders\SuperSeeder\Factory;

class PersonFactory extends Factory
{
    public function __construct()
    {
        parent::__construct(Person::class);
    }

    protected function finalize($model, $data, $carry)
    {
        $model->email = strtolower($model->firstname) . '@spatie.be';

        if (!isset($model->role)) {
            $model->role = 'user';
        }

        return $model;
    }

    protected function setAdmin($model, $value)
    {
        if ($value === true) {
            $model->role = 'admin';
        }

        return $model;
    }
}
