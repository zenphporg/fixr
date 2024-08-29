<?php

declare(strict_types=1);

namespace App;

use LaravelZero\Framework\Kernel as BaseKernel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Kernel extends BaseKernel
{
  /**
   * Run the console application.
   *
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface|null  $output
   * @return int
   */
  public function handle($input, $output = null)
  {
    $this->app->instance(InputInterface::class, $input);
    $this->app->instance(OutputInterface::class, $output);

    return parent::handle($input, $output);
  }
}
