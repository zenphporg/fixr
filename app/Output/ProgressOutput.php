<?php

declare(strict_types=1);

namespace App\Output;

use App\Concerns\InteractsWithSymbols;
use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ProgressOutput
{
  use InteractsWithSymbols;

  /**
   * Holds the current number of processed files.
   */
  protected int $processed = 0;

  /**
   * Holds the number of symbols on the current terminal line.
   */
  protected int $symbolsPerLine = 0;

  /**
   * Creates a new Progress Output instance.
   *
   * @return void
   */
  public function __construct(
    protected EventDispatcherInterface $dispatcher,
    protected InputInterface $input,
    protected OutputInterface $output,
  ) {
    $this->symbolsPerLine = (new Terminal)->getWidth() - 4;
  }

  /**
   * Subscribes for file processed events.
   */
  public function subscribe(): void
  {
    $this->dispatcher->addListener(FixerFileProcessedEvent::NAME, [$this, 'handle']);
  }

  /**
   * Stops the file processed event subscription.
   */
  public function unsubscribe(): void
  {
    $this->dispatcher->removeListener(FixerFileProcessedEvent::NAME, [$this, 'handle']);
  }

  /**
   * Handle the given processed file event.
   */
  public function handle(FixerFileProcessedEvent $event): void
  {
    $symbolsOnCurrentLine = $this->processed % $this->symbolsPerLine;

    if ($symbolsOnCurrentLine >= (new Terminal)->getWidth() - 4) {
      $symbolsOnCurrentLine = 0;
    }

    if ($symbolsOnCurrentLine === 0) {
      $this->output->writeln('');
      $this->output->write('  ');
    }

    $this->output->write($this->getSymbol($event->getStatus()));

    $this->processed++;
  }
}
