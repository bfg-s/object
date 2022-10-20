<?php

namespace Bfg\Object\Attributes;

use Attribute;

/**
 * Class StaticClassStorage.
 * @package Bfg\Object\Attributes
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class StaticClassStorage
{
    /**
     * StaticClassStorage constructor.
     * @param  string  $classes_path
     * @param  bool  $relative_path
     */
    public function __construct(
        public string $classes_path,
        public bool $relative_path = true,
    ) {
    }
}
