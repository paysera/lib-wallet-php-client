<?php

namespace Paysera_WalletApi\Paysera\WalletApi\Entity;

class Paysera_WalletApi_Entity_SpotInfo
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $logoUri;

    /**
     * @var int
     */
    private $locationId;

    /**
     * @param string $title
     *
     * @return Paysera_WalletApi_Entity_SpotInfo
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $description
     *
     * @return Paysera_WalletApi_Entity_SpotInfo
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $address
     *
     * @return Paysera_WalletApi_Entity_SpotInfo
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $logoUri
     *
     * @return Paysera_WalletApi_Entity_SpotInfo
     */
    public function setLogoUri($logoUri)
    {
        $this->logoUri = $logoUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogoUri()
    {
        return $this->logoUri;
    }

    /**
     * @param int $locationId
     *
     * @return Paysera_WalletApi_Entity_SpotInfo
     */
    public function setLocationId($locationId)
    {
        $this->locationId = $locationId;

        return $this;
    }

    /**
     * @return int
     */
    public function getLocationId()
    {
        return $this->locationId;
    }
}
