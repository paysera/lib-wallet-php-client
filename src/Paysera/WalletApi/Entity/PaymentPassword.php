<?php

/**
 * Entity representing payment password
 */
class Paysera_WalletApi_Entity_PaymentPassword
{
    const TYPE_GENERATED = 'generated';
    const TYPE_PROVIDED = 'provided';

    const STATUS_DISABLED = 'disabled';
    const STATUS_PENDING = 'pending';
    const STATUS_UNLOCKED = 'unlocked';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var bool
     */
    protected $optional;

    /**
     * @var bool
     */
    protected $cancelable;

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
     * Set type
     *
     * @param string $type
     *
     * @return self
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
     * Set value
     *
     * @param string $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
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
     * Set optional
     *
     * @param bool $optional
     *
     * @return self
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;
        return $this;
    }

    /**
     * Get optional
     *
     * @return bool
     */
    public function getOptional()
    {
        return $this->optional;
    }

    /**
     * Set cancelable
     *
     * @param boolean $cancelable
     *
     * @return self
     */
    public function setCancelable($cancelable)
    {
        $this->cancelable = $cancelable;
        return $this;
    }

    /**
     * Get cancelable
     *
     * @return boolean
     */
    public function getCancelable()
    {
        return $this->cancelable;
    }

    /**
     * @return bool
     */
    public function isTypeProvided()
    {
        return $this->getType() === self::TYPE_PROVIDED;
    }

    /**
     * @return bool
     */
    public function isTypeGenerated()
    {
        return $this->getType() === self::TYPE_GENERATED;
    }
}
