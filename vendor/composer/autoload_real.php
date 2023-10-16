<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitf4d84672a3e96d0f5bc5f28f3465cdbb
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitf4d84672a3e96d0f5bc5f28f3465cdbb', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitf4d84672a3e96d0f5bc5f28f3465cdbb', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitf4d84672a3e96d0f5bc5f28f3465cdbb::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
