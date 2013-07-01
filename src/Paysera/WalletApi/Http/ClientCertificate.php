<?php


/**
 * ClientCertificate
 */
class Paysera_WalletApi_Http_ClientCertificate
{
    const TYPE_PEM = 'PEM';
    const TYPE_DER = 'DER';
    const TYPE_ENG = 'ENG';

    /**
     * @var string
     */
    protected $privateKeyPath;

    /**
     * @var string
     */
    protected $privateKeyPassword;

    /**
     * @var string
     */
    protected $privateKeyType = self::TYPE_PEM;

    /**
     * @var string
     */
    protected $certificatePath;

    /**
     * @var string
     */
    protected $certificatePassword;

    /**
     * @var string
     */
    protected $certificateType = self::TYPE_PEM;

    public static function create()
    {
        return new self();
    }

    /**
     * Sets certificatePassword
     *
     * @param string $certificatePassword
     *
     * @return $this
     */
    public function setCertificatePassword($certificatePassword)
    {
        $this->certificatePassword = $certificatePassword;

        return $this;
    }

    /**
     * Gets certificatePassword
     *
     * @return string
     */
    public function getCertificatePassword()
    {
        return $this->certificatePassword;
    }

    /**
     * Sets certificatePath
     *
     * @param string $certificatePath
     *
     * @return $this
     */
    public function setCertificatePath($certificatePath)
    {
        $this->certificatePath = $certificatePath;

        return $this;
    }

    /**
     * Gets certificatePath
     *
     * @return string
     */
    public function getCertificatePath()
    {
        return $this->certificatePath;
    }

    /**
     * Sets certificateType
     *
     * @param string $certificateType
     *
     * @return $this
     */
    public function setCertificateType($certificateType)
    {
        $this->certificateType = $certificateType;

        return $this;
    }

    /**
     * Gets certificateType
     *
     * @return string
     */
    public function getCertificateType()
    {
        return $this->certificateType;
    }

    /**
     * Sets privateKeyPassword
     *
     * @param string $privateKeyPassword
     *
     * @return $this
     */
    public function setPrivateKeyPassword($privateKeyPassword)
    {
        $this->privateKeyPassword = $privateKeyPassword;

        return $this;
    }

    /**
     * Gets privateKeyPassword
     *
     * @return string
     */
    public function getPrivateKeyPassword()
    {
        return $this->privateKeyPassword;
    }

    /**
     * Sets privateKeyPath
     *
     * @param string $privateKeyPath
     *
     * @return $this
     */
    public function setPrivateKeyPath($privateKeyPath)
    {
        $this->privateKeyPath = $privateKeyPath;

        return $this;
    }

    /**
     * Gets privateKeyPath
     *
     * @return string
     */
    public function getPrivateKeyPath()
    {
        return $this->privateKeyPath;
    }

    /**
     * Sets privateKeyType
     *
     * @param string $privateKeyType
     *
     * @return $this
     */
    public function setPrivateKeyType($privateKeyType)
    {
        $this->privateKeyType = $privateKeyType;

        return $this;
    }

    /**
     * Gets privateKeyType
     *
     * @return string
     */
    public function getPrivateKeyType()
    {
        return $this->privateKeyType;
    }


}