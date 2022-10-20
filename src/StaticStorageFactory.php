<?php

namespace Bfg\Object;

use Bfg\Entity\ConfigFactory;

/**
 * Class StaticStorageFactory.
 * @package Bfg\Object
 */
class StaticStorageFactory extends ConfigFactory
{
    /**
     * StaticStorageFactory constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->bootstrapPath('cache/static_storage.php'));
    }
}
