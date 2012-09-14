<?php

namespace Yumilicious\UnitTests;

class Base extends \jtreminio\TestExtensions\TestExtensionsSilex
{
    /** @var \Silex\Application */
    protected $app;

    public function createApplication()
    {
        // Silex
        $this->app = require __DIR__ . '/../../../src/app.php';

        // Tests mode
        unset($this->app['exception_handler']);

        // Services
        require __DIR__ . '/../../../src/services.php';

        // Controllers
        require __DIR__ . '/../../../src/controllers.php';

        return $this->app;
    }
}