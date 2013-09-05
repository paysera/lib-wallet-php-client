<?php

class Paysera_WalletApi_Util_Assert
{
    public static function isInt($value)
    {
        if ((string)intval($value) !== (string)$value) {
            throw new Paysera_WalletApi_Exception_LogicException('Value must be integer');
        }
    }

    public static function isId($value)
    {
        if ((string)$value !== 'me' && (string)intval($value) !== (string)$value) {
            throw new Paysera_WalletApi_Exception_LogicException('Value must be integer or "me"');
        }
    }

    public static function isIntOrNull($value)
    {
        if ($value !== null && (string)intval($value) !== (string)$value) {
            throw new Paysera_WalletApi_Exception_LogicException('Value must be integer');
        }
    }

    public static function isScalar($value)
    {
        if (!is_scalar($value)) {
            throw new Paysera_WalletApi_Exception_LogicException('Value must be string');
        }
    }
}