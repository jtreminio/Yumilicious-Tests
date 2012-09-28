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
        $this->app['debug'] = true;
        unset($this->app['exception_handler']);
        $this->app['session.test'] = true;

        // Use FilesystemSessionStorage to store session
        $this->app['session.storage'] = $this->app->share(function() {
            return new \Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage(sys_get_temp_dir());
        });

        // Services
        require __DIR__ . '/../../../src/services.php';

        // Controllers
        require __DIR__ . '/../../../src/controllers.php';

        return $this->app;
    }
}