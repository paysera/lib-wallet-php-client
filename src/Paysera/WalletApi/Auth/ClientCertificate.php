<?php

/**
 * Signs requests by adding certificate info before sending them to API
 */
class Paysera_WalletApi_Auth_ClientCertificate implements Paysera_WalletApi_Auth_SignerInterface
{
    const HEADER_PREFIX = 'Wallet-Api-';

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
     * @param array                          $parameters
     */
    public function signRequest(Paysera_WalletApi_Http_Request $request, array $parameters = array())
    {
        $request->setClientCertificate(clone $this->clientCertificate);

        foreach ($parameters as $name => $value) {
            $name = implode('-', array_map('ucfirst', explode('-', str_replace('_', '-', $name))));
            $request->setHeader(self::HEADER_PREFIX . $name, $value);
        }
    }
}