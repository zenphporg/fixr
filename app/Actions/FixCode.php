<?php

declare(strict_types=1);

namespace App\Actions;

use App\Factories\ConfigurationResolverFactory;
use App\Output\ProgressOutput;
use LaravelZero\Framework\Exceptions\ConsoleException;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Runner\Runner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class FixCode
{
  /**
   * Creates a new Fix Code instance.
   *
   * @return void
   */
  public function __construct(
    protected ErrorsManager $errors,
    protected EventDispatcher $events,
    protected InputInterface $input,
    protected OutputInterface $output,
    protected ProgressOutput $progress,
  ) {}

  /**
   * Fixes the project resolved by the current input and output.
   *
   * @return array{int, array<string, array{appliedFixers: array<int, string>, diff: string}>}
   */
  public function execute()
  {
    try {
      [$resolver, $totalFiles] = ConfigurationResolverFactory::fromIO($this->input, $this->output);
    } catch (ConsoleException $exception) {
      return [$exception->getCode(), []];
    }

    if (is_null($this->input->getOption('format'))) {
      $this->progress->subscribe();
    }

    /** @var \Traversable<mixed, \SplFileInfo>|null $finder */
    $finder = $resolver->getFinder();

    /** @var array<string, array{appliedFixers: array<int, string>, diff: string}> $changes */
    $changes = (new Runner(
      $finder,
      $resolver->getFixers(),
      $resolver->getDiffer(),
      $this->events,
      $this->errors,
      $resolver->getLinter(),
      $resolver->isDryRun(),
      $resolver->getCacheManager(),
      $resolver->getDirectory(),
      $resolver->shouldStopOnViolation()
    ))->fix();

    return tap([$totalFiles, $changes], fn () => $this->progress->unsubscribe());
  }
}
