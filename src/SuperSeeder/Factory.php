<?php

namespace Spatie\Seeders\SuperSeeder;

class Factory
{
    /**
     * @var string
     */
    protected $model;

    /**
     * @param string $model  The model's classname
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Make a model from data raw data
     *
     * @param $data
     * @param array $carry
     * @return void
     */
    public function make($data, $carry = [])
    {
        $model = new $this->model;

        $this->initialize($model, $data, $carry);

        if (is_array($data)) {
            foreach ($data as $subject => $value) {
                $setter = 'set' . ucfirst($subject);
                $this->$setter($model, $value, $data, $carry);
            }
        }

        $this->finalize($model, $data, $carry);

        $this->save($model);

        return $model;
    }

    /**
     * Determine if a set of data can be transformed into a model.
     *
     * @param array $data
     * @return bool
     */
    public function isModel($data)
    {
        return true;
    }

    /**
     * Hook to apply initial transformations to the model.
     *
     * @param $model
     * @param $data
     * @param $carry
     * @return void
     */
    protected function initialize($model, $data, $carry)
    {
        return;
    }

    /**
     * Hook to apply final transformations to the model.
     *
     * @param $model
     * @param $data
     * @param $carry
     * @return void
     */
    protected function finalize($model, $data, $carry)
    {
        return;
    }

    /**
     * Save a model. You can overwrite this method if you don't want your models to be immediately
     * saved to the database.
     * @param $model
     * @return void
     */
    protected function save($model)
    {
        $model->save();
    }

    /**
     * Magically fill the models with paramaters that map one to one to the model's attributes.
     *
     * @param $method
     * @param $parameters
     * @return void
     */
    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'set') {
            $model = $parameters[0];
            $attribute = lcfirst(substr($method, 3));
            $value = $parameters[1];
            $model->setAttribute($attribute, $value);
        }
    }
}
