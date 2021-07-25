# Extension object

To work with objects and arrays.

## Collection
Adds to the collection of a `paginate` method, 
convenient to create a paginator from the collection.
```php
collect([])->paginate($perPage = 15, $pageName = 'page', $page = null);
```

## Helpers

### pipeline
The organization of pipeline is the same as it is implemented by middleware Laravel.
```php
pipeline($send, array $pipes);
```

### is_call
When this is called an item.
```php
is_call(mixed $subject);
```

### is_assoc
Check whether an array is associative.
```php
is_assoc(array $arr);
```

### array_merge_recursive_distinct
`array_merge_recursive` does indeed merge arrays, but it converts values with duplicate
keys to arrays rather than overwriting the value in the first array with the duplicate
value in the second array, as array_merge does. I.e., with array_merge_recursive,
this happens (documented behavior):
```php
array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     => array('key' => array('org value', 'new value'));
```
`array_merge_recursive_distinct` does not change the datatypes of the values in the arrays.
Matching keys' values in the second array overwrite those in the first array, as is the
case with array_merge, i.e.:
```php
array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     => array('key' => array('new value'));
```
Parameters are passed by reference, though only for performance reasons. They're not
altered by this function.

### array_dots_uncollapse
Expand an array folded into a dot array.
```php
array_dots_uncollapse(array $array);
```

### multi_dot_call
Access to an object or/and an array using the dot path method
```php
multi_dot_call($obj, string $dot_path, bool $locale = true);
```
