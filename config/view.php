<?php

return [

  /**
   * COMPILED VIEW PATH
   *
   * This option determines where all the compiled Blade templates will be
   * stored for your application. Typically, this is within the storage
   * directory. However, as usual, you are free to change this value.
   */
  'cache' => false,
  'compiled' => realpath(sys_get_temp_dir()),

  /**
   * VIEW STORAGE PATHS
   *
   * Most templating systems load templates from disk. Here you may specify
   * an array of paths that should be checked for your views. Of course
   * the usual Laravel view path has already been registered for you.
   */
  'paths' => [
    resource_path('views'),
  ],
];
