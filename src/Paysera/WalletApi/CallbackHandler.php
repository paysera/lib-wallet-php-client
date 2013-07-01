<?php

class Paysera_WalletApi_CallbackHandler
{
    /**
     * @var Paysera_WalletApi_Auth_CallbackSignChecker
     */
    protected $callbackSignChecker;

    /**
     * @var Paysera_WalletApi_Mapper
     */
    protected $mapper;

    /**
     * Constructs object
     *
     * @param Paysera_WalletApi_Auth_CallbackSignChecker $callbackSignChecker
     * @param Paysera_WalletApi_Mapper                   $mapper
     */
    public function __construct(
        Paysera_WalletApi_Auth_CallbackSignChecker $callbackSignChecker,
        Paysera_WalletApi_Mapper $mapper
    ) {
        $this->callbackSignChecker = $callbackSignChecker;
        $this->mapper = $mapper;
    }

    public function handle($post, Paysera_WalletApi_Event_EventDispatcher $eventDispatcher)
    {
        if (!isset($post['event']) || !isset($post['sign'])) {
            throw new Paysera_WalletApi_Exception_CallbackException('At least one of required parameters is missing');
        }
        $event = $post['event'];
        $sign = $post['sign'];

        if (!$this->callbackSignChecker->checkSign($event, $sign)) {
            throw new Paysera_WalletApi_Exception_CallbackException('Sign validation failed');
        }

        $eventData = json_decode($event, true);

        try {
            if ($eventData['object'] === 'transaction') {
                $subject = $this->mapper->decodeTransaction($eventData['data']);
            } elseif ($eventData['object'] === 'payment') {
                $subject = $this->mapper->decodePayment($eventData['data']);
            } elseif ($eventData['object'] === 'allowance') {
                $subject = $this->mapper->decodeAllowance($eventData['data']);
            } else {
                throw new Paysera_WalletApi_Exception_CallbackUnsupportedException('Unknown event object');
            }
        } catch (Paysera_WalletApi_Exception_CallbackException $exception) {
            throw $exception;                                     // just pass callback exceptions
        } catch (Exception $exception) {
            throw new Paysera_WalletApi_Exception_CallbackException(    // wrap other exceptions to callback exception
                'Exception caught while trying to decode event subject',
                0,
                $exception
            );
        }

        $eventKey = $eventData['object'] . '.' . $eventData['type'];
        if (!$eventDispatcher->dispatch($eventKey, $subject)) {
            throw new Paysera_WalletApi_Exception_CallbackUnsupportedException('Unhandled event');
        }
    }
}