<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\PathsRepository;
use App\Project;
use App\Repositories\ConfigurationJsonRepository;
use App\Repositories\GitPathsRepository;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\InputInterface;

final class RepositoriesServiceProvider extends ServiceProvider
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
    $this->app->singleton(ConfigurationJsonRepository::class, function () {
      $input = resolve(InputInterface::class);
      $config = $input->getOption('config') ?: Project::path().'/fixr.json';
      assert(is_string($config));

      $noConfig = $input->getOption('no-config');
      $preset = $input->getOption('preset');
      assert(is_string($preset) || is_null($preset));

      return new ConfigurationJsonRepository(
        $noConfig ? null : $config,
        $preset,
      );
    });

    $this->app->singleton(PathsRepository::class, function () {
      return new GitPathsRepository(
        Project::path(),
      );
    });
  }
}
