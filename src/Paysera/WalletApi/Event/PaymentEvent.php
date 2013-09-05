<?php


/**
 * PaymentEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Event_PaymentEvent extends Paysera_WalletApi_EventDispatcher_Event
{
    /**
     * @var Paysera_WalletApi_Entity_Payment
     */
    protected $payment;

    /**
     * @param Paysera_WalletApi_Entity_Payment $payment
     */
    public function __construct(Paysera_WalletApi_Entity_Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Gets payment
     *
     * @return Paysera_WalletApi_Entity_Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

} 