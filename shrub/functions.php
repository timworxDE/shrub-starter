<?php

use Leaf\Config;
use Shrub\Shrub;

/**
 * Set up leaf config with an array
 * @param array $config
 * @param string|null $parent
 * @return void
 */
function _set_config(array $config, ?string $parent = null): void {
    foreach ($config as $name => $value) {
        $key = empty($parent) ? $name : $parent.'.'.$name;
        if (is_array($value)) {
            _set_config($value, $key);
        } else {
            if (is_string($value)) {
                $value = str_replace('%project_root%', $_SERVER['PROJECT_ROOT'], $value);
            }

            Config::set($key, $value);

            if ($parent == 'env')
                $_ENV[$name] = $value; // set values in env config to global env var
        }
    }
}

if (!function_exists('shrub')) {
    /**
     * Return the Shrub instance
     * @return \Shrub\Shrub
     */
    function shrub(): Shrub
    {
        if (!(Config::getStatic('shrub'))) {
            Config::singleton('shrub', function () {
                return new Shrub();
            });
        }

        return Config::get('shrub');
    }
}

if (!function_exists('load_env')) {
    /**
     * Load the .env file
     * @return void
     */
    function load_env(): void
    {
        if (!(Config::getStatic('load_env'))) {
            Config::singleton('load_env', function () {
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
                $dotenv->load();
            });
        }

        Config::get('load_env');
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = []): string
    {
        // todo: move to renderer class
        return match(Config::get('views.engine')) {
            'blade' => shrub()->blade()->render($view, $data),
            'twig' => shrub()->twig($view, $data),
            'bare' => shrub()->template()->render($view, $data),
            default => file_get_contents(Config::get('views.pages').'/'.$view)
        };
    }
}
