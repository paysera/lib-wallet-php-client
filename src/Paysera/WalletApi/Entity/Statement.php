<?php


/**
 * Statement
 */
class Paysera_WalletApi_Entity_Statement
{
    const TYPE_TRANSFER = 'transfer';
    const TYPE_COMMISSION = 'commission';
    const TYPE_CURRENCY = 'currency';
    const TYPE_TAX = 'tax';
    const TYPE_RETURN = 'return';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Paysera_WalletApi_Entity_Money
     */
    protected $amount;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $details;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Paysera_WalletApi_Entity_Statement_Party
     */
    protected $otherParty;

    /**
     * @var integer
     */
    protected $transferId;

    /**
     * Gets id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets amount
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Gets date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Gets details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Gets otherParty
     *
     * @return Paysera_WalletApi_Entity_Statement_Party
     */
    public function getOtherParty()
    {
        return $this->otherParty;
    }

    /**
     * Gets transferId
     *
     * @return int
     */
    public function getTransferId()
    {
        return $this->transferId;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


}