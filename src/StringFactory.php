<?php

namespace Spatie\Seeders;

use Spatie\SuperSeeder\Factory;

class StringFactory extends Factory
{
    public function isModel($data)
    {
        $hasDescription = isset($data['desc']);
        $hasContents = isset($data['text']) || isset($data['html']) || isset($data['name']);

        return $hasDescription && $hasContents;
    }
    
    protected function initialize($model, $data, $carry)
    {
        $model->name = implode('.', $carry);
        $model->contains_html = false;
        $model->hide_string = false;
        $model->draft = false;
    }

    protected function setDesc($model, $value)
    {
        $model->description = $value;
    }

    protected function setHtml($model, $value)
    {
        $model->contains_html = true;
        $this->setText($model, $value);
    }

    protected function setName($model, $value)
    {
        $values = [];

        foreach (config('app.locales') as $locale) {
            $values[$locale] = $value;
        }
        
        $this->setText($model, $values);
    }

    protected function setText($model, $value)
    {
        $locales = array_fill_keys(config('app.locales'), null);

        if (is_array($value)) {
            $translations = array_merge($locales, $value);
            $defaultTranslation = $value[config('app.locale')];
        } else {
            $translations = array_merge($locales, [config('app.locale') => $value]);
            $defaultTranslation = $value;
        }

        foreach($translations as $locale => $translation) {
            $model->translate($locale)->text = $translation ?: "$defaultTranslation $locale";
        }
    }

    protected function setHide($model, $value)
    {
        $model->hide_string = $value;
    }
}
