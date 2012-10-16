<?php

namespace Yumilicious\UnitTests;

use Yumilicious\Domain;

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

    /**
     * Sets up the application object to be used in our domain classes
     *
     * @param \PHPUnit_Framework_MockObject_MockObject|Domain $object
     * @return self
     */
    protected final function setApp($object)
    {
        $this->setAttribute($object, 'app', $this->app);

        return $this;
    }

    /**
     * Override service container
     *
     * @param string $name
     * @param object $service
     * @return self
     */
    protected final function setService($name, $service)
    {
        $this->app[$name] = $service;

        return $this;
    }
}