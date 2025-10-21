<?php

namespace Shrub;

use Leaf\App;
use Leaf\Config;
use Symfony\Component\Yaml\Yaml;

class Shrub extends App
{
    public function __construct(array $userSettings = [])
    {
        parent::__construct($userSettings);

        $this->loadConfig();
    }

    protected function loadConfig(array $userSettings = [])
    {
        $_SERVER['PROJECT_ROOT'] = realpath($_SERVER['DOCUMENT_ROOT'].'/..');

        $config = Yaml::parseFile(__DIR__.'/res/default.config.yaml');
        if (file_exists($_SERVER['PROJECT_ROOT'].'/config.yaml')) {
            $config = array_replace_recursive($config, Yaml::parseFile($_SERVER['PROJECT_ROOT'].'/config.yaml'));
        } elseif (file_exists($_SERVER['PROJECT_ROOT'].'/config.yml')) {
            $config = array_replace_recursive($config, Yaml::parseFile($_SERVER['PROJECT_ROOT'].'/config.yml'));
        }

        _set_config($config);
        parent::loadConfig($userSettings);

        $this->setupDefaultContainer();
    }

    protected function setupDefaultContainer()
    {
        Config::singleton('shrub', function () {
            return $this;
        });

        // Setup BareUI
        Config::set('views.path', Config::get('views.pages'));
        Config::set('views.cachePath', Config::get('views.cache'));

        // todo: check config which template engione is used
        // Setup Twig
        parent::attachView(\Leaf\Twig::class, 'twig');
        parent::twig()->configure(
            [
                Config::get('views.pages'),
                Config::get('views.templates'),
            ],
            [
                'cache' => Config::get('views.cache'),
                'debug' => Config::get('debug'),
            ]
        );

        // Setup Blade
        parent::attachView(\Leaf\Blade::class);
        parent::blade()->configure([
            'views' => [
                Config::get('views.pages'),
                Config::get('views.templates'),
            ],
            'cache' => Config::get('views.cache')
        ]);

        // Setup Vite
        \Leaf\Vite::config([
            'assets' => Config::get('assets.build'),
        ]);
    }

    public static function run(?callable $callback = null)
    {
        self::shrubRoutes();
        return parent::run($callback);
    }

    protected static function shrubRoutes()
    {
        self::post('/_formmail', function () {
            $result = FormMailer::processRequest();
            $flash = $result['success'] ? 'FORM_SUCCESS' : 'FORM_ERROR';
            if (array_key_exists('missing', $result)) {
                $flash .= 'FORM_MISSING';
                session()->set('missing', $result['missing']);
            }
            response()->withFlash('form', $flash)->redirect($result['redirect']);
        });

        self::get('.*', function () {
            $path = request()->getPath();
            $view = self::getView($path);

            if (empty($view)) {
                $errorTemplates = Config::get('views.templates').'/error';
                $ext = self::getViewExtension();
                if (file_exists($errorTemplates.'/404'.$ext)) {
                    response()->status(404)->view('/error/404');
                } elseif (file_exists($errorTemplates.'/40X'.$ext)) {
                    response()->status(404)->view('/error/40X');
                } elseif (file_exists($errorTemplates.'/4XX'.$ext)) {
                    response()->status(404)->view('/error/4XX');
                }
                response()->status(404)->send();
                // todo: move to renderer class
            }

            $params = request()->query();
            $params['_meta'] = [
                'path' => $path,
                'view' => $view,
            ];
            response()->view($view, $params);
        });
    }

    protected static function getView(string $url): ?string
    {
        // todo: move to renderer class
        $logger = Config::get('log');
        $logger->info("View for URL: {$url}");

        $pages = Config::get('views.pages');

        $filePath = $pages.$url;
        $ext = self::getViewExtension();

        if (is_file($filePath.$ext)) {
            $view = $url.$ext;
        } elseif (is_file($filePath.'/index'.$ext)) {
            $view = $url.'/index'.$ext;
        } else {
            $logger->warning("View for URL: {$url} not found");
            $view = null;
        }

        if ($ext == '.blade.php' && !empty($view)) {
            $view = rtrim($view, '.blade.php');
        }

        return $view;
    }

    protected static function getViewExtension(): string
    {
        // todo: move to renderer class
        return match(Config::get('views.engine')) {
            'blade' => '.blade.php',
            'twig' => '.html.twig',
            'bare' => '.view.php',
            default => '.html'
        };
    }
}