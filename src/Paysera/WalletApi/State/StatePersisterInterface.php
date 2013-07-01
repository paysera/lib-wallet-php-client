<?php

interface Paysera_WalletApi_State_StatePersisterInterface
{
    /**
     * Saves parameter
     *
     * @param string $name
     * @param mixed  $value
     */
    public function saveParameter($name, $value);

    /**
     * Gets saved parameter
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter($name, $default = null);
}