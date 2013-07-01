<?php

/**
 * Entity representing Transaction
 */
class Paysera_WalletApi_Entity_Transaction
{
    const STATUS_NEW = 'new';
    const STATUS_WAITING = 'waiting';
    const STATUS_WAITING_REGISTRATION = 'waiting_registration';
    const STATUS_WAITING_FUNDS = 'waiting_funds';
    const STATUS_RESERVED = 'reserved';
    const STATUS_DELETED = 'deleted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVOKED = 'revoked';
    const STATUS_FAILED = 'failed';
    const STATUS_CONFIRMED = 'confirmed';

    const TYPE_AUTOMATIC = 'automatic';
    const TYPE_PAGE = 'page';
    const TYPE_FLASH = 'flash';
    const TYPE_PIN = 'pin';

    /**
     * @var string    read-only
     */
    protected $key;

    /**
     * @var DateTime    read-only
     */
    protected $createdAt;

    /**
     * @var string    read-only
     */
    protected $status;

    /**
     * @var string    read-only
     */
    protected $type;

    /**
     * @var integer    read-only
     */
    protected $wallet;

    /**
     * @var DateTime    read-only
     */
    protected $confirmedAt;

    /**
     * @var string    read-only
     */
    protected $correlationKey;

    /**
     * @var Paysera_WalletApi_Entity_Payment[]
     */
    protected $payments = array();

    /**
     * @var integer[]
     */
    protected $paymentIdList = array();

    /**
     * @var Paysera_WalletApi_Entity_Allowance
     */
    protected $allowance;

    /**
     * @var integer
     */
    protected $allowanceId;

    /**
     * @var boolean
     */
    protected $allowanceOptional = false;

    /**
     * @var boolean
     */
    protected $useAllowance = true;

    /**
     * @var boolean
     */
    protected $suggestAllowance = false;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var string
     */
    protected $callbackUri;

    /**
     * @var boolean
     */
    protected $callbacksDisabled = false;

    /**
     * @var integer
     */
    protected $reserveFor;

    /**
     * @var DateTime
     */
    protected $reserveUntil;

    /**
     * @var Paysera_WalletApi_Entity_UserInformation
     */
    protected $userInformation;

