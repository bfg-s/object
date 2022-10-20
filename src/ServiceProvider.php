<?php

namespace Bfg\Object;

use Bfg\Installer\Processor\DumpAutoloadProcessor;
use Bfg\Installer\Providers\InstalledProvider;
use Bfg\Object\Attributes\StaticClassStorage;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Executed when the provider is registered
     * and the extension is installed.
     * @return void
     */
    public function boot(): void
    {
        if (! Collection::hasMacro('paginate')) {
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
        $this->factory = app(StaticStorageFactory::class);

        foreach ($this->factory->all() as $class => $properties) {
            foreach ($properties as $property => $data) {
                $class::$$property = $data;
            }
        }
    }

    /**
     * Run on dump extension.
     * @param  DumpAutoloadProcessor  $processor
     */
    public function dump(DumpAutoloadProcessor $processor)
    {
        parent::dump($processor);

        $this->factory->clear();

        \Attributes::find(StaticClassStorage::class, [
            app(StaticStorageProcessor::class, [
                'factory' => $this->factory,
            ]), 'run',
        ], \Attribute::TARGET_PROPERTY);

        $this->factory->save();
    }
}
