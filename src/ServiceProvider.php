<?php

namespace Bfg\Object;

use Bfg\Installer\Providers\InstalledProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class ServiceProvider
 * @package Bfg\Object
 */
class ServiceProvider extends InstalledProvider
{
    /**
     * The description of extension.
     * @var string|null
     */
    public ?string $description = "To work with objects and arrays";

    /**
     * Set as installed by default.
     * @var bool
     */
    public bool $installed = true;

    /**
     * Executed when the provider is registered
     * and the extension is installed.
     * @return void
     */
    public function installed(): void
    {
        if (!Collection::hasMacro('paginate')) {

            Collection::macro('paginate', function ($perPage = 15, $pageName = 'page', $page = null) {

                $page = $page ?: (Paginator::resolveCurrentPage($pageName) ?: 1);

                return (new LengthAwarePaginator(
                    $this->forPage($page, $perPage),
                    $this->count(),
                    $perPage,
                    $page,
                    ['pageName' => $pageName]
                ))->withPath('');
            });
        }
    }

    /**
     * Executed when the provider run method
     * "boot" and the extension is installed.
     * @return void
     */
    public function run(): void
    {
        //
    }
}

