<?php

declare(strict_types=1);

namespace App\Factories;

use App\Repositories\ConfigurationJsonRepository;
use PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

final class ConfigurationFactory
{
  /**
   * @var array<int, string>
   */
  protected static array $notName = [
    '_ide_helper_actions.php',
    '_ide_helper_models.php',
    '_ide_helper.php',
    '.phpstorm.meta.php',
    '*.blade.php',
  ];

  /**
   * @var array<int, string>
   */
  protected static array $exclude = [
    'bootstrap/cache',
    'build',
    'node_modules',
    'storage',
  ];

  /**
   * Creates a PHP CS Fixer Configuration with the given array of rules.
   *
   * @param  array<string, array<string, array<int|string, string|null>|bool|string>|bool>  $rules
   */
  public static function preset(array $rules): ConfigInterface
  {
    $localConfiguration = resolve(ConfigurationJsonRepository::class);

    return (new Config)
      ->setParallelConfig(ParallelConfigFactory::detect())
      ->setFinder(self::finder())
      ->setIndent($localConfiguration->indent())
      ->setRules(array_merge($rules, $localConfiguration->rules()))
      ->setRiskyAllowed(true)
      ->setUsingCache(true);
  }

  /**
   * Creates the finder instance.
   */
  public static function finder(): Finder
  {
    $localConfiguration = resolve(ConfigurationJsonRepository::class);

    $finder = Finder::create()
      ->notName(self::$notName)
      ->exclude(self::$exclude)
      ->ignoreDotFiles(true)
      ->ignoreVCS(true);

    foreach ($localConfiguration->finder() as $method => $arguments) {
      if (! method_exists($finder, $method)) {
        abort(1, sprintf('Option [%s] is not valid.', $method));
      }

      $finder->{$method}($arguments);
    }

    return $finder;
  }
}
