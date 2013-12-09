<?php

/**
 * PriceRules
 */
class Paysera_WalletApi_Entity_PriceRules
{
    /**
     * @var Paysera_WalletApi_Entity_Money
     */
    protected $min;

    /**
     * @var Paysera_WalletApi_Entity_Money
     */
    protected $max;

    /**
     * @var Paysera_WalletApi_Entity_Money[]
     */
    protected $choices = array();

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    static public function create()
    {
        return new self();
    }

    /**
     * @param Paysera_WalletApi_Entity_Money $min
     *
     * @return self
     */
    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param Paysera_WalletApi_Entity_Money $max
     *
     * @return self
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param Paysera_WalletApi_Entity_Money $choice
     *
     * @return self
     */
    public function addChoice($choice)
    {
        $this->choices[] = $choice;
        return $this;
    }

    /**
     * @param Paysera_WalletApi_Entity_Money[] $choices
     *
     * @return self
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Money[]
     */
    public function getChoices()
    {
        return $this->choices;
    }
} 
