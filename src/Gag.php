<?php

namespace Bfg\Object;

use Bfg\Doc\Attributes\DocClassName;
use Bfg\Doc\Attributes\DocMethods;
use Bfg\Object\Attributes\StaticClassStorage;

/**
 * @mixin \GagGags
 */
class Gag extends GagCore
{
    /**
     * Storage of components.
     * @var array
     */
    #[
        StaticClassStorage('app/Gags', false),
        DocMethods([self::class, 'static'], '{key}(...$arguments)', 'Storage gag {key} component'),
        DocClassName('{class}Gags')
    ]
    public static array $storage = [];

    /**
     * Gag instances for subject injection with child.
     * @var array
     */
    protected static array $instances = [];
}
