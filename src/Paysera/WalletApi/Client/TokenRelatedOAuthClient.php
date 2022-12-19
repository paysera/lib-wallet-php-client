<?php

class Paysera_WalletApi_Client_TokenRelatedOAuthClient extends Paysera_WalletApi_Client_OAuthClient
{
    /**
     * @var Paysera_WalletApi_Entity_MacAccessToken
     */
    protected $currentAccessToken;

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


    public function deleteAccessToken($token)
    {
        Paysera_WalletApi_Util_Assert::isScalar($token);
        $this->delete(sprintf('token?access_token=%s', $token));
    }
}
