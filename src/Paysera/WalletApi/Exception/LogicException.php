<?php

/**
 * Thrown when library is used not as it should be. For example, if trying to create payment which already has an ID
 */
class Paysera_WalletApi_Exception_LogicException extends Paysera_WalletApi_Exception_ApiException
{

}