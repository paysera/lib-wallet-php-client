<?php

/**
 * Paysera_WalletApi_Entity_ClientPermissionsToWallet
 */
class Paysera_WalletApi_Entity_ClientPermissionsToWallet
{
    /**
     * @var Paysera_WalletApi_Entity_Wallet
     */
    private $wallet;

    /**
     * @var array
     */
    private $scopes = [];

    /**
     * @param Paysera_WalletApi_Entity_Wallet $wallet
     * @return self
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_Wallet
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * @param array $scopes
     * @return self
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @return bool
     */
    public function isBalanceGranted()
    {
        return $this->isGranted(Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE);
    }

    /**
     * @return bool
     */
    public function isStatementsGranted()
    {
        return $this->isGranted(Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS);
    }

    /**
     * @return self
     */
    public function grantBalance()
    {
        return $this->grant(Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE);
    }

    /**
     * @return self
     */
    public function grantStatements()
    {
        return $this->grant(Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS);
    }

    /**
     * @return self
     */
    public function revokeBalance()
    {
        return $this->revoke(Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE);
    }

    /**
     * @return self
     */
    public function revokeStatements()
    {
        return $this->revoke(Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS);
    }

    /**
     * @param string $scope
     * @return bool
     */
    public function isGranted($scope)
    {
        return in_array($scope, $this->scopes);
    }


    /**
     * @param $scope
     * @return self
     */
    private function revoke($scope)
    {
        $index = array_search($scope, $this->scopes);

        if ($index !== false) {
            unset($this->scopes[$index]);
        }

        return $this;
    }

    /**
     * @param string $scope
     * @return self
     */
    private function grant($scope)
    {
        if (!$this->isGranted($scope)) {
            $this->scopes[] = $scope;
        }

        return $this;
    }
}
