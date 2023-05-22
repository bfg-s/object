<?php

if (! function_exists('gag')) {
    /**
     * @param  string|null  $name
     * @param ...$arguments
     * @return mixed
     */
    function gag(string $name = null, ...$arguments): mixed
    {
        return \Bfg\Object\Gag::create($name, ...$arguments);
    }
}

if (! function_exists('pipeline')) {
    /**
     * The organization of pipeline is the same as
     * it is implemented by middleware Laravel.
     *
     * @param $send
     * @param  array  $pipes
     * @return mixed|$send
     */
    function pipeline($send, array $pipes): mixed
    {
        return app(\Illuminate\Pipeline\Pipeline::class)
            ->send($send)
            ->through($pipes)
            ->thenReturn();
    }
}

if (! function_exists('is_call')) {
    /**
     * When this is called an item.
     *
     * @param  mixed  $subject
     * @return bool
     */
    function is_call(mixed $subject): bool
    {
        return is_array($subject) && is_callable($subject) || $subject instanceof Closure;
    }
}

if (! function_exists('is_assoc')) {
    /**
     * Check whether an array is associative.
     *
     * @param  array  $arr
     * @return bool
     */
    function is_assoc(array $arr): bool
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}

if (! function_exists('array_merge_recursive_distinct')) {
    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):.
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param  array  $array1
     * @param  array  $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    function array_merge_recursive_distinct(array &$array1, array &$array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}

if (! function_exists('array_dots_uncollapse')) {
    /**
     * Expand an array folded into a dot array.
     *
     * @param  array  $array
     * @return array
     */
    function array_dots_uncollapse(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            \Illuminate\Support\Arr::set($result, $key, $value);
        }

        return $result;
    }
}

if (! function_exists('multi_dot_call')) {
    /**
     * Access to an object or/and an array using the dot path method.
     *
     * @param $obj
     * @param  string  $dot_path
     * @param  bool  $locale
     * @return mixed
     */
    function multi_dot_call($obj, string $dot_path, bool $locale = true): mixed
    {
        return \Bfg\Object\Accessor::create($obj)->dotCall($dot_path, $locale);
    }
}

if (! function_exists('eloquent_instruction')) {
    /**
     * [ ==|=|is  (VALUE)] = where('name', '=', 'value')
     * [ <=       (VALUE)] = where('name', '<=', 'value')
     * [ >=       (VALUE)] = where('name', '>=', 'value')
     * [ <        (VALUE)] = where('name', '<', 'value')
     * [ >        (VALUE)] = where('name', '>', 'value')
     * [ !=|not   (VALUE)] = where('name', '!=', 'value')
     * [ %%|like  (VALUE)] = where('name', 'like', '%value%')
     * [ %|%like  (VALUE)] = where('name', 'like', '%value')
     * [ !%|like% (VALUE)] = where('name', 'like', 'value%')
     * [ in       (VALUE)] = whereIn('name', explode(';', 'value;value...'))
     * [ not in   (VALUE)] = whereNotIn('name', explode(';', 'value;value...'))
     * [ not null (VALUE)] = whereNotNull('name')
     * [ null     (VALUE)] = whereNull('name').
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Builder|array|string  $eloquent
     * @param  array  $instructions
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation
     */
    function eloquent_instruction(
        Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Builder|array|string $eloquent,
        array $instructions
    ): Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Builder {
        return \Bfg\Object\Accessor::create($eloquent)->eloquentInstruction($instructions);
    }
}
