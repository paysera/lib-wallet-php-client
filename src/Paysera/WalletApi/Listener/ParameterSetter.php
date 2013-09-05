<?php


/**
 * ParameterSetter
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Listener_ParameterSetter implements Paysera_WalletApi_EventDispatcher_EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param Paysera_WalletApi_Event_RequestEvent $event
     */
    public function onBeforeRequest(Paysera_WalletApi_Event_RequestEvent $event)
    {
        $options = $event->getOptions();
        $parameters = isset($options['parameters']) ? $options['parameters'] : array();
        $options['parameters'] = $this->parameters + $parameters;
        $event->setOptions($options);
    }

    public static function getSubscribedEvents()
    {
        return array(
            Paysera_WalletApi_Events::BEFORE_REQUEST => array('onBeforeRequest', 100),
        );
    }

}