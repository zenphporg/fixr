<?php

declare(strict_types=1);

namespace App\Actions;

use App\Output\SummaryOutput;
use Illuminate\Console\Command;
use PhpCsFixer\Console\Report\FixReport;
use PhpCsFixer\Console\Report\FixReport\ReportSummary;
use PhpCsFixer\Error\ErrorsManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ElaborateSummary
{
  /**
   * Creates a new Elaborate Summary instance.
   *
   * @return void
   */
  public function __construct(
    protected ErrorsManager $errors,
    protected InputInterface $input,
    protected OutputInterface $output,
    protected SummaryOutput $summaryOutput,
  ) {}

  /**
   * Elaborates the summary of the given changes.
   *
   * @param  array<string, array{appliedFixers: array<int, string>, diff: string}>  $changes
   */
  public function execute(int $totalFiles, array $changes): int
  {
    $summary = new FixReport\ReportSummary(
      $changes,
      $totalFiles,
      0,
      0,
      $this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE,
      $this->input->getOption('test') || $this->input->getOption('bail'),
      $this->output->isDecorated()
    );

    if ($this->input->getOption('format')) {
      $this->displayUsingFormatter($summary, $totalFiles);
    } else {
      $this->summaryOutput->handle($summary, $totalFiles);
    }

    $failure = (($summary->isDryRun() || $this->input->getOption('repair')) && count($changes) > 0)
        || count($this->errors->getInvalidErrors()) > 0
        || count($this->errors->getExceptionErrors()) > 0
        || count($this->errors->getLintErrors()) > 0;

    return $failure ? Command::FAILURE : Command::SUCCESS;
  }

  /**
   * Formats the given summary using the "selected" formatter.
   */
  protected function displayUsingFormatter(ReportSummary $summary, int $totalFiles): void
  {
    $reporter = match ($format = $this->input->getOption('format')) {
      'checkstyle' => new FixReport\CheckstyleReporter,
      'gitlab' => new FixReport\GitlabReporter,
      'json' => new FixReport\JsonReporter,
      'junit' => new FixReport\JunitReporter,
      'txt' => new FixReport\TextReporter,
      'xml' => new FixReport\XmlReporter,
      default => abort(1, sprintf('Format [%s] is not supported.', $format)),
    };

    $this->output->write($reporter->generate($summary));
  }
}