    /**
     * @var boolean
     */
    protected $autoConfirm;

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
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusWaiting()
    {
        return $this->status === self::STATUS_WAITING;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusWaitingRegistration()
    {
        return $this->status === self::STATUS_WAITING_REGISTRATION;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusWaitingFunds()
    {
        return $this->status === self::STATUS_WAITING_FUNDS;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusReserved()
    {
        return $this->status === self::STATUS_RESERVED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusRevoked()
    {
        return $this->status === self::STATUS_REVOKED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Checks transfer status
     *
     * @return boolean
     */
    public function isStatusDeleted()
    {
        return $this->status === self::STATUS_DELETED;
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
     * Checks transaction type
     *
     * @return boolean
     */
    public function isTypeAutomatic()
    {
        return $this->type === self::TYPE_AUTOMATIC;
    }

    /**
     * Checks transaction type
     *
     * @return boolean
     */
    public function isTypeFlash()
    {
        return $this->type === self::TYPE_FLASH;
    }

    /**
     * Checks transaction type
     *
     * @return boolean
     */
    public function isTypePage()
    {
        return $this->type === self::TYPE_PAGE;
    }

    /**
     * Checks transaction type
     *
     * @return boolean
     */
    public function isTypePin()
    {
        return $this->type === self::TYPE_PIN;
    }

    /**
     * Sets wallet
     *
     * @param integer $wallet

     * @return self
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;
        return $this;
    }

    /**
     * Gets wallet
     *
     * @return integer
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * Get confirmedAt
     *
     * @return DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * Get correlationKey
     *
     * @return string
     */
    public function getCorrelationKey()
    {
        return $this->correlationKey;
    }

    /**
     * Add payment
     *
     * @param Paysera_WalletApi_Entity_Payment $payment
     *
     * @return self
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function addPayment(Paysera_WalletApi_Entity_Payment $payment)
    {
        if ($this->getKey() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot add payment to already saved transaction');
        }
        $this->payments[] = $payment;
        return $this;
    }

    /**
     * Get payments
     *
     * @return Paysera_WalletApi_Entity_Payment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Adds payment to transaction by it's ID
     *
     * @param integer $paymentId
     *
     * @return self
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function addPaymentId($paymentId)
    {
        if ($this->getKey() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot add payment to already saved transaction');
        }
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $this->paymentIdList[] = $paymentId;
        return $this;
    }

    /**
     * Gets paymentIdList
     *
     * @return integer[]
     */
    public function getPaymentIdList()
    {
        return $this->paymentIdList;
    }

    /**
     * Set allowance
     *
     * @param Paysera_WalletApi_Entity_Allowance $allowance
     *
     * @return self
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function setAllowance(Paysera_WalletApi_Entity_Allowance $allowance)
    {
        if ($this->getKey() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot set allowance to already saved transaction');
        }
        $this->allowance = $allowance;
        $this->allowanceId = null;
        return $this;
    }

    /**
     * Get allowance
     *
     * @return Paysera_WalletApi_Entity_Allowance
     */
    public function getAllowance()
    {
        return $this->allowance;
    }

    /**
     * Sets allowance by it's ID
     *
     * @param integer $allowanceId
     *
     * @return self
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function setAllowanceId($allowanceId)
    {
        if ($this->getKey() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot set allowance to already saved transaction');
        }
        Paysera_WalletApi_Util_Assert::isInt($allowanceId);
        $this->allowanceId = $allowanceId;
        $this->allowance = null;
        return $this;
    }

    /**
     * Gets allowanceId
     *
     * @return integer
     */
    public function getAllowanceId()
    {
        return $this->allowanceId;
    }

    /**
     * Set allowanceOptional
     *
     * @param boolean $allowanceOptional
     *
     * @return self
     */
    public function setAllowanceOptional($allowanceOptional)
    {
        $this->allowanceOptional = (boolean) $allowanceOptional;
        return $this;
    }

    /**
     * Get allowanceOptional
     *
     * @return boolean
     */
    public function getAllowanceOptional()
    {
        return $this->allowanceOptional;
    }

    /**
     * Get allowanceOptional
     *
     * @return boolean
     */
    public function isAllowanceOptional()
    {
        return $this->allowanceOptional;
    }

    /**
     * Set useAllowance
     *
     * @param boolean $useAllowance
     *
     * @return self
     */
    public function setUseAllowance($useAllowance)
    {
        $this->useAllowance = (boolean) $useAllowance;
        return $this;
    }

    /**
     * Get useAllowance
     *
     * @return boolean
     */
    public function getUseAllowance()
    {
        return $this->useAllowance;
    }

    /**
     * Gets useAllowance
     *
     * @return boolean
     */
    public function isUseAllowance()
    {
        return $this->useAllowance;
    }

    /**
     * Set suggestAllowance
     *
     * @param boolean $suggestAllowance
     *
     * @return self
     */
    public function setSuggestAllowance($suggestAllowance)
    {
        $this->suggestAllowance = (boolean) $suggestAllowance;
        return $this;
    }

    /**
     * Get suggestAllowance
     *
     * @return boolean
     */
    public function getSuggestAllowance()
    {
        return $this->suggestAllowance;
    }

    /**
     * Gets suggestAllowance
     *
     * @return boolean
     */
    public function isSuggestAllowance()
    {
        return $this->suggestAllowance;
    }

    /**
     * Set redirectUri
     *
     * @param string $redirectUri
     *
     * @return self
     */
    public function setRedirectUri($redirectUri)
    {
        if ($redirectUri === null) {
            $this->redirectUri = null;
        } else {
            Paysera_WalletApi_Util_Assert::isScalar($redirectUri);
            $this->redirectUri = (string) $redirectUri;
        }
        return $this;
    }

    /**
     * Get redirectUri
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Set callbackUri
     *
     * @param string|boolean false $callbackUri
     *
     * @return self
     */
    public function setCallbackUri($callbackUri)
    {
        if ($callbackUri === null) {
            $this->setCallbacksDisabled(false);
            $this->callbackUri = null;
        } elseif ($callbackUri === false) {
            $this->disableCallbacks();
        } else {
            Paysera_WalletApi_Util_Assert::isScalar($callbackUri);
            $this->callbackUri = (string) $callbackUri;
            $this->setCallbacksDisabled(false);
        }
        return $this;
    }

    /**
     * Get callbackUri
     *
     * @return string
     */
    public function getCallbackUri()
    {
        return $this->callbackUri;
    }

    /**
     * Set callbacksDisabled
     *
     * @param boolean $callbacksDisabled
     *
     * @return self
     */
    public function setCallbacksDisabled($callbacksDisabled)
    {
        $this->callbacksDisabled = (boolean) $callbacksDisabled;
        if ($this->callbacksDisabled) {
            $this->callbackUri = null;
        }
        return $this;
    }

    /**
     * Set callbacksDisabled to true
     *
     * @return self
     */
    public function disableCallbacks()
    {
        $this->setCallbacksDisabled(true);
        return $this;
    }

    /**
     * Get callbacksDisabled
     *
     * @return boolean
     */
    public function getCallbacksDisabled()
    {
        return $this->callbacksDisabled;
    }

    /**
     * Get callbacksDisabled
     *
     * @return boolean
     */
    public function isCallbacksDisabled()
    {
        return $this->callbacksDisabled;
    }

    /**
     * Gets reserve period in hours.
     *
     * @return integer
     */
    public function getReserveFor()
    {
        return $this->reserveFor;
    }

    /**
     * Sets reserve period in hours
     *
     * @param integer $reserveForInHours

     * @return self
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function setReserveFor($reserveForInHours)
    {
        if ($this->getKey() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot change reserve time to already saved transaction');
        }
        $this->reserveFor = $reserveForInHours;
        $this->reserveUntil = null;
        return $this;
    }

    /**
     * Gets reserveUntil
     *
     * @return DateTime
     */
    public function getReserveUntil()
    {
        return $this->reserveUntil;
    }

    /**
     * Sets reserveUntil
     *
     * @param DateTime $reserveUntil
     *
     * @return self
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function setReserveUntil(DateTime $reserveUntil)
    {
        if ($this->getKey() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot change reserve time to already saved transaction');
        }
        $this->reserveFor = null;
        $this->reserveUntil = $reserveUntil;
        return $this;
    }

    /**
     * Gets userInformation
     *
     * @return Paysera_WalletApi_Entity_UserInformation
     */
    public function getUserInformation()
    {
        return $this->userInformation;
    }

    /**
     * Sets userInformation
     *
     * @param Paysera_WalletApi_Entity_UserInformation $userInformation

     * @return self
     */
    public function setUserInformation(Paysera_WalletApi_Entity_UserInformation $userInformation)
    {
        $this->userInformation = $userInformation;
        return $this;
    }

    /**
     * Sets autoConfirm
     *
     * @param boolean $autoConfirm
     *
     * @return self
     */
    public function setAutoConfirm($autoConfirm)
    {
        $this->autoConfirm = $autoConfirm;

        return $this;
    }

    /**
     * Gets autoConfirm
     *
     * @return boolean
     */
    public function isAutoConfirm()
    {
        return $this->autoConfirm;
    }
}