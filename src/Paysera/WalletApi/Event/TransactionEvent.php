<?php


/**
 * RequestEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Event_TransactionEvent extends Paysera_WalletApi_EventDispatcher_Event
{
    /**
     * @var Paysera_WalletApi_Entity_Transaction
     */
    protected $transaction;

    /**
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     */
    public function __construct(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Gets transaction
     *
     * @return Paysera_WalletApi_Entity_Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

} 