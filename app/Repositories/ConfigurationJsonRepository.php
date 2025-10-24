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
    /** @var array<string, array<int, string>|string> */
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
    $rules = $this->get()['rules'] ?? [];
    assert(is_array($rules));

    return $rules;
  }

  /**
   * Get the cache file location.
   */
  public function cacheFile(): ?string
  {
    $cacheFile = $this->get()['cache-file'] ?? null;
    assert(is_string($cacheFile) || is_null($cacheFile));

    return $cacheFile;
  }

  /**
   * Get the preset option.
   */
  public function preset(): string
  {
    $preset = $this->preset ?: ($this->get()['preset'] ?? 'zen');
    assert(is_string($preset));

    return $preset;
  }

  /**
   * Get the indent option.
   */
  public function indent(): string
  {
    $indent = $this->preset() === 'zen' ? '  ' : '    ';
    $result = $this->indent ?: ($this->get()['indent'] ?? $indent);
    assert(is_string($result));

    return $result;
  }

  /**
   * Get the configuration from the "fixr.json" file.
   *
   * @return array<string, array<int, string>|string>
   */
  protected function get(): array
  {
    if (! is_null($this->path) && $this->fileExists((string) $this->path)) {
      $contents = file_get_contents($this->path);
      assert($contents !== false);

      $configuration = json_decode($contents, true);

      if (! is_array($configuration)) {
        abort(1, sprintf('The configuration file [%s] is not valid JSON.', $this->path));
      }

      /** @var array<string, array<int, string>|string> */
      return $configuration;
    }

    return [];
  }

  /**
   * Determine if a local or remote file exists.
   */
  protected function fileExists(string $path): bool
  {
    return match (true) {
      str_starts_with($path, 'http://') || str_starts_with($path, 'https:') => (function () use ($path) {
        $headers = get_headers($path);
        assert($headers !== false);

        return str_contains($headers[0], '200 OK');
      })(),
      default => file_exists($path)
    };
  }
}
