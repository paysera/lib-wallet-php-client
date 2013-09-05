<?php

/**
 * Interface for web client
 */
interface Paysera_WalletApi_Http_ClientInterface
{

    /**
     * Makes request to remote server
     *
     * @param Paysera_WalletApi_Http_Request $request
     *
     * @return Paysera_WalletApi_Http_Response
     *
     * @throws Paysera_WalletApi_Exception_HttpException
     * @throws Paysera_WalletApi_Exception_ConfigurationException
     */
    public function makeRequest(Paysera_WalletApi_Http_Request $request);

}