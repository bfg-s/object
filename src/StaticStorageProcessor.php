<?php

namespace Bfg\Object;

use Bfg\Attributes\Scanner\ScanClasses;
use Bfg\Attributes\Scanner\ScanDirectories;
use Bfg\Attributes\Scanner\ScanFiles;
use Bfg\Object\Attributes\StaticClassStorage;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class StaticStorageProcessor.
 * @package Bfg\Object
 */
class StaticStorageProcessor
{
    /**
     * StaticStorageProcessor constructor.
     * @param  StaticStorageFactory  $factory
     * @param  Filesystem  $fs
     */
    public function __construct(
        public StaticStorageFactory $factory,
        public Filesystem $fs
    ) {
    }

    /**
     * @param  StaticClassStorage  $attribute
     * @param  ReflectionProperty  $property
     * @param  ReflectionClass  $class
     */
    public function run(StaticClassStorage $attribute, ReflectionProperty $property, ReflectionClass $class)
    {
        $path = $attribute->relative_path ?
            dirname($class->getFileName())."/{$attribute->classes_path}" :
            base_path("{$attribute->classes_path}");

        if (is_dir($path)) {
            foreach ($this->classes($path) as $class_path) {
                if (property_exists($class_path->name, 'name')) {
                    $name = $class_path->name::$name;
                } else {
                    $name = \Str::snake(class_basename($class_path->name));
                }
                $this->factory->set(
                    [$class->name, $property->name, $name], $class_path->name
                );
                $p = $property->name;
                $class->name::$$p[$property->name] = $class_path->name;
            }
        }
    }

    /**
     * @param  string  $path
     * @return \Illuminate\Support\Collection|ReflectionClass[]
     */
    protected function classes(string $path)
    {
        return (new ScanClasses($this->fs, new ScanFiles($this->fs, new ScanDirectories($this->fs, $path))))
            ->classes;
    }
}
