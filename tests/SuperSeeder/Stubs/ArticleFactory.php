<?php

namespace Spatie\Seeders\Test\Superseeder\Stubs;

use Spatie\Seeders\Superseeder\Factory;

class ArticleFactory extends Factory
{
    public function __construct()
    {
        parent::__construct(Article::class);
    }

    public function isModel($data)
    {
        return isset($data['name']);
    }

    protected function setTags($model, $value)
    {
        $tags = explode(',', $value);
        $model->tags = $tags;

        return $model;
    }

    protected function finalize($model, $data, $carry)
    {
        $model->category = $carry[0];
        $model->source = $carry[1];

        return $model;
    }
}
