<?php

class Paysera_WalletApi_Entity_Inquiry_Inquiry
{
    const TYPE_REQUIRED = 'required';
    const TYPE_OPTIONAL = 'optional';

    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    
    private $identifier;
    private $type;
    private $description;
    private $status;
    private $inquiryItems;
    
    public function __construct()
    {
        $this->inquiryItems = array();
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Inquiry_InquiryItem[]
     */
    public function getInquiryItems()
    {
        return $this->inquiryItems;
    }

    /**
     * @param Paysera_WalletApi_Entity_Inquiry_InquiryItem[] $inquiryItems
     * @return $this
     */
    public function setInquiryItems($inquiryItems)
    {
        $this->inquiryItems = $inquiryItems;
        return $this;
    }

    /**
     * @param Paysera_WalletApi_Entity_Inquiry_InquiryItem $inquiryItem
     * @return $this
     */
    public function addInquiryItem($inquiryItem)
    {
        $this->inquiryItems[] = $inquiryItem;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
