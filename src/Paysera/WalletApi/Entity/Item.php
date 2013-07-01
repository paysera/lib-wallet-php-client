<?php

/**
 * Entity representing one Item in a Payment
 */
class Paysera_WalletApi_Entity_Item
{
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
    protected $imageUri;

    /**
     * @var Paysera_WalletApi_Entity_Money
     */
    protected $price;

    /**
     * @var integer
     */
    protected $quantity = 1;

    /**
     * @var mixed
     */
    protected $parameters;

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
     * Sets title
     *
     * @param string $title

     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Gets title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets description
     *
     * @param string $description

     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets imageUri
     *
     * @param string $imageUri

     * @return self
     */
    public function setImageUri($imageUri)
    {
        $this->imageUri = $imageUri;
        return $this;
    }

    /**
     * Gets imageUri
     *
     * @return string
     */
    public function getImageUri()
    {
        return $this->imageUri;
    }

    /**
     * Sets price
     *
     * @param Paysera_WalletApi_Entity_Money $price

     * @return self
     */
    public function setPrice(Paysera_WalletApi_Entity_Money $price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Gets price
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets quantity
     *
     * @param integer $quantity

     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Gets quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets parameters
     *
     * @param mixed $parameters

     * @return self
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Gets parameters
     *
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    public function getTotalPrice()
    {
        return $this->price === null || $this->quantity === null
            ? null
            : Paysera_WalletApi_Entity_Money::create()->setAmountInCents(
                $this->price->getAmountInCents() * $this->quantity
            )->setCurrency($this->price->getCurrency());
    }
}