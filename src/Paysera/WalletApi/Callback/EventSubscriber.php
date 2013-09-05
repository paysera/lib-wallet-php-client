<?php

/**
 * Can be used as base class for your own callback event subscriber
 */
abstract class Paysera_WalletApi_Callback_EventSubscriber
    implements Paysera_WalletApi_EventDispatcher_EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            'transaction.failed' => 'onTransactionFailed',
            'transaction.rejected' => 'onTransactionRejected',
            'transaction.reserved' => 'onTransactionReserved',
            'transaction.waiting_funds' => 'onTransactionWaitingFunds',
            'transaction.confirmed' => 'onTransactionConfirmed',
            'transaction.waiting_registration' => 'onTransactionWaitingRegistration',
            'transaction.waiting_password' => 'onTransactionWaitingPassword',
        );
    }

    /**
     * Gets called when transaction has failed
     *
     * @param Paysera_WalletApi_Event_TransactionEvent $event
     */
    public function onTransactionFailed(Paysera_WalletApi_Event_TransactionEvent $event)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when transaction has been rejected
     *
     * @param Paysera_WalletApi_Event_TransactionEvent $event
     */
    public function onTransactionRejected(Paysera_WalletApi_Event_TransactionEvent $event)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved
     *
     * @param Paysera_WalletApi_Event_TransactionEvent $event
     */
    public function onTransactionReserved(Paysera_WalletApi_Event_TransactionEvent $event)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when transaction is reserved with missing funds
     *
     * @param Paysera_WalletApi_Event_TransactionEvent $event
     */
    public function onTransactionWaitingFunds(Paysera_WalletApi_Event_TransactionEvent $event)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved and transaction was confirmed automatically
     *
     * @param Paysera_WalletApi_Event_TransactionEvent $event
     */
    public function onTransactionConfirmed(Paysera_WalletApi_Event_TransactionEvent $event)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved, but at least one of beneficiaries is not yet registered
     *
     * @param Paysera_WalletApi_Event_TransactionEvent $event
     */
    public function onTransactionWaitingRegistration(Paysera_WalletApi_Event_TransactionEvent $event)
    {
        // does nothing - method for overriding
    }

    /**
     * Gets called when money for transaction has been reserved, but at least one payment password is pending
     *
     * @param Paysera_WalletApi_Event_TransactionEvent $event
     */
    public function onTransactionWaitingPassword(Paysera_WalletApi_Event_TransactionEvent $event)
    {
        // does nothing - method for overriding
    }
}