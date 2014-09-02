<?php

/**
 * Entity representing payment commission
 */
class Paysera_WalletApi_Entity_Commission
{
    /**
     * @var Paysera_WalletApi_Entity_Money $outCommission
     */
    protected $outCommission;

    /**
     * @var Paysera_WalletApi_Entity_Money $inCommission
     */
    protected $inCommission;

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    static public function create()
    {
        return new static();
    }

    /**
     * Set out commission
     *
     * @param Paysera_WalletApi_Entity_Money $outCommission
     *
     * @return self
     */
    public function setOutCommission(Paysera_WalletApi_Entity_Money $outCommission)
    {
        $this->outCommission = $outCommission;

        return $this;
    }

    /**
     * Get out commission
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getOutCommission()
    {
        return $this->outCommission;
    }

    /**
     * Set in commission
     *
     * @param Paysera_WalletApi_Entity_Money $inCommission
     *
     * @return self
     */
    public function setInCommission(Paysera_WalletApi_Entity_Money $inCommission)
    {
        $this->inCommission = $inCommission;

        return $this;
    }

    /**
     * Get in commission
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getInCommission()
    {
        return $this->inCommission;
    }
}
