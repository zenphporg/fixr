<?php

declare(strict_types=1);

use App\Factories\ConfigurationFactory;

return ConfigurationFactory::preset([
  '@PER' => true,
  'no_unused_imports' => true,
]);
