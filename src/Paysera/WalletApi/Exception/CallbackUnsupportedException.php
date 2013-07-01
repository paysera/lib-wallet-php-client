<?php

/**
 * Thrown if callback is recognised and signed properly, but event type or object is unknown.
 * You can choose to catch exceptions of this type and provide successful response to skip repeating the callback.
 */
class Paysera_WalletApi_Exception_CallbackUnsupportedException extends Paysera_WalletApi_Exception_CallbackException
{

}