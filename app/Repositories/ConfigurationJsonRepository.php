<?php

declare(strict_types=1);

namespace App\Repositories;

final class ConfigurationJsonRepository
{
  /**
   * Lists the finder options.
   *
   * @var array<int, 'exclude'|'notPath'|'notName'>
   */
  protected array $finderOptions = [
    'exclude',
    'notPath',
    'notName',
  ];

  /**
   * Create a new Configuration Json Repository instance.
   *
   * @return void
   */
  public function __construct(
    protected ?string $path,
    protected ?string $preset,
    protected ?string $indent = null
  ) {}

  /**
   * Get the finder options.
   *
   * @return array<string, array<int, string>|string>
   */
  public function finder(): array
  {
    return collect($this->get())
      ->filter(fn ($value, $key) => in_array($key, $this->finderOptions))
      ->toArray();
  }

  /**
   * Get the rules options.
   *
   * @return array<int, string>
   */
  public function rules(): array
  {
    return $this->get()['rules'] ?? [];
  }

  /**
   * Get the cache file location.
   */
  public function cacheFile(): ?string
  {
    return $this->get()['cache-file'] ?? null;
  }

  /**
   * Get the preset option.
   */
  public function preset(): string
  {
    return $this->preset ?: ($this->get()['preset'] ?? 'zen');
  }

  /**
   * Get the indent option.
   */
  public function indent(): string
  {
    $indent = $this->preset() === 'zen' ? '  ' : '    ';

    return $this->indent ?: ($this->get()['indent'] ?? $indent);
  }

  /**
   * Get the configuration from the "fixr.json" file.
   *
   * @return array<string, array<int, string>|string>
   */
  protected function get(): array
  {
    if (! is_null($this->path) && $this->fileExists((string) $this->path)) {
      return tap(json_decode(file_get_contents($this->path), true), function ($configuration) {
        if (! is_array($configuration)) {
          abort(1, sprintf('The configuration file [%s] is not valid JSON.', $this->path));
        }
      });
    }

    return [];
  }

  /**
   * Determine if a local or remote file exists.
   */
  protected function fileExists(string $path): bool
  {
    return match (true) {
      str_starts_with($path, 'http://') || str_starts_with($path, 'https:') => str_contains(get_headers($path)[0], '200 OK'),
      default => file_exists($path)
    };
  }
}
