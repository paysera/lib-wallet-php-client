<?php

class Paysera_WalletApi_Entity_Restriction_UserRestriction
{
    const TYPE_LEGAL = 'legal';
    const TYPE_NATURAL = 'natural';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $identityRequired;

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
     * @param boolean $identityRequired
     *
     * @return $this
     */
    public function setIdentityRequired($identityRequired)
    {
        $this->identityRequired = $identityRequired;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIdentityRequired()
    {
        return $this->identityRequired;
    }

    /**
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}