<?php

class Paysera_WalletApi_Mapper_IdentityMapper
{
    /**
     * Maps Identity entity to array
     *
     * @param Paysera_WalletApi_Entity_User_Identity $identity
     *
     * @return array
     */
    public function mapFromEntity(Paysera_WalletApi_Entity_User_Identity $identity)
    {
        $data = array();
        if ($identity->getName() !== null) {
            $data['name'] = $identity->getName();
        }
        if ($identity->getSurname() !== null) {
            $data['surname'] = $identity->getSurname();
        }
        if ($identity->getNationality() !== null) {
            $data['nationality'] = $identity->getNationality();
        }
        if ($identity->getCode() !== null) {
            $data['code'] = $identity->getCode();
        }
        return $data;
    }

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
