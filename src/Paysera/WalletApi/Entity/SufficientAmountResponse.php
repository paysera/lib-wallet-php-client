<?php

class Paysera_WalletApi_Entity_SufficientAmountResponse
{
    /**
     * @var bool
     */
    private $sufficient;

    public function isSufficient()
    {
        return $this->sufficient;
    }

    /**
     * @param bool $sufficient
     *
     * @return $this
     */
    public function setSufficient($sufficient)
    {
        $this->sufficient = $sufficient;

        return $this;
    }
}
