<?php


class Paysera_WalletApi_Entity_Location_SearchFilter extends Paysera_WalletApi_Entity_Search_Filter
{
    /**
     * @var string
     */
    protected $lat;

    /**
     * @var string
     */
    protected $lng;

    /**
     * @var string
     */
    protected $distance;

    /**
     * @var array
     */
    protected $statuses;

    /**
     * @var DateTime
     */
    protected $updatedAfter;

    /**
     * @var array
     */
    protected $locationServices;

    /**
     * @var array
     */
    protected $payCategory;

    /**
     * @param string $distance
     *
     * @return $this
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param string $lat
     *
     * @return $this
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param string $lng
     *
     * @return $this
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param array $locationServices
     *
     * @return $this
     */
    public function setLocationServices($locationServices)
    {
        $this->locationServices = $locationServices;

        return $this;
    }

    /**
     * @return array
     */
    public function getLocationServices()
    {
        return $this->locationServices;
    }

    /**
     * @param array $payCategory
     *
     * @return $this
     */
    public function setPayCategory($payCategory)
    {
        $this->payCategory = $payCategory;

        return $this;
    }

    /**
     * @return array
     */
    public function getPayCategory()
    {
        return $this->payCategory;
    }

    /**
     * @param array $statuses
     */
    public function setStatuses(array $statuses)
    {
        $this->statuses = $statuses;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * @param \DateTime $updatedAfter
     *
     * @return $this
     */
    public function setUpdatedAfter($updatedAfter)
    {
        $this->updatedAfter = $updatedAfter;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAfter()
    {
        return $this->updatedAfter;
    }
}