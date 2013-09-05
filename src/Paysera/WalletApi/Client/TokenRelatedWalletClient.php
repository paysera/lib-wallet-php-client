<?php

class Paysera_WalletApi_Client_TokenRelatedWalletClient extends Paysera_WalletApi_Client_WalletClient
{
    /**
     * @var Paysera_WalletApi_Entity_MacAccessToken
     */
    protected $currentAccessToken;

    /**
     * Gets active allowance for current wallet
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getActiveAllowance()
    {
        return $this->mapper->decodeAllowance($this->get('allowance/active/me'));
    }

    /**
     * @param string $currency
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getActiveAllowanceLimit($currency = 'EUR')
    {
        return parent::getAllowanceLimit('me', $currency);
    }

    /**
     * Tries to accept transaction by sending user's PIN code using API
     *
     * @param string  $transactionKey
     * @param string  $pin
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function acceptTransactionUsingCurrentPin($transactionKey, $pin)
    {
        return $this->acceptTransactionUsingPin($transactionKey, 'me', $pin);
    }

    /**
     * Gets statements for current wallet using API
     *
     * @param Paysera_WalletApi_Entity_Statement_SearchFilter $filter
     *
     * @return Paysera_WalletApi_Entity_Statement_SearchResult|Paysera_WalletApi_Entity_Statement[]
     */
    public function getCurrentWalletStatements(Paysera_WalletApi_Entity_Statement_SearchFilter $filter = null)
    {
        return $this->getWalletStatements('me', $filter);
    }

    /**
     * Tries to accept transaction by active allowance using API
     *
     * @param string                                                $transactionKey
     * @param int|Paysera_WalletApi_Entity_WalletIdentifier|string $payer
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     */
    public function acceptTransactionUsingAllowance($transactionKey, $payer = 'me')
    {
        return parent::acceptTransactionUsingAllowance($transactionKey, $payer);
    }

    public function getAllowanceForWallet($walletId = 'me')
    {
        return parent::getAllowanceForWallet($walletId);
    }

    public function cancelAllowanceForWallet($walletId = 'me')
    {
        return parent::cancelAllowanceForWallet($walletId);
    }

    public function sendTransactionFlashSms($transactionKey, $walletId = 'me')
    {
        return parent::sendTransactionFlashSms($transactionKey, $walletId);
    }

    public function getAvailableTransactionTypes($transactionKey, $walletId = 'me')
    {
        return parent::getAvailableTransactionTypes($transactionKey, $walletId);
    }

    public function getWallet($walletId = 'me')
    {
        return parent::getWallet($walletId);
    }

    public function getWalletBalance($walletId = 'me')
    {
        return parent::getWalletBalance($walletId);
    }

    public function getUser($userId = 'me')
    {
        return parent::getUser($userId);
    }

    public function getUserEmail($userId = 'me')
    {
        return parent::getUserEmail($userId);
    }

    public function getUserPhone($userId = 'me')
    {
        return parent::getUserPhone($userId);
    }

    public function getUserAddress($userId = 'me')
    {
        return parent::getUserAddress($userId);
    }

    public function getUserIdentity($userId = 'me')
    {
        return parent::getUserIdentity($userId);
    }

    public function getUserWallets($userId = 'me')
    {
        return parent::getUserWallets($userId);
    }

    /**
     * Gets currentAccessToken
     *
     * @return \Paysera_WalletApi_Entity_MacAccessToken
     */
    public function getCurrentAccessToken()
    {
        return $this->currentAccessToken;
    }

    /**
     * Only for internal use - will not change the token with which requests are made
     *
     * @param \Paysera_WalletApi_Entity_MacAccessToken $currentAccessToken
     */
    public function setCurrentAccessToken($currentAccessToken)
    {
        $this->currentAccessToken = $currentAccessToken;
    }
}