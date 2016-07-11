<?php

class InquiryResultMapperTest extends \PHPUnit_Framework_TestCase
{
    private $inquiryResultMapper;

    public function setUp()
    {
        $this->inquiryResultMapper = new \Paysera_WalletApi_Mapper_InquiryResultMapper(array(
            Paysera_WalletApi_Entity_Inquiry_InquiryItem::TYPE_USER_IDENTITY =>
                new Paysera_WalletApi_Mapper_IdentityMapper(),
            Paysera_WalletApi_Entity_Inquiry_InquiryItem::TYPE_PERSON_CODE =>
                new Paysera_WalletApi_Mapper_PlainValueMapper()
        ));
    }

    public function testInquiryResultValueWithoutIdentity()
    {
        $inquiryValue = 9999999;
        
        $data = [
            'inquiry_identifier' => 'identifier',
            'item_identifier' => 'item identifier',
            'item_type' => 'person_code',
            'value' => $inquiryValue,
        ];

        $result = $this->inquiryResultMapper->mapToEntity($data);

        $this->assertSame($result->getInquiryIdentifier(), $data['inquiry_identifier']);
        $this->assertSame($result->getItemIdentifier(), $data['item_identifier']);
        $this->assertNotNull($result->getValue());
        $this->assertSame($result->getValue(), $inquiryValue);
    }

    public function testInquiryResultValueWithIdentity()
    {
        $inquiryValue = [
            'name' => 'Name',
            'surname' => 'Surname',
            'nationality' => 'LT',
            'code' => 606060
        ];

        $data = [
            'inquiry_identifier' => 'identifier',
            'item_identifier' => 'item identifier',
            'item_type' => 'user_identity',
            'value' => $inquiryValue,
        ];

        $result = $this->inquiryResultMapper->mapToEntity($data);

        $this->assertSame($result->getInquiryIdentifier(), $data['inquiry_identifier']);
        $this->assertSame($result->getItemIdentifier(), $data['item_identifier']);
        $this->assertNotNull($result->getValue());

        $identity = $result->getValue();
        $this->assertInstanceOf('\Paysera_WalletApi_Entity_User_Identity', $identity);
        $this->assertSame($identity->getName(), $inquiryValue['name']);
        $this->assertSame($identity->getSurname(), $inquiryValue['surname']);
        $this->assertSame($identity->getNationality(), $inquiryValue['nationality']);
        $this->assertSame($identity->getCode(), $inquiryValue['code']);

    }
}
