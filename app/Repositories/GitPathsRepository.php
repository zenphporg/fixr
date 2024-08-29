<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\PathsRepository;
use App\Factories\ConfigurationFactory;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

final class GitPathsRepository implements PathsRepository
{
  /**
   * Creates a new Paths Repository instance.
   */
  public function __construct(protected string $path) {}

  /**
   * Determine the "dirty" files.
   *
   * @return array<int, string>
   */
  public function dirty(): array
  {
    $process = tap(new Process(['git', 'status', '--short', '--', '**.php']))->run();

    if (! $process->isSuccessful()) {
      abort(1, 'The [--dirty] option is only available when using Git.');
    }

    $dirtyFiles = collect(preg_split('/\R+/', $process->getOutput(), flags: PREG_SPLIT_NO_EMPTY))
      ->mapWithKeys(fn ($file) => [substr($file, 3) => trim(substr($file, 0, 3))])
      ->reject(fn ($status) => $status === 'D')
      ->map(fn ($status, $file) => $status === 'R' ? Str::after($file, ' -> ') : $file)
      ->map(function ($file) {
        if (PHP_OS_FAMILY === 'Windows') {
          $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
        }

        return $this->path.DIRECTORY_SEPARATOR.$file;
      })
      ->values()
      ->all();

    $files = array_values(array_map(function ($splFile) {
      return $splFile->getPathname();
    }, iterator_to_array(ConfigurationFactory::finder()
      ->in($this->path)
      ->files()
    )));

    return array_values(array_intersect($files, $dirtyFiles));
  }
}
