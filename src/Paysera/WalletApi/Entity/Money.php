<?php

/**
 * Entity representing money
 */
class Paysera_WalletApi_Entity_Money
{
    /**
     * @var string
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * Creates object instance. Used for fluent interface
     *
     * @return self
     */
    static public function create()
    {
        return new self();
    }

    /**
     * Create zero Money
     *
     * @param string $currency
     *
     * @return self
     */
    static public function createZero($currency)
    {
        return new self('0', $currency);
    }

    /**
     * Constructs object
     *
     * @param string $amount
     * @param string $currency
     *
     * @throws InvalidArgumentException
     */
    public function __construct($amount = null, $currency = null)
    {
        if ($amount !== null) {
            $this->setAmount($amount);
        }
        if ($currency !== null) {
            $this->setCurrency($currency);
        }
    }

    /**
     * Sets amount
     *
     * @param string $amount    Amount in base currency units. Cents separated with dot (".")
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setAmount($amount)
    {
        if (!is_scalar($amount)) {
            throw new InvalidArgumentException('Amount must be string');
        }
        $amount = (string) $amount;
        if (!preg_match('/^\-?\d+(.\d+)?$/', $amount)) {
            throw new InvalidArgumentException('Invalid amount string');
        }
        if (strpos($amount, '.') !== false) {
            $amount = rtrim(rtrim($amount, '0'), '.');
        }
        $amount = preg_replace('/^(\-?)0+(\d)/', '$1$2', $amount);

        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return string|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setCurrency($currency)
    {
        if (!is_string($currency)) {
            throw new InvalidArgumentException('Currency must be string');
        } elseif (strlen($currency) !== 3) {
            throw new InvalidArgumentException('Invalid currency');
        }
        $this->currency = strtoupper($currency);
        return $this;
    }

    /**
     * Get currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets amount in cents
     *
     * @param integer $amountInCents
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setAmountInCents($amountInCents)
    {
        if ((string) intval($amountInCents) !== (string) $amountInCents) {
            throw new InvalidArgumentException('Amount must be integer');
        }
        $amountInCents = ($amountInCents < 0 ? '-' : '') . '00' . (string)abs($amountInCents);
        $amount = substr($amountInCents, 0, -2) . '.' . substr($amountInCents, -2);
        $this->setAmount($amount);
        return $this;
    }

    /**
     * Gets amount in cents. If there are more than 2 digits after dot, cents are rounded.
     * Cent is 1/100 of base unit.
     *
     * @return integer|null
     */
    public function getAmountInCents()
    {
        if ($this->amount === null) {
            return null;
        } else {
            $parts = explode('.', $this->amount, 2);
            if (count($parts) === 1) {
                return intval($parts[0]) * 100;
            } else {
                $centsPart = substr($parts[1] . '00', 0, 3);
                $cents = intval(round(intval($centsPart, 10), -1) / 10);
                if (substr($this->amount, 0, 1) === '-') {
                    $cents *= -1;
                }
                return intval($parts[0]) * 100 + $cents;
            }
        }
    }

    /**
     * Formats money amount. Returns empty string if amount is null
     *
     * @param integer $digits
     * @param string  $separator
     *
     * @return string
     */
    public function formatAmount($digits = 2, $separator = '.')
    {
        if ($this->amount === null) {
            return '';
        }
        $parts = explode('.', $this->amount, 2);
        if ($digits <= 0) {
            return $parts[0];
        } else {
            if (count($parts) === 1) {
                $parts[1] = '0';
            }
            return $parts[0] . $separator . substr(str_pad($parts[1], $digits, '0'), 0, $digits);
        }
    }

    /**
     * Is same currency
     *
     * @param self $money
     *
     * @return boolean
     */
    public function isSameCurrency(Paysera_WalletApi_Entity_Money $money)
    {
        return $this->getCurrency() == $money->getCurrency();
    }

    /**
     * Checks for equality
     *
     * @param self $money
     *
     * @return boolean
     */
    public function isEqual(Paysera_WalletApi_Entity_Money $money)
    {
        return $this->amount === $money->amount && ($this->currency === $money->currency || $this->amount === '0');
    }

    /**
     * toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->formatAmount() . ' ' . $this->getCurrency();
    }

}
