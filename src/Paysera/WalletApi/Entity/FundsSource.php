<?php

/**
 * Entity representing payment funds source
 */
class Paysera_WalletApi_Entity_FundsSource
{
    const TYPE_BUY  = 'buy';
    const TYPE_SELL  = 'sell';
    const TYPE_LOAN  = 'loan';
    const TYPE_GIFT  = 'gift';
    const TYPE_INHERITANCE  = 'inheritance';
    const TYPE_DIVIDENDS  = 'dividends';

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $details
     */
    protected $details;

    /**
     * Set type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return $this
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }
}
