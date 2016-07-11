<?php

class Paysera_WalletApi_Mapper_InquiryResultMapper
{
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
            $value = $data['value'];
            if ($inquiryResult->getItemType() == 'user_identity') {
                $value = (new Paysera_WalletApi_Mapper_IdentityMapper())
                    ->mapToEntity($data['value']);
            }
            $inquiryResult->setValue($value);
        }

        return $inquiryResult;
    }
}
