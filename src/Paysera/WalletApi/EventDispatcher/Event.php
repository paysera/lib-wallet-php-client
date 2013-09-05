<?php


class Paysera_WalletApi_EventDispatcher_Event
{
    /**
     * @var bool Whether no further event listeners should be triggered
     */
    protected $propagationStopped = false;

    /**
     * @var Paysera_WalletApi_EventDispatcher_EventDispatcher Dispatcher that dispatched this event
     */
    protected $dispatcher;

    /**
     * @var string This event's name
     */
    protected $name;

    /**
     * @return bool
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * Stops the propagation of the event
     *
     * @return $this
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
        return $this;
    }

    /**
     * Stores the EventDispatcher that dispatches this Event
     *
     * @param Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher
     *
     * @api
     */
    public function setDispatcher(Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns the EventDispatcher that dispatches this Event
     *
     * @return Paysera_WalletApi_EventDispatcher_EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Gets the event's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the event's name property.
     *
     * @param string $name The event name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
