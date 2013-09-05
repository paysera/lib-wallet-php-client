<?php


/**
 * AllowanceEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Event_AllowanceEvent extends Paysera_WalletApi_EventDispatcher_Event
{
    /**
     * @var Paysera_WalletApi_Entity_Allowance
     */
    protected $allowance;

    /**
     * @param Paysera_WalletApi_Entity_Allowance $allowance
     */
    public function __construct(Paysera_WalletApi_Entity_Allowance $allowance)
    {
        $this->allowance = $allowance;
    }

    /**
     * Gets allowance
     *
     * @return Paysera_WalletApi_Entity_Allowance
     */
    public function getAllowance()
    {
        return $this->allowance;
    }

} 