<?php


class Paysera_WalletApi_Entity_Inquiry_InquiryResult
{

    /**
     * @var string
     */
    private $inquiryIdentifier;

    /**
     * @var string
     */
    private $itemIdentifier;

    /**
     * @var string
     */
    private $itemType;
    
    /**
     * @var mixed
     */
    private $value;

    /**
     * @return string
     */
    public function getInquiryIdentifier()
    {
        return $this->inquiryIdentifier;
    }

    /**
     * @param string $inquiryIdentifier
     *
     * @return $this
     */
    public function setInquiryIdentifier($inquiryIdentifier)
    {
        $this->inquiryIdentifier = $inquiryIdentifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemIdentifier()
    {
        return $this->itemIdentifier;
    }

    /**
     * @param string $itemIdentifier
     *
     * @return $this
     */
    public function setItemIdentifier($itemIdentifier)
    {
        $this->itemIdentifier = $itemIdentifier;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @param string $itemType
     *
     * @return $this
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;

        return $this;
    }
}
