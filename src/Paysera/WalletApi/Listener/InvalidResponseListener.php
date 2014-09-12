<?php

class Paysera_WalletApi_Listener_InvalidResponseListener implements Paysera_WalletApi_EventDispatcher_EventSubscriberInterface
{
    /**
     * @param Paysera_WalletApi_Event_ResponseExceptionEvent $event
     */
    public function onResponseException(Paysera_WalletApi_Event_ResponseExceptionEvent $event)
    {
        $options = $event->getOptions();

        if (
                $event->getException()->getStatusCode() === 502
            &&  !$event->isRepeatRequest()
            &&  (!isset($options['isRepeated']) || $options['isRepeated'] === false)
        ) {
            $event->setRepeatRequest(true);
            $event->setOptions(array_merge(
                $event->getOptions(),
                array('isRepeated' => true)
            ));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            Paysera_WalletApi_Events::ON_RESPONSE_EXCEPTION => 'onResponseException',
        );
    }
} 