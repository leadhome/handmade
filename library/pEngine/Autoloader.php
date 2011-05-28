<?php

class pEngine_Autoloader
{
    /**
     * Is registered pEngine autoload
     *
     * @var boolean
     */
    protected static $_isRegistered;

    /**
     * pEngine path
     *
     * @var string
     */
    protected static $_pEnginePath;

    /**
     * Autoload callback
     *
     * @var array
     */
    protected static $_callback = array('pEngine_Autoloader', 'load');

    /**
     * Register pEngine autoload
     *
     * @return boolean
     */
    public static function register()
    {
        if (self::isRegistered()) {
            return false;
        }

        self::$_isRegistered = spl_autoload_register(self::$_callback);

        return self::$_isRegistered;
    }

    /**
     * Unregister pEngine autoload
     *
     * @return boolean
     */
    public static function unregister()
    {
        if (!self::isRegistered()) {
            return false;
        }

        self::$_isRegistered = !spl_autoload_unregister(self::$_callback);

        return self::$_isRegistered;
    }

    /**
     * Is pEngine autoload registered
     *
     * @return boolean
     */
    public static function isRegistered()
    {
        return self::$_isRegistered;
    }

    /**
     * Load class
     *
     * @param string $className
     */
    public static function load($className)
    {
        if (0 !== strpos($className, 'pEngine')) {
            return false;
        }

        $path = self::getpEnginePath() . '/' . str_replace('_', '/', $className) . '.php';

        return include $path;
    }

    /**
     * Get pEngine path
     *
     * @return string
     */
    public static function getpEnginePath()
    {
        if (!self::$_pEnginePath) {
            self::$_pEnginePath = realpath(dirname(__FILE__) . '/..');
        }

        return self::$_pEnginePath;
    }
}