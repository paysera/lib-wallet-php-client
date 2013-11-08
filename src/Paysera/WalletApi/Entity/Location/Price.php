<?php

/**
 * Price
 *
 * @author Vytautas Gimbutas <vytautas@gimbutas.net>
 */
class Paysera_WalletApi_Entity_Location_Price
{
    const TYPE_PRICE = 'price';
    const TYPE_OFFER = 'offer';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Paysera_WalletApi_Entity_Money
     */
    protected $price;

    /**
     * Set price
     *
     * @param \Paysera_WalletApi_Entity_Money $price
     *
     * @return Paysera_WalletApi_Entity_Location_Price
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return \Paysera_WalletApi_Entity_Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Paysera_WalletApi_Entity_Location_Price
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
     * Marks as price
     *
     * @return Paysera_WalletApi_Entity_Location_Price
     */
    public function markAsPrice()
    {
        $this->type = self::TYPE_PRICE;
        return $this;
    }

    /**
     * Marks as offer
     *
     * @return Paysera_WalletApi_Entity_Location_Price
     */
    public function markAsOffer()
    {
        $this->type = self::TYPE_OFFER;
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
     * @return bool
     */
    public function isOffer()
    {
        return $this->type === self::TYPE_OFFER;
    }

    /**
     * @return bool
     */
    public function isPrice()
    {
        return $this->type === self::TYPE_PRICE;
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
