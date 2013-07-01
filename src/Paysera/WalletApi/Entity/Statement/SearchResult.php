<?php


/**
 * Result
 */
class Paysera_WalletApi_Entity_Statement_SearchResult extends Paysera_WalletApi_Entity_Search_Result
{
    /**
     * @var Paysera_WalletApi_Entity_Statement[]
     */
    protected $statements = array();

    /**
     * @return array
     */
    public function getResultList()
    {
        return $this->statements;
    }

}