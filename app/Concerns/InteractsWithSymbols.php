<?php

declare(strict_types=1);

namespace App\Concerns;

use PhpCsFixer\Error\Error;
use PhpCsFixer\Runner\Event\FileProcessed;

/**
 * @property \Symfony\Component\Console\Input\InputInterface $input
 * @property \Symfony\Component\Console\Output\OutputInterface $output
 */
trait InteractsWithSymbols
{
  /**
   * @var array<int, array{symbol: string, format: string}|array<int, array{symbol: string, format: string}>>
   */
  protected array $statuses = [
    FileProcessed::STATUS_INVALID => ['symbol' => '!', 'format' => '<options=bold;fg=red>%s</>'],
    FileProcessed::STATUS_SKIPPED => ['symbol' => '.', 'format' => '<fg=gray>%s</>'],
    FileProcessed::STATUS_NO_CHANGES => ['symbol' => '.', 'format' => '<fg=gray>%s</>'],
    FileProcessed::STATUS_FIXED => [
      ['symbol' => '⨯', 'format' => '<options=bold;fg=red>%s</>'],
      ['symbol' => '✓', 'format' => '<options=bold;fg=green>%s</>'],
    ],
    FileProcessed::STATUS_EXCEPTION => ['symbol' => '!', 'format' => '<options=bold;fg=red>%s</>'],
    FileProcessed::STATUS_LINT => ['symbol' => '!', 'format' => '<options=bold;fg=red>%s</>'],
  ];

  /**
   * Gets the symbol for the given status.
   */
  final public function getSymbol(int $status): string
  {
    $statusSymbol = $this->statuses[$status];

    if (! isset($statusSymbol['symbol'])) {
      /** @var array<int, array{symbol: string, format: string}> $statusSymbol */
      $statusSymbol = ($this->input->getOption('test') || $this->input->getOption('bail'))
          ? $statusSymbol[0]
          : $statusSymbol[1];
    }

    /** @var array{symbol: string, format: string} $statusSymbol */
    if ($this->output->isDecorated()) {
      return sprintf($statusSymbol['format'], $statusSymbol['symbol']);
    }

    return $statusSymbol['symbol'];
  }

  /**
   * Converts the given error type to a processed status.
   */
  final protected function getSymbolFromErrorType(int $type): string
  {
    $status = match ($type) {
      Error::TYPE_INVALID => FileProcessed::STATUS_INVALID,
      Error::TYPE_EXCEPTION => FileProcessed::STATUS_EXCEPTION,
      Error::TYPE_LINT => FileProcessed::STATUS_LINT,
      default => FileProcessed::STATUS_INVALID,
    };

    return $this->getSymbol($status);
  }
}
