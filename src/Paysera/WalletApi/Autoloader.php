<?php

/**
 * Autoloader for Paysera WalletApi classes
 */
class Paysera_WalletApi_Autoloader
{
    /**
     * Registers GatewayClient_Autoloader as an SPL autoloader.
     */
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array('Paysera_WalletApi_Autoloader', 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    static public function autoload($class)
    {
        if (0 !== strpos($class, 'Paysera_WalletApi')) {
            return;
        }

        if (file_exists($file = dirname(__FILE__) . '/../../' . str_replace('_', '/', $class) . '.php')) {
            require $file;
        }
    }
}