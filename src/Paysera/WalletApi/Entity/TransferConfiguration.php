<?php

class Paysera_WalletApi_Entity_TransferConfiguration
{
    /**
     * @var int|null
     */
    private $walletId;

    /**
     * @var int|null
     */
    private $applicationClientId;

    /**
     * @var int|null
     */
    private $clientId;

    public function __construct()
    {
        $this->walletId = null;
        $this->applicationClientId = null;
        $this->clientId = null;
    }

    /**
     * @param int $walletId
     * @return $this
     */
    public function setWalletId($walletId)
    {
        $this->walletId = $walletId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWalletId()
    {
        return $this->walletId;
    }

    /**
     * @param int $applicationClientId
     * @return $this
     */
    public function setApplicationClientId($applicationClientId)
    {
        $this->applicationClientId = $applicationClientId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getApplicationClientId()
    {
        return $this->applicationClientId;
    }

    /**
     * @param int $clientId
     * @return $this
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClientId()
    {
        return $this->clientId;
    }
}
