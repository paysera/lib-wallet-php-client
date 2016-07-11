<?php

class Paysera_WalletApi_Mapper_InquiryResultMapper
{
    private $valueProviders;

    public function __construct(array $valueProviders)
    {
        $this->valueProviders = $valueProviders;
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
            $provider = $this->getValueProvider($inquiryResult->getItemType());
            $inquiryResult->setValue($provider->mapToEntity($data['value']));
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
        $valueProvider = $this->getValueProvider($entity->getItemType());
        return array(
            'inquiry_identifier' => $entity->getInquiryIdentifier(),
            'item_identifier' => $entity->getItemIdentifier(),
            'item_type' => $entity->getItemType(),
            'value' => $valueProvider->mapFromEntity($entity->getValue())
        );
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    private function getValueProvider($type)
    {
        return $this->valueProviders[$type];
    }
}
