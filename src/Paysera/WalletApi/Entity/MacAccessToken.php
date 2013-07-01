<?php

class Paysera_WalletApi_Entity_MacAccessToken
{
    /**
     * @var integer UNIX timestamp until when the access token is valid
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $macId;

    /**
     * @var string
     */
    protected $macKey;

    /**
     * @var string
     */
    protected $refreshToken;

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
     * Gets expiresAt
     *
     * @return integer timestamp
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Sets expiresAt
     *
     * @param integer $expiresAt timestamp

     * @return self
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * Gets macId
     *
     * @return string
     */
    public function getMacId()
    {
        return $this->macId;
    }

    /**
     * Sets macId
     *
     * @param string $macId

     * @return self
     */
    public function setMacId($macId)
    {
        $this->macId = $macId;
        return $this;
    }

    /**
     * Gets macKey
     *
     * @return string
     */
    public function getMacKey()
    {
        return $this->macKey;
    }

    /**
     * Sets macKey
     *
     * @param string $macKey

     * @return self
     */
    public function setMacKey($macKey)
    {
        $this->macKey = $macKey;
        return $this;
    }

    /**
     * Sets refreshToken
     *
     * @param string $refreshToken
     *
     * @return $this
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * Gets refreshToken
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

}