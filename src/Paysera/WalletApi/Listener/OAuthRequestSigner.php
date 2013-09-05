<?php


/**
 * OAuthRequestSigner
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Listener_OAuthRequestSigner extends Paysera_WalletApi_Listener_RequestSigner
{
    /**
     * @var Paysera_WalletApi_Entity_MacAccessToken
     */
    protected $token;

    /**
     * @var Paysera_WalletApi_Client_OAuthClient
     */
    protected $oauthClient;

    /**
     * @param Paysera_WalletApi_Client_OAuthClient          $oauthClient related with client credentials
     * @param Paysera_WalletApi_Entity_MacAccessToken $token
     */
    public function __construct(
        Paysera_WalletApi_Client_OAuthClient $oauthClient,
        Paysera_WalletApi_Entity_MacAccessToken $token
    ) {
        $this->oauthClient = $oauthClient;
        $this->token = $token;
        $this->signer = new Paysera_WalletApi_Auth_Mac($token->getMacId(), $token->getMacKey());
    }

    /**
     * @param Paysera_WalletApi_Event_ResponseExceptionEvent $event
     */
    public function onResponseException(Paysera_WalletApi_Event_ResponseExceptionEvent $event)
    {
        if ($event->getException()->getErrorCode() === 'invalid_grant') {
            $options = $event->getOptions();
            if (!isset($options['oauth_access_token_retry'])) {
                $options['oauth_access_token_retry'] = true;
                $event->setOptions($options);

                $refreshToken = $this->token->getRefreshToken();
                if ($refreshToken !== null) {
                    $newToken = $this->oauthClient->refreshAccessToken($refreshToken);
                    $this->token = $newToken;
                    $this->signer = new Paysera_WalletApi_Auth_Mac($newToken->getMacId(), $newToken->getMacKey());
                    $event->stopPropagation()->setRepeatRequest(true);

                    $event->getDispatcher()->dispatch(
                        Paysera_WalletApi_Events::AFTER_OAUTH_TOKEN_REFRESH,
                        new Paysera_WalletApi_Event_MacAccessTokenEvent($newToken)
                    );
                }
            }
        }
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

    public static function getSubscribedEvents()
    {
        return parent::getSubscribedEvents() + array(
            Paysera_WalletApi_Events::ON_RESPONSE_EXCEPTION => 'onResponseException',
        );
    }
}