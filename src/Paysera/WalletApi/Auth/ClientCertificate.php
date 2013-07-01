<?php

/**
 * Signs requests by adding certificate info before sending them to API
 */
class Paysera_WalletApi_Auth_ClientCertificate implements Paysera_WalletApi_Auth_SignerInterface
{
    /**
     * @var Paysera_WalletApi_Http_ClientCertificate
     */
    protected $clientCertificate;

    /**
     * Constructs object
     *
     * @param Paysera_WalletApi_Http_ClientCertificate $clientCertificate
     */
    public function __construct(Paysera_WalletApi_Http_ClientCertificate $clientCertificate)
    {
        $this->clientCertificate = $clientCertificate;
    }

    /**
     * Signs request - adds Authorization header with generated value
     *
     * @param Paysera_WalletApi_Http_Request $request
     */
    public function signRequest(Paysera_WalletApi_Http_Request $request)
    {
        $request->setClientCertificate(clone $this->clientCertificate);
    }
}