<?php

namespace Bupple\Engine\Providers;

use Bupple\Engine\BuppleEngine;
use Bupple\Engine\Core\Drivers\Memory\MemoryManager;
use Bupple\Engine\Core\Drivers\Memory\Contracts\MemoryDriverInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BuppleEngineServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MemoryManager::class, function ($app) {
            return new MemoryManager($app['config']->get('bupple-engine.memory', []));
        });

        $this->app->singleton(BuppleEngine::class, function ($app) {
            return new BuppleEngine(
                $app->make(MemoryManager::class),
                $app['config']->get('bupple-engine', [])
            );
        });

        $this->app->alias(BuppleEngine::class, 'bupple.engine');

        // Bind the default memory driver
        $this->app->bind(MemoryDriverInterface::class, function ($app) {
            return $app->make(MemoryManager::class)->driver();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/bupple-engine.php' => config_path('bupple-engine.php'),
        ]);
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            BuppleEngine::class,
            'bupple.engine',
        ];
    }
}
