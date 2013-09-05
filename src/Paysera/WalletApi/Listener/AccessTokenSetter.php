<?php


/**
 * AccessTokenSetter
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Listener_AccessTokenSetter extends Paysera_WalletApi_Listener_BaseRefreshedTokenListener
{
    /**
     * @var Paysera_WalletApi_Client_TokenRelatedWalletClient
     */
    protected $tokenRelatedClient;

    /**
     * @param $tokenRelatedClient
     */
    public function __construct($tokenRelatedClient)
    {
        $this->tokenRelatedClient = $tokenRelatedClient;
    }

    /**
     * @param Paysera_WalletApi_Event_MacAccessTokenEvent $event
     */
    public function onTokenRefresh(Paysera_WalletApi_Event_MacAccessTokenEvent $event)
    {
        $this->tokenRelatedClient->setCurrentAccessToken($event->getToken());
    }

} 