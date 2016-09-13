<?php

namespace Eboo\Base;

use Eboo\App;
use Eboo\Response;

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
        $twig = new \Twig_Environment($loader,[
            'cache' => $this->app->getConfig('view.cachePath'),
            'auto_reload' => true,
        ]);
        return (new Response($twig->render($viewName, $values),200));
    }

}