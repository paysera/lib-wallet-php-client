<?php

class Paysera_WalletApi_Entity_TransferConfiguration
{
    /**
     * @var int
     */
    private $walletId;

    /**
     * @var int
     */
    private $applicationClientId;

    /**
     * @var int
     */
    private $clientId;

    /**
     * @param $walletId int
     * @return $this
     */
    public function setWalletId($walletId)
    {
        $this->walletId = $walletId;

        return $this;
    }

    /**
     * @return int
     */
    public function getWalletId()
    {
        return $this->walletId;
    }

    /**
     * @param $applicationClientId int
     * @return $this
     */
    public function setApplicationClientId($applicationClientId)
    {
        $this->applicationClientId = $applicationClientId;

        return $this;
    }

    /**
     * @return int
     */
    public function getApplicationClientId()
    {
        return $this->applicationClientId;
    }

    /**
     * @param $clientId int
     * @return $this
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }
}
