<?php

/**
 * Checks whether callback sign is valid
 */
class Paysera_WalletApi_Callback_SignChecker
{
    /**
     * @var string
     */
    protected $publicKeyUri;

    /**
     * @var Paysera_WalletApi_Http_ClientInterface
     */
    protected $webClient;

    /**
     * Constructs object
     *
     * @param string                          $publicKeyUri
     * @param Paysera_WalletApi_Http_ClientInterface $webClient
     */
    public function __construct($publicKeyUri, Paysera_WalletApi_Http_ClientInterface $webClient)
    {
        $this->publicKeyUri = $publicKeyUri;
        $this->webClient = $webClient;
    }

    /**
     * Checks whether callback sign is valid. Before checking, downloads public key from Wallet server
     *
     * @param string $data
     * @param string $sign
     *
     * @return boolean
     *
     * @throws Paysera_WalletApi_Exception_CallbackException
     */
    public function checkSign($data, $sign)
    {
        return $this->checkSignWithPublicKey($data, $sign, $this->getPublicKey());
    }

    /**
     * Downloads public key
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_CallbackException
     */
    protected function getPublicKey()
    {
        try {
            return $this->webClient->makeRequest(new Paysera_WalletApi_Http_Request($this->publicKeyUri))->getContent();
        } catch (Paysera_WalletApi_Exception_HttpException $exception) {
            throw new Paysera_WalletApi_Exception_CallbackException(
                'Cannot get public key from Wallet server',
                0,
                $exception
            );
        }
    }

    /**
     * Checks whether callback sign is valid, providing public key to use
     *
     * @param string $data
     * @param string $sign
     * @param string $publicKey
     *
     * @return boolean
     *
     * @throws Paysera_WalletApi_Exception_CallbackException
     */
    protected function checkSignWithPublicKey($data, $sign, $publicKey)
    {
        while (openssl_error_string()) {
            // empty error buffer
        }
        $result = openssl_verify(hash('sha256', $data, true), base64_decode($sign), $publicKey);
        if ($result === -1) {
            throw new Paysera_WalletApi_Exception_CallbackException(
                'OpenSSL error, probably incorrect public key from Wallet system: ' . openssl_error_string()
            );
        } else {
            return $result === 1;
        }
    }

}