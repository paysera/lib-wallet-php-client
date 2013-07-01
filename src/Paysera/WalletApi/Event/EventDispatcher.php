<?php

/**
 * Class for dispatching events.
 * For simplicity, does not support subscribers and passes the subject itself, not the event object
 */
class Paysera_WalletApi_Event_EventDispatcher
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Constructs object
     *
     * @param array $listeners keys are event descriptors, values are callables
     */
    public function __construct(array $listeners = array())
    {
        foreach ($listeners as $eventKey => $listener) {
            $this->addListener($eventKey, $listener);
        }
    }

    /**
     * Dispatches event to all listeners
     *
     * @param string $eventKey
     * @param object $subject
     *
     * @return boolean whether at least one listener was registered
     */
    public function dispatch($eventKey, $subject)
    {
        $listeners = isset($this->listeners[$eventKey]) ? $this->listeners[$eventKey] : array();

        if (count($listeners) === 0) {
            return false;
        } else {
            foreach ($listeners as $listener) {
                call_user_func($listener, $subject);
            }
            return true;
        }
    }

    /**
     * Adds listener to some event. Does not replace existing - just adds new one to the end of queue
     *
     * @param string   $eventKey
     * @param callable $listener
     *
     * @throws Paysera_WalletApi_Exception_ConfigurationException
     */
    public function addListener($eventKey, $listener)
    {
        if (!is_callable($listener)) {
            throw new Paysera_WalletApi_Exception_ConfigurationException('Listener must be a callable');
        }
        $this->listeners[$eventKey][] = $listener;
    }
}