<?php

/**
 * Can be used as base class for your own callback event handler.
 */
abstract class Paysera_WalletApi_Event_BaseCallbackEventDispatcher extends Paysera_WalletApi_Event_EventDispatcher
{
    /**
     * Constructs object
     */
    public function __construct()
    {
        parent::__construct(array(
            'transaction.failed' => array($this, 'onTransactionFailed'),
            'transaction.rejected' => array($this, 'onTransactionRejected'),
            'transaction.reserved' => array($this, 'onTransactionReserved'),
            'transaction.waiting_funds' => array($this, 'onTransactionWaitingFunds'),
            'transaction.confirmed' => array($this, 'onTransactionConfirmed'),
            'transaction.waiting_registration' => array($this, 'onTransactionWaitingRegistration'),
        ));
    }

    /**
     * Gets called when transaction has failed
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     */
    public function onTransactionFailed(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when transaction has been rejected
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     */
    public function onTransactionRejected(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     */
    public function onTransactionReserved(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when transaction is reserved with missing funds
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     */
    public function onTransactionWaitingFunds(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved and transaction was confirmed automatically
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     */
    public function onTransactionConfirmed(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved, but at least one of beneficiaries is not yet registered
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     */
    public function onTransactionWaitingRegistration(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        // does nothing - method for overriding
    }
}