<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpCsFixer\Error\ErrorsManager;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   */
  public function boot(): void {}

  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(ErrorsManager::class, function () {
      return new ErrorsManager;
    });

    $this->app->singleton(EventDispatcher::class, function () {
      return new EventDispatcher;
    });
  }
}
