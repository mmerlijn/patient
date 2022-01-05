<?php

namespace mmerlijn\patient\Casts;


use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Initials implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes)
    {

        return preg_replace('/([A-Z])/', '$1.', strtoupper($value));
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return preg_replace('/[^A-Z]/', '', strtoupper($value));
    }
}