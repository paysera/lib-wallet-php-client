<?php


/**
 * MacAccessTokenEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Event_MacAccessTokenEvent extends Paysera_WalletApi_EventDispatcher_Event
{
    /**
     * @var Paysera_WalletApi_Entity_MacAccessToken
     */
    protected $token;

    /**
     * @param Paysera_WalletApi_Entity_MacAccessToken $token
     */
    public function __construct(Paysera_WalletApi_Entity_MacAccessToken $token)
    {
        $this->token = $token;
    }

    /**
     * Gets token
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     */
    public function getToken()
    {
        return $this->token;
    }

} 