<?php

/**
 * Entity representing Location
 */
class Paysera_WalletApi_Entity_Location
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const SERVICE_TYPE_CASH_IN = 'cash_in';
    const SERVICE_TYPE_CASH_OUT = 'cash_out';
    const SERVICE_TYPE_IDENTIFICATION = 'identification';
    const SERVICE_TYPE_PAY = 'pay';

    private static $serviceTypes = array(
        self::SERVICE_TYPE_CASH_IN,
        self::SERVICE_TYPE_CASH_OUT,
        self::SERVICE_TYPE_IDENTIFICATION,
        self::SERVICE_TYPE_PAY,
    );

    /**
     * @var
     * @readonly
     */
    private $id;

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
    private $identifier;

    /**
     * @var string
     */
    private $address;

    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lng;

    /**
     * @var int
     */
    private $radius = 0;

    /**
     * @var Paysera_WalletApi_Entity_Location_Price[]
     */
    private $prices = array();

    /**
     * @var Paysera_WalletApi_Entity_Location_DayWorkingHours[]
     */
    private $workingHours = array();

    /**
     * @var string
     */
    private $imagePinOpen;

    /**
     * @var string
     */
    private $imagePinClosed;

    /**
     * @var array
     */
    private $services = array();

    /**
     * @var array
     */
    private $payCategories = array();

    /**
     * @var array
     */
    private $cashInTypes = array();

    /**
     * @var array
     */
    private $cashOutTypes = array();

    /**
     * @var string
     */
    private $status;

    /**
     * @var bool
     */
    private $public;

    /**
     * @var Paysera_WalletApi_Entity_Spot[]
     */
    private $spots = array();

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
     * Set public
     *
     * @param boolean $public
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
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
     * @param string $imagePinClosed
     *
     * @return $this
     */
    public function setImagePinClosed($imagePinClosed)
    {
        $this->imagePinClosed = $imagePinClosed;

        return $this;
    }

    /**
     * @return string
     */
    public function getImagePinClosed()
    {
        return $this->imagePinClosed;
    }

    /**
     * @param string $imagePinOpen
     *
     * @return $this
     */
    public function setImagePinOpen($imagePinOpen)
    {
        $this->imagePinOpen = $imagePinOpen;

        return $this;
    }

    /**
     * @return string
     */
    public function getImagePinOpen()
    {
        return $this->imagePinOpen;
    }

    /**
     * @param array $payCategories
     *
     * @return $this
     */
    public function setPayCategories($payCategories)
    {
        $this->payCategories = $payCategories;

        return $this;
    }

    /**
     * @return array
     */
    public function getPayCategories()
    {
        return $this->payCategories;
    }

    /**
     * @return array
     */
    public function getCashInTypes()
    {
        return $this->cashInTypes;
    }

    /**
     * @param array $cashInTypes
     *
     * @return $this
     */
    public function setCashInTypes($cashInTypes)
    {
        $this->cashInTypes = $cashInTypes;

        return $this;
    }

    /**
     * @return array
     */
    public function getCashOutTypes()
    {
        return $this->cashOutTypes;
    }

    /**
     * @param array $cashOutTypes
     *
     * @return $this
     */
    public function setCashOutTypes($cashOutTypes)
    {
        $this->cashOutTypes = $cashOutTypes;

        return $this;
    }

    /**
     * @param array $services
     *
     * @return $this
     */
    public function setServices($services)
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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

    /**
     * Available location service types
     *
     * @return array
     */
    public static function getServiceTypes()
    {
        return self::$serviceTypes;
    }

    /**
     * @param Paysera_WalletApi_Entity_Spot[] $spots
     * 
     * @return $this
     */
    public function setSpots($spots)
    {
        $this->spots = $spots;

        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Spot[]
     */
    public function getSpots()
    {
        return $this->spots;
    }
}
