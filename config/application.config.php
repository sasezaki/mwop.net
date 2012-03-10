<?php
return array(
    'modules' => array(
        'Application',
        'Authentication',
        // // 'Cache', // commenting out as profiling shows increased load times?
        // 'Comic',
        'CommonResource',
        'Blog',
        'Contact',
    ),
    'module_listener_options' => array( 
        'config_cache_enabled'     => false,
        'cache_dir'                => realpath(dirname(__DIR__) . '/data/cache'),
        'module_paths'             => array(
            realpath(__DIR__ . '/../module'),
        ),
    ),
);