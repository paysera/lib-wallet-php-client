<?php
/**
 * Created by: Gediminas Samulis
 * Date: 2013-12-18
 */

class Paysera_WalletApi_Entity_Restrictions
{
    /**
     * @var Paysera_WalletApi_Entity_Restriction_UserRestriction
     */
    protected $accountOwnerRestriction;

    /**
     * Creates object, used for fluent interface
     *
     * @return static
     */
    static public function create()
    {
        return new static();
    }

    /**
     * @param \Paysera_WalletApi_Entity_Restriction_UserRestriction $accountOwnerRestriction
     *
     * @return $this
     */
    public function setAccountOwnerRestriction($accountOwnerRestriction)
    {
        $this->accountOwnerRestriction = $accountOwnerRestriction;

        return $this;
    }

    /**
     * @return \Paysera_WalletApi_Entity_Restriction_UserRestriction
     */
    public function getAccountOwnerRestriction()
    {
        return $this->accountOwnerRestriction;
    }
}