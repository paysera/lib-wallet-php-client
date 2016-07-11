<?php

class Paysera_WalletApi_Mapper_PlainValueMapper
{
    /**
     * Returns same data
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function mapFromEntity($data)
    {
        return $data;
    }

    /**
     * Returns same data
     *
     * @param mixed $entity
     *
     * @return mixed
     */
    public function mapToEntity($entity)
    {
        return $entity;
    }
}
