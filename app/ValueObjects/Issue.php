<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Support\Str;
use NunoMaduro\Collision\Highlighter;
use ReflectionClass;

final class Issue
{
  /**
   * Creates a new Issue instance.
   *
   * @param  array<string, array<int, string>|\Throwable>  $payload
   */
  public function __construct(
    protected string $path,
    protected string $file,
    protected string $symbol,
    protected array $payload
  ) {}

  /**
   * Returns the file where the change occur.
   */
  public function file(): string
  {
    return str_replace($this->path.DIRECTORY_SEPARATOR, '', $this->file);
  }

  /**
   * Returns the issue's description.
   */
  public function description(bool $testing): string
  {
    if (! empty($this->payload['source'])) {
      return $this->payload['source']->getMessage();
    }

    return collect($this->payload['appliedFixers'])->map(function ($appliedFixer) {
      return $appliedFixer;
    })->implode(', ');
  }

  /**
   * If the issue can be fixed.
   */
  public function fixable(): bool
  {
    return ! empty($this->payload['appliedFixers']);
  }

  /**
   * Returns the issue's code, if any.
   */
  public function code(): ?string
  {
    if (! $this->fixable()) {
      $content = file_get_contents($this->file);

      $exception = $this->payload['source']->getPrevious() ?: $this->payload['source'];

      return (new Highlighter)->highlight($content, $exception->getLine());
    }

    return $this->diff();
  }

  /**
   * Returns the issue's symbol.
   */
  public function symbol(): string
  {
    return $this->symbol;
  }

  /**
   * Returns the issue's diff, if any.
   */
  protected function diff(): ?string
  {
    if ($this->payload['diff']) {
      $highlighter = new Highlighter;
      $reflector = new ReflectionClass($highlighter);

      $diff = $this->payload['diff'];

      $diff = str((string) $diff)
        ->explode("\n")
        ->map(function ($line) {
          if (Str::startsWith($line, '+')) {
            return '//+<fg=green>'.$line.'</>';
          } elseif (Str::startsWith($line, '-')) {
            return '//-<fg=red>'.$line.'</>';
          }

          return $line;
        })->implode("\n");

      $method = tap($reflector->getMethod('getHighlightedLines'))->setAccessible(true);
      $tokenLines = $method->invoke($highlighter, "<?php\n".$diff);
      $tokenLines = array_slice($tokenLines, 3);

      $method = tap($reflector->getMethod('colorLines'))->setAccessible(true);

      /** @var array<int, string> $lines */
      $lines = $method->invoke($highlighter, $tokenLines);
      $lines = collect($lines)->map(function ($line) {
        if (str($line)->startsWith('[90;3m//-')) {
          return str($line)
            ->replaceFirst('[90;3m//-', '');
        }

        if (str($line)->startsWith('//-')) {
          return str($line)
            ->replaceFirst('//-', '');
        }

        if (str($line)->startsWith('[90;3m//+')) {
          return str($line)
            ->replaceFirst('[90;3m//+', '');
        }

        if (str($line)->startsWith('//+')) {
          return str($line)
            ->replaceFirst('//+', '');
        }

        return $line;
      });

      return '  '.$lines->implode("\n  ");
    } else {
      return '';
    }
  }
}
