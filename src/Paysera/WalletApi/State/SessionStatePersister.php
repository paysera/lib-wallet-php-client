<?php

class Paysera_WalletApi_State_SessionStatePersister implements Paysera_WalletApi_State_StatePersisterInterface
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * Constructs object
     *
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Saves parameter
     *
     * @param string $name
     * @param mixed  $value
     */
    public function saveParameter($name, $value)
    {
        $_SESSION[$this->prefix][$name] = $value;
    }

    /**
     * Gets saved parameter
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter($name, $default = null)
    {
        return isset($_SESSION[$this->prefix][$name]) ? $_SESSION[$this->prefix][$name] : $default;
    }
}