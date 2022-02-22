<?php

namespace MennenOnline\LaravelResponseModels\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonSerializable;
use ReturnTypeWillChange;
use Stringable;
use TypeError;

abstract class BaseModel implements Arrayable, JsonSerializable, Stringable
{
    protected array $attributes = [];

    /*
     * Define your Response Conversion below in following Scheme:
     *
     * responseField => model_field
     *
     * e.g.
     *
     * myResponseField => my_response_field
     */
    protected array $fieldMap = [];

    protected array $removeAfterFill = [];

    protected array $casts = [];

    public function __construct(array|object $attributes = []) {
        if(is_object($attributes)) {
            $attributes = (array)$attributes;
        }

        if (!empty($attributes)) {
            $this->fill($attributes);
        }
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
        collect($this->fieldMap)->each(function($modelField, $responseKey) use($attributes) {
            $content = Arr::get($attributes, $responseKey);
            $this->attributes = Arr::add($this->attributes, $modelField, $content);
        })->toArray();
        $this->attributes = $this->searchForNotSnakeKeys($this->attributes);
    }

    public function __get(string $name) {
        $methodName = 'get-'.Str::camel($name).'-attribute';
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        } else {
            return $this->attributes[$name] ?? null;
        }
    }

    public function __set(string $name, $value): void {
        $methodName = 'set-'.Str::camel($name).'-attribute';
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

    #[ReturnTypeWillChange] public function jsonSerialize() {
        return serialize($this->attributes);
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