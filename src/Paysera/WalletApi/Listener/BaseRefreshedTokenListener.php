<?php


/**
 * BaseRefreshedTokenListener
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
abstract class Paysera_WalletApi_Listener_BaseRefreshedTokenListener
    implements Paysera_WalletApi_EventDispatcher_EventSubscriberInterface
{

    /**
     * @param Paysera_WalletApi_Event_MacAccessTokenEvent $event
     */
    public function onTokenRefresh(Paysera_WalletApi_Event_MacAccessTokenEvent $event)
    {
        // implement in subclasses
    }

    public static function getSubscribedEvents()
    {
        return array(
            Paysera_WalletApi_Events::AFTER_OAUTH_TOKEN_REFRESH => 'onTokenRefresh',
        );
    }
} 