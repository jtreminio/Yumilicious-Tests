<?php

namespace Yumilicious\IntegrationTests;

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

        $this->app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options'   => array(
                'driver'   => 'pdo_mysql',
                'host'     => 'localhost',
                'dbname'   => 'yumiliciousTests',
                'user'     => 'root',
                'password' => '',
            ),
        ));

        // Services
        require __DIR__ . '/../../../src/services.php';

        // Controllers
        require __DIR__ . '/../../../src/controllers.php';

        return $this->app;
    }

    public static function setUpBeforeClass()
    {
        /**
         * Requires table yumiliciousTests to exist. Drops all data from this table and clones yumilicious into it
         */
        exec(
            'mysqldump -u root --no-data --add-drop-table yumiliciousTests | ' .
            'grep ^DROP | ' .
            'mysql -u root yumiliciousTests && ' .
            'mysqldump -u root yumilicious | ' .
            'mysql -u root yumiliciousTests'
        );
    }
}