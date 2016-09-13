<?php

namespace Eboo\Base;

use Eboo\App;

class Controller
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function view($viewName, array $values = [])
    {
        $loader = new \Twig_Loader_Filesystem($this->app->getConfig('view.templatePath'));
        $twig = new Twig_Environment($loader,[
            'cache' => $this->app->getConfig('view.cachePath')
        ]);
        return $twig->render($viewName, $values);
    }

}