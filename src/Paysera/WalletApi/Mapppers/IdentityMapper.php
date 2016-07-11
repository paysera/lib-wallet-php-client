<?php

class Paysera_WalletApi_Entity_User_Identity_Mapper
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

        if (isset($identity['name'])) {
            $identity->setName($identity['name']);
        }

        if (isset($identity['surname'])) {
            $identity->setSurname($identity['surname']);
        }

        if (isset($identity['nationality'])) {
            $identity->setNationality($identity['nationality']);
        }

        if (isset($identity['code'])) {
            $identity->setCode($identity['code']);
        }

        return $identity;
    }
}
