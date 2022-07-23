<?php

namespace MennenOnline\LaravelResponseModels\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonSerializable;
use ReturnTypeWillChange;
use Stringable;
use TypeError;

abstract class BaseModel extends Model implements Arrayable, JsonSerializable, Stringable
{
    /*
     * Define your Response Conversion below in following Scheme:
     *
     * responseField => model_field
     *
     * e.g.
     *
     * myResponseField => my_response_field
     *
     * If a Field doesnt need to be remapped, simply fill it like key and not like above key = value
     *
     * id
     *
     *
     */
    protected array $fieldMap = [];

    protected array $removeAfterFill = [];

    public function __construct(array|object $attributes = []) {
        if(is_object($attributes)) {
            $attributes = (array)$attributes;
        }

        parent::__construct($attributes);

        if (!empty($attributes)) {
            $this->fill($attributes);
        }
    }

    public function __call($method, $parameters) {
        if(!method_exists($this, $method)) {
            $attributeName = str($method)->replace('get', '')->replace('Attribute', '')->snake()->replace('_', '.')->toString();
            return Arr::get($this->attributes, $attributeName) ?? $this->__get($attributeName);
        }

        return $this->$method($parameters);
    }

    public static function convertToArrayRecursive(array|object $object): array {
        if(is_object($object)) {
            $object = (array)$object;
        }

        $array = [];

        foreach($object as $key => $value) {
            $array[$key] = (is_array($value) || is_object($value)) ? self::convertToArrayRecursive($value) : $value;
        }
        return $array;
    }

    public function fill(array $attributes = []) {
        $attributes = self::convertToArrayRecursive($attributes);

        $attributes = $this->searchForNotSnakeKeys($attributes);

        $this->attributes = $attributes;
    }

    public function __get($name) {
        if(str($name)->contains('.')) {
            return Arr::get($this->attributes, $name);
        }
        return $this->searchAndGetKey($this->attributes, $name);
    }

    private function searchAndGetKey(array $array, string $key) {
        if(Arr::has($array, $key)) {
            return $array[$key];
        }

        foreach($array as $subKey => $subValue) {
            if(is_array($array[$subKey]) && $result = $this->searchAndGetKey($subValue, $key)) {
                return $result;
            }
        }

        return false;
    }

    public function __set($name, $value): void {
        $methodName = Str::camel('set-'.$name.'-attribute');
        if (method_exists($this, $methodName)) {
            $this->$methodName($value);
        } else {
            $this->attributes[$name] = $value;
        }
    }

    public function toArray(): array {
        return $this->attributes;
    }

    public function __toString(): string {
        return json_encode($this->attributes);
    }

    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * @param  array  $attributes
     * @return array
     */
    private function searchForNotSnakeKeys(array $attributes): array {
        $values = array_values($attributes);
        $keys = array_keys($attributes);
        foreach($keys as $index => $key) {
            $keys[$index] = str($key)->snake()->toString();
        }
        $attributes = array_combine($keys, $values);

        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                $attributes[$key] = $this->searchForNotSnakeKeys($value);
            }
        }
        return $attributes;
    }
}