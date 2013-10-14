<?php

/**
 * Entity representing Location
 */
class Paysera_WalletApi_Entity_Location
{
    /**
     * @var
     * @readonly
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var float
     */
    protected $lat;

    /**
     * @var float
     */
    protected $lng;

    /**
     * @var int
     */
    protected $radius = 0;

    /**
     * @var Paysera_WalletApi_Entity_Location_Price[]
     */
    protected $prices = array();

    /**
     * @var Paysera_WalletApi_Entity_Location_DayWorkingHours[]
     */
    protected $workingHours = array();

    /**
     * Set id
     *
     * @param mixed $id
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set radius
     *
     * @param int $radius
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setRadius($radius)
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * Get radius
     *
     * @return int
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Adds price
     *
     * @param Paysera_WalletApi_Entity_Location_Price $price
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function addPrice(Paysera_WalletApi_Entity_Location_Price $price)
    {
        $this->prices[] = $price;
        return $this;
    }

    /**
     * Set prices
     *
     * @param \Paysera_WalletApi_Entity_Location_Price[] $prices
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setPrices(array $prices)
    {
        $this->prices = $prices;

        return $this;
    }

    /**
     * Get prices
     *
     * @return \Paysera_WalletApi_Entity_Location_Price[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Adds working hours
     *
     * @param Paysera_WalletApi_Entity_Location_DayWorkingHours $workingHours
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function addWorkingHours(Paysera_WalletApi_Entity_Location_DayWorkingHours $workingHours)
    {
        $this->workingHours[] = $workingHours;
        return $this;
    }

    /**
     * Set workingHours
     *
     * @param \Paysera_WalletApi_Entity_Location_DayWorkingHours[] $workingHours
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setWorkingHours(array $workingHours)
    {
        $this->workingHours = $workingHours;

        return $this;
    }

    /**
     * Get workingHours
     *
     * @return \Paysera_WalletApi_Entity_Location_DayWorkingHours[]
     */
    public function getWorkingHours()
    {
        return $this->workingHours;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    public static function create()
    {
        return new static();
    }
}
