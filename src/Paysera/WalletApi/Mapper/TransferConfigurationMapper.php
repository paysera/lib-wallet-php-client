<?php

class Paysera_WalletApi_Mapper_TransferConfigurationMapper
{
    /**
     * Maps TransferConfiguration entity to array
     *
     * @param Paysera_WalletApi_Entity_TransferConfiguration $transferConfiguration
     *
     * @return array
     */
    public function mapFromEntity(Paysera_WalletApi_Entity_TransferConfiguration $transferConfiguration)
    {
        $data = [];
        if ($transferConfiguration->getClientId() !== null) {
            $data['clientId'] = $transferConfiguration->getClientId();
        }
        if ($transferConfiguration->getApplicationClientId() !== null) {
            $data['appClientId'] = $transferConfiguration->getApplicationClientId();
        }
        if ($transferConfiguration->getWalletId() !== null) {
            $data['walletId'] = $transferConfiguration->getWalletId();
        }

        return $data;
    }

    /**
     * Maps array to TransferConfiguration entity
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_TransferConfiguration
     */
    public function mapToEntity(array $data)
    {
        $transferConfiguration = new Paysera_WalletApi_Entity_TransferConfiguration();

        if (isset($data['clientId'])) {
            $transferConfiguration->setClientId($data['clientId']);
        }

        if (isset($data['appClientId'])) {
            $transferConfiguration->setApplicationClientId($data['appClientId']);
        }

        if (isset($data['walletId'])) {
            $transferConfiguration->setWalletId($data['walletId']);
        }

        return $transferConfiguration;
    }
}
