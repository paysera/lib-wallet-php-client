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
     * @param int $walletId
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
     * @param int $applicationClientId
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
     * @param int $clientId
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
