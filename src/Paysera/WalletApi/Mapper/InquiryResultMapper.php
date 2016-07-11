<?php

class Paysera_WalletApi_Mapper_InquiryResultMapper
{
    private $inquiryResultMapper;

    public function __construct(array $inquiryResultMapper)
    {
        $this->inquiryResultMapper = $inquiryResultMapper;
    }

    /**
     * Maps array to Inquiry result entity
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Inquiry_InquiryResult
     */
    public function mapToEntity(array $data)
    {
        $inquiryResult = new Paysera_WalletApi_Entity_Inquiry_InquiryResult();

        if (isset($data['inquiry_identifier'])) {
            $inquiryResult->setInquiryIdentifier($data['inquiry_identifier']);
        }

        if (isset($data['item_identifier'])) {
            $inquiryResult->setItemIdentifier($data['item_identifier']);
        }

        if (isset($data['item_type'])) {
            $inquiryResult->setItemType($data['item_type']);
        }

        if (isset($data['value'])) {
            $mapper = $this->getInquiryResultMapper($inquiryResult->getItemType());
            $inquiryResult->setValue($mapper->mapToEntity($data['value']));
        }

        return $inquiryResult;
    }

    /**
     * Maps Inquiry result entity to array
     *
     * @param Paysera_WalletApi_Entity_Inquiry_InquiryResult $entity
     *
     * @return array
     */
    public function mapFromEntity($entity)
    {
        $mapper = $this->getInquiryResultMapper($entity->getItemType());
        return array(
            'inquiry_identifier' => $entity->getInquiryIdentifier(),
            'item_identifier' => $entity->getItemIdentifier(),
            'item_type' => $entity->getItemType(),
            'value' => $mapper->mapFromEntity($entity->getValue())
        );
    }

    /**
     * @param string $type
     *
     * @return Object
     */
    private function getInquiryResultMapper($type)
    {
        return $this->inquiryResultMapper[$type];
    }
}
