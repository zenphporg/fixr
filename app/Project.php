<?php

declare(strict_types=1);

namespace App;

use App\Contracts\PathsRepository;
use Symfony\Component\Console\Input\InputInterface;

final class Project
{
  /**
   * Determine the project paths to apply the code style based on the options and arguments passed.
   *
   * @return array<int, string>
   */
  public static function paths(InputInterface $input): array
  {
    if ($input->getOption('dirty')) {
      return self::resolveDirtyPaths();
    }

    $path = $input->getArgument('path');
    assert(is_array($path));

    /** @var array<int, string> */
    return array_values($path);
  }

  /**
   * The project being analysed path.
   */
  public static function path(): string
  {
    $cwd = getcwd();
    assert($cwd !== false);

    return $cwd;
  }

  /**
   * Resolves the dirty paths, if any.
   *
   * @return array<int, string>
   */
  public static function resolveDirtyPaths(): array
  {
    $files = app(PathsRepository::class)->dirty();

    if (empty($files)) {
      abort(0, 'No dirty files found.');
    }

    return $files;
  }
}
