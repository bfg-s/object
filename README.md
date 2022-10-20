# Extension object

To work with objects and arrays.

## GAG Object
The Gag object is designed for recursive data filling and the construction 
of circuits and its children from the repartition that can be automatically 
filling. Standard Gag Object connects all classes in the `app/Gags` folder, 
all the classes are parsed there automatically fall into the Gag storage.

By default, there is already a harvested Gag object `Bfg\Object\Gag` 
which you can use globally for your needs. There is also the opportunity 
to create its own Gag collection.

Consider the situation with the filling of the standard GAG object:
```php
\Bfg\Object\Gag::register('gag_name', MyComponent::class);
\Bfg\Object\Gag::register('gag_next', MyNextComponent::class);
```
Implementation methods:
```php
public function gag(\Bfg\Object\Gag $gag)
{
    $gag->gag_name(...$construct_arguments)
        ->before(function (...$construct_arguments) {}) 
        // "before" To call an event before initialization.
        ->then(function (MyComponent $component, ...$construct_arguments) {})
        // "then" To call an event after initialization.
        ->gag_next();

    return $gag;
}
```
And the initializer will enter it:
```php
\Bfg\Object\Gag::instance(MyComponent::class, function (
    MyComponent $component, array $child, ...$construct_arguments
) {
    return $component->applyChilds($child);
});
```
If you need to wrap in the GAG already ready component and there is no 
possibility to overload or replace it, then you can, I added a wrapper 
for such cases, to the priment, helper `view` is organized through such 
a wrapper:
```php
<?php
use Bfg\Object\GagCore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * Class ViewComponent
 * @package Bfg\Layout\BodyComponents
 */
class ViewComponent
{
    /**
     * The name of the component in the Gag storage.
     * 
     * @var string
     */
    static string $name = "view";

    /**
     * To save in the designer of the object template.
     * 
     * @var Application|Factory|View
     */
    public Application|Factory|View $view;

    /**
     * ViewComponent constructor.
     *
     * @param  string  $name
     * @param  array  $data
     * @param  array  $mergeData
     */
    public function __construct(string $name, array $data = [], array $mergeData = [])
    {
        $this->view = view($name, $data, $mergeData);
    }
    
    /**
     * To call an event before initialization.
     * 
     * @param ...$construct_arguments
     */
    public function gagBefore(...$construct_arguments){
        //
    }
    
    /**
     * To call an event after initialization.
     * 
     * @param ...$construct_arguments
     */
    public function gagThen(...$construct_arguments){
        //
    }

    /**
     * For the use of GAG object to sequence.
     * 
     * @param  GagCore  $core
     * @return string
     */
    public function gagApply(GagCore $core): string
    {
        return $this->view->with('content', implode('', $core->child))->render();
    }
}
```
Well, accordingly, if you need to create your Gag object, 
you can go to the following way:
```php
<?php

use Bfg\Object\GagCore;

/**
 * @mixin \MyGags
 */
class MyGag extends GagCore
{
    /**
     * Storage of components
     * @var array
     */
    #[
        StaticClassStorage('MyComponents'),
        StaticClassStorage('app/Components', false),
        DocMethods([Body::class, 'static'], '{key}({value_construct})', 'Storage body gag {key} component'),
        DocClassName('{class}Gags')
    ]
    static array $storage = [];

    /**
     * Gag instances for subject injection with child
     * @var array
     */
    protected static array $instances = [];
}
```

## Static class storage
This is an attribute that allows you to scan the folder on the 
files with classes and is the list of them for the Static Properties.
> Important! The property must be a static public array!
```php
#[StaticClassStorage('Components')]
static array $classes = [];
```

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
