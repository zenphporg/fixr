<?php

declare(strict_types=1);

use App\Factories\ConfigurationFactory;

return ConfigurationFactory::preset([
  '@PSR12' => true,
  'no_unused_imports' => true,
]);
