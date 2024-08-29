<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\ElaborateSummary;
use App\Actions\FixCode;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class DefaultCommand extends Command
{
  /**
   * Command name.
   *
   * @var string
   */
  protected $name = 'default';

  /**
   * Command description.
   *
   * @var string
   */
  protected $description = 'Fix the coding style of the given path';

  /**
   * The configuration of the command.
   */
  protected function configure(): void
  {
    parent::configure();

    $this
      ->setDefinition(
        [
          new InputArgument('path', InputArgument::IS_ARRAY, 'The path to fix', [(string) getcwd()]),
          new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The configuration that should be used'),
          new InputOption('no-config', '', InputOption::VALUE_NONE, 'Disable loading any configuration file'),
          new InputOption('preset', '', InputOption::VALUE_REQUIRED, 'The preset that should be used'),
          new InputOption('test', '', InputOption::VALUE_NONE, 'Test for code style errors without fixing them'),
          new InputOption('bail', '', InputOption::VALUE_NONE, 'Test for code style errors without fixing them and stop on first error'),
          new InputOption('repair', '', InputOption::VALUE_NONE, 'Fix code style errors but exit with status 1 if there were any changes made'),
          new InputOption('dirty', '', InputOption::VALUE_NONE, 'Only fix files that have uncommitted changes'),
          new InputOption('format', '', InputOption::VALUE_REQUIRED, 'The output format that should be used'),
          new InputOption('cache-file', '', InputArgument::OPTIONAL, 'The path to the cache file'),
        ]
      );
  }

  /**
   * Execute the console command.
   */
  public function handle(FixCode $fixCode, ElaborateSummary $elaborateSummary): int
  {
    [$totalFiles, $changes] = $fixCode->execute();

    return $elaborateSummary->execute($totalFiles, $changes);
  }
}
