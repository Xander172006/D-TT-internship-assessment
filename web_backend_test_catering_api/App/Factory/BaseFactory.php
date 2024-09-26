<?php

namespace App\Factory;

abstract class BaseFactory {
    protected $model;

    public function create(array $attributes = []): object {
        $attributes = array_merge($this->defaultAttributes(), $attributes);
        return new $this->model($attributes);
    }

    protected function defaultAttributes() {
        return [];
    }

    public function createMany(int $count, array $attributes = []): array {
        $instances = [];
        for ($i = 0; $i < $count; $i++) {
            $instance = $this->create($attributes);
            $instance->save();
            $instances[] = $instance;
        }
        return $instances;
    }
}
