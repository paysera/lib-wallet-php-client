<?php

class Paysera_WalletApi_Callback_Handler
{
    /**
     * @var Paysera_WalletApi_Callback_SignChecker
     */
    protected $callbackSignChecker;

    /**
     * @var Paysera_WalletApi_Mapper
     */
    protected $mapper;

    /**
     * @var Paysera_WalletApi_EventDispatcher_EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Constructs object
     *
     * @param Paysera_WalletApi_EventDispatcher_EventDispatcher $eventDispatcher
     * @param Paysera_WalletApi_Callback_SignChecker            $callbackSignChecker
     * @param Paysera_WalletApi_Mapper                          $mapper
     */
    public function __construct(
        Paysera_WalletApi_EventDispatcher_EventDispatcher $eventDispatcher,
        Paysera_WalletApi_Callback_SignChecker $callbackSignChecker,
        Paysera_WalletApi_Mapper $mapper
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->callbackSignChecker = $callbackSignChecker;
        $this->mapper = $mapper;
    }


    public function handle($post)
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
                $subject = new Paysera_WalletApi_Event_TransactionEvent(
                    $this->mapper->decodeTransaction($eventData['data'])
                );
            } elseif ($eventData['object'] === 'payment') {
                $subject = new Paysera_WalletApi_Event_PaymentEvent(
                    $this->mapper->decodePayment($eventData['data'])
                );
            } elseif ($eventData['object'] === 'allowance') {
                $subject = new Paysera_WalletApi_Event_AllowanceEvent(
                    $this->mapper->decodeAllowance($eventData['data'])
                );
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
        $this->eventDispatcher->dispatch($eventKey, $subject);
    }
}