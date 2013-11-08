<?php


class Paysera_WalletApi_EventDispatcher_EventDispatcher
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var array
     */
    protected $sorted = array();

    /**
     * @var Paysera_WalletApi_EventDispatcher_EventDispatcher[]
     */
    protected $relatedDispatchers = array();

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
     * @param string                                  $eventName
     * @param Paysera_WalletApi_EventDispatcher_Event $event
     *
     * @return boolean whether at least one listener was registered
     */
    public function dispatch($eventName, Paysera_WalletApi_EventDispatcher_Event $event = null)
    {
        if (null === $event) {
            $event = new Paysera_WalletApi_EventDispatcher_Event();
        }

        $event->setDispatcher($this);
        $event->setName($eventName);

        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        if (!isset($this->sorted[$eventName])) {
            $this->sortListeners($eventName);
        }

        foreach ($this->sorted[$eventName] as $listener) {
            call_user_func($listener, $event);
            if ($event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    /**
     * Adds listener to some event. Does not replace existing - just adds new one to the end of queue
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority
     *
     * @throws Paysera_WalletApi_Exception_ConfigurationException
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        if (!is_callable($listener)) {
            throw new Paysera_WalletApi_Exception_ConfigurationException('Listener must be a callable');
        }
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);

        foreach ($this->relatedDispatchers as $dispatcher) {
            $dispatcher->addListener($eventName, $listener, $priority);
        }
    }

    /**
     * @param Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher
     */
    public function mergeDispatcher(Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher)
    {
        $dispatcher->relatedDispatchers[] = $this;
        foreach ($dispatcher->listeners as $eventName => $priorities) {
            foreach ($priorities as $priority => $listeners) {
                foreach ($listeners as $listener) {
                    $this->addListener($eventName, $listener, $priority);
                }
            }
        }
    }

    /**
     * @see EventDispatcherInterface::addSubscriber
     *
     * @api
     */
    public function addSubscriber(Paysera_WalletApi_EventDispatcher_EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, array($subscriber, $params));
            } elseif (is_string($params[0])) {
                $this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
     * Sorts the internal list of listeners for the given event by priority.
     *
     * @param string $eventName The name of the event.
     */
    private function sortListeners($eventName)
    {
        $this->sorted[$eventName] = array();

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }
}
