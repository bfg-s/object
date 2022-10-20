<?php

namespace Bfg\Object;

use Bfg\Doc\Attributes\DocClassName;
use Bfg\Doc\Attributes\DocMethods;

/**
 * @mixin \GagCoreGags
 */
abstract class GagCore
{
    /**
     * The name of injected gag.
     * @var string|null
     */
    public ?string $name = null;

    /**
     * The arguments of injected gag.
     * @var array
     */
    public array $arguments = [];

    /**
     * Gag subject for work.
     * @var object|null
     */
    public object|string|null $subject = null;

    /**
     * Gag child components.
     * @var array
     */
    public array $child = [];

    /**
     * Storage of components.
     * @var array
     */
    #[
        DocMethods([self::class, 'static'], '{key}(...$arguments)', 'Storage gag {key} component'),
        DocClassName('{class}Gags')
    ]
    public static array $storage = [];

    /**
     * Gag instances for subject injection with child.
     * @var array
     */
    protected static array $instances = [];

    /**
     * Gag before callback's.
     * @var callable[]
     */
    protected array $before = [];

    /**
     * Gag then callback's.
     * @var callable[]
     */
    protected array $then = [];

    /**
     * Gag then callback's.
     * @var callable
     */
    protected $instance = null;

    /**
     * MAGIC SEGMENT.
     */

    /**
     * Gag constructor.
     * @param  string|null  $name
     * @param  array  $arguments
     */
    public function __construct(string $_name = null, array $arguments = [])
    {
        $this->createAttrs($_name, $arguments);
    }

    /**
     * Magic call for init or child injection.
     * @param  string  $name
     * @param  array  $arguments
     * @return $this
     * @throws \Exception
     */
    public function __call(string $name, array $arguments)
    {
        if (static::has($name)) {
            if ($this->subject) {
                return $this->child[] =
                    static::create($name, ...$arguments);
            } else {
                $this->createAttrs($name, $arguments);

                return $this;
            }
        } else {
            throw new \Exception("Gag subject [{$name}] not found!");
        }
    }

    /**
     * PUBLIC PROCESSORS.
     */

    /**
     * @param  callable  $call
     * @return $this
     */
    public function with(callable $call): static
    {
        call_user_func($call, $this);

        return $this;
    }

    /**
     * Add before callback.
     * @param  callable  $callable
     * @return $this
     */
    public function before(callable $callable): static
    {
        $this->before[] = $callable;

        return $this;
    }

    /**
     * Add then callback.
     * @param  callable  $callable
     * @return $this
     */
    public function then(callable $callable): static
    {
        $this->then[] = $callable;

        return $this;
    }

    /**
     * Make gag.
     * @return mixin|object|string
     */
    public function make()
    {
        $argument_values = array_values($this->arguments);

        foreach ($this->before as $item) {
            call_user_func_array($item, $argument_values);
        }

        if (method_exists($this->subject, 'gagBefore')) {
            $this->subject->gagBefore(...$argument_values);
        }

        $this->subject = app()->has($this->subject) ?
            app($this->subject) : new ($this->subject)(...$this->arguments);

        if (method_exists($this->subject, 'gagThen')) {
            $this->subject->gagThen(...$argument_values);
        }

        foreach ($this->then as $item) {
            call_user_func($item, $this->subject, ...$argument_values);
        }

        /** @var GagCore $item */
        foreach ($this->child as $key => $item) {
            $this->child[$key] = $item->make();
        }

        if ($this->instance) {
            return call_user_func($this->instance, $this->subject, $this->child, ...$argument_values);
        } elseif (method_exists($this->subject, 'gagApply')) {
            return $this->subject->gagApply($this, ...$argument_values);
        } else {
            foreach (static::$instances as $instance => $callback) {
                if ($this->subject instanceof $instance) {
                    return call_user_func($callback, $this->subject, $this->child, ...$argument_values);
                }
            }
        }

        return $this->subject;
    }

    /**
     * INSIDE PROCESSORS.
     */

    /**
     * Fill all gag attributes.
     * @param  string|null  $name
     * @param  array  $arguments
     */
    protected function createAttrs(string $name = null, array $arguments = [])
    {
        if ($name && static::has($name)) {
            $this->name = $name;
            if ($arguments) {
                $this->arguments = $arguments;
            }
            $this->subject = static::$storage[$name];
        }
    }

    /**
     * STATIC CONTROL.
     */

    /**
     * Gag creator.
     *
     * @param  string|null  $_name
     * @param ...$arguments
     * @return static
     */
    public static function create(string $_name = null, ...$arguments): static
    {
        return new static($_name, $arguments);
    }

    /**
     * Has gag subject in storage.
     *
     * @param  string  $name
     * @return bool
     */
    public static function has(string $name): bool
    {
        return isset(static::$storage[$name]);
    }

    /**
     * Register new gag component.
     * @param  string  $name
     * @param  string  $class
     */
    public static function register(string $name, string $class)
    {
        static::$storage[$name] = $class;
    }

    /**
     * Register new gag components.
     * @param  array  $array
     */
    public static function add(array $array)
    {
        static::$storage = array_merge(static::$storage, $array);
    }

    /**
     * Add instance for subject injection with child.
     * @param  string  $instanceof
     * @param  callable  $callback
     */
    public static function instance(string $instanceof, callable $callback)
    {
        static::$instances[$instanceof] = $callback;
    }
}
