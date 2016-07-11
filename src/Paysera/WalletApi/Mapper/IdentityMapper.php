<?php

class Paysera_WalletApi_Mapper_IdentityMapper
{
    /**
     * Maps array to Identity entity
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_User_Identity
     */
    public function mapToEntity(array $data)
    {
        $identity = new Paysera_WalletApi_Entity_User_Identity();

        if (isset($data['name'])) {
            $identity->setName($data['name']);
        }

        if (isset($data['surname'])) {
            $identity->setSurname($data['surname']);
        }

        if (isset($data['nationality'])) {
            $identity->setNationality($data['nationality']);
        }

        if (isset($data['code'])) {
            $identity->setCode($data['code']);
        }

        return $identity;
    }
}
