<?php

class Paysera_WalletApi_Client_WalletClient extends Paysera_WalletApi_Client_BaseClient
{

    /**
     * Creates payment using API
     *
     * @param Paysera_WalletApi_Entity_Payment $payment
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function createPayment(Paysera_WalletApi_Entity_Payment $payment)
    {
        return $this->mapper->decodePayment($this->client->post('payment', $this->mapper->encodePayment($payment)));
    }

    /**
     * Gets payment by ID using API
     *
     * @param integer $paymentId
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getPayment($paymentId)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $responseData = $this->get('payment/' . $paymentId);
        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Cancels payment by ID using API
     *
     * @param integer $paymentId
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function cancelPayment($paymentId)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $responseData = $this->delete('payment/' . $paymentId);
        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Removes freeze period for payment
     *
     * @param integer $paymentId
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function removeFreezePeriod($paymentId)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $responseData = $this->put(
            'payment/' . $paymentId . '/freeze',
            array('freeze_until' => 0)
        );
        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Extends freeze period for payment for specified amount of hours
     *
     * @param integer $paymentId
     * @param integer $periodInHours
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function extendFreezePeriod($paymentId, $periodInHours)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        Paysera_WalletApi_Util_Assert::isInt($periodInHours);
        $responseData = $this->put(
            'payment/' . $paymentId . '/freeze',
            array('freeze_for' => $periodInHours)
        );
        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Extends freeze period for payment for specified amount of hours
     *
     * @param integer  $paymentId
     * @param DateTime $freezeUntil
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function changeFreezePeriod($paymentId, DateTime $freezeUntil)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $responseData = $this->put(
            'payment/' . $paymentId . '/freeze',
            array('freeze_until' => $freezeUntil->getTimestamp())
        );
        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Finalizes payment, optionally changing the final price
     *
     * @param integer                  $paymentId
     * @param Paysera_WalletApi_Entity_Money $finalPrice
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function finalizePayment($paymentId, Paysera_WalletApi_Entity_Money $finalPrice = null)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);

        $responseData = $this->put(
            'payment/' . $paymentId . '/finalize',
            $finalPrice === null ? null : $this->mapper->encodePrice($finalPrice)
        );
        return $this->mapper->decodePayment($responseData);
    }


    /**
     * Finds payments by provided parameters
     *
     * @param string        $status
     * @param integer       $walletId
     * @param integer       $beneficiaryId
     * @param array $params optional search parameters
     *
     * @return Paysera_WalletApi_Entity_Search_Result
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function findPayments(
        $status = null,
        $walletId = null,
        $beneficiaryId = null,
        $params = array()
    ) {
        Paysera_WalletApi_Util_Assert::isIntOrNull($walletId);
        Paysera_WalletApi_Util_Assert::isIntOrNull($beneficiaryId);
        $query = array();
        if ($status !== null) {
            $query['status'] = $status;
        }
        if ($walletId !== null) {
            $query['wallet'] = $walletId;
        }
        if ($beneficiaryId !== null) {
            $query['beneficiary'] = $beneficiaryId;
        }
        if (count($params)) {
            $query = array_merge($query, $params);
        }
        $result = $this->get('payments' . (count($query) > 0 ? '?' . http_build_query($query) : ''));

        return $this->mapper->decodePaymentSearchResult($result);
    }

    /**
     * Creates allowance using API
     *
     * @param Paysera_WalletApi_Entity_Allowance $allowance
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function createAllowance(Paysera_WalletApi_Entity_Allowance $allowance)
    {
        $requestData = $this->mapper->encodeAllowance($allowance);
        $responseData = $this->post('allowance', $requestData);
        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Gets allowance by ID using API
     *
     * @param integer $allowanceId
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getAllowance($allowanceId)
    {
        Paysera_WalletApi_Util_Assert::isInt($allowanceId);
        $responseData = $this->get('allowance/' . $allowanceId);
        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Gets active allowance for specified wallet using API
     *
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getAllowanceForWallet($walletId)
    {
        Paysera_WalletApi_Util_Assert::isId($walletId);
        $responseData = $this->get('allowance/active/' . $walletId);
        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Gets current allowance limit for specified wallet using API
     *
     * @param integer $walletId
     * @param string  $currency
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getAllowanceLimit($walletId, $currency = 'EUR')
    {
        Paysera_WalletApi_Util_Assert::isId($walletId);
        Paysera_WalletApi_Util_Assert::isScalar($currency);
        $responseData = $this->get('allowance/limit/' . $walletId . '?currency=' . urlencode($currency));
        return $this->mapper->decodeMoney($responseData);
    }

    /**
     * Cancels allowance by ID using API
     *
     * @param integer $allowanceId
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function cancelAllowance($allowanceId)
    {
        Paysera_WalletApi_Util_Assert::isInt($allowanceId);
        $responseData = $this->delete('allowance/' . $allowanceId);
        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Cancels allowance for specified wallet using API
     *
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function cancelAllowanceForWallet($walletId)
    {
        Paysera_WalletApi_Util_Assert::isId($walletId);
        $responseData = $this->delete('allowance/active/' . $walletId);
        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Creates transaction using API
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function createTransaction(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        $requestData = $this->mapper->encodeTransaction($transaction);
        $responseData = $this->post('transaction', $requestData);
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Gets transaction by transaction key using API
     *
     * @param string $transactionKey
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getTransaction($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $responseData = $this->get('transaction/' . $transactionKey);
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Revokes transaction by transaction key using API
     *
     * @param string $transactionKey
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function revokeTransaction($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $responseData = $this->delete('transaction/' . $transactionKey);
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Confirms transaction by transaction key using API
     *
     * @param string $transactionKey
     * @param array $transactionPrices
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function confirmTransaction($transactionKey, $transactionPrices = array())
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $requestData = $this->mapper->encodeTransactionPrices($transactionPrices);
        $responseData = $this->put('transaction/' . $transactionKey . '/confirm', $requestData);
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Tries to accept transaction by active allowance using API
     *
     * @param string $transactionKey
     * @param int|Paysera_WalletApi_Entity_WalletIdentifier|string $payer
     * @param Paysera_WalletApi_Entity_FundsSource[] $fundsSources
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     */
    public function acceptTransactionUsingAllowance($transactionKey, $payer, $fundsSources = array())
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);

        $content = array();

        if (count($fundsSources) > 0) {
            $content = array_merge($this->mapper->encodeFundsSources($fundsSources));
        }

        if ($payer instanceof Paysera_WalletApi_Entity_WalletIdentifier) {
            $payer->validate();

            $content = array_merge($content, $this->mapper->encodePayer($payer));
            $uri = 'transaction/' . $transactionKey . '/reserve';
        } else {
            Paysera_WalletApi_Util_Assert::isId($payer);

            $uri = 'transaction/' . $transactionKey . '/reserve/' . (string)$payer;
        }

        $responseData = $this->put($uri, $content);

        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Tries to accept transaction by sending user's PIN code using API
     *
     * @param string $transactionKey
     * @param int|Paysera_WalletApi_Entity_WalletIdentifier|string $payer
     * @param string $pin
     * @param Paysera_WalletApi_Entity_FundsSource[] $fundsSources
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     */
    public function acceptTransactionUsingPin($transactionKey, $payer, $pin, $fundsSources = array())
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        Paysera_WalletApi_Util_Assert::isScalar($pin);

        $content = $this->mapper->encodePin($pin);

        if (count($fundsSources) > 0) {
            $content = array_merge($this->mapper->encodeFundsSources($fundsSources));
        }

        if ($payer instanceof Paysera_WalletApi_Entity_WalletIdentifier) {
            $payer->validate();

            $content = array_merge($content, $this->mapper->encodePayer($payer));
            $uri = 'transaction/' . $transactionKey . '/reserve';
        } else {
            Paysera_WalletApi_Util_Assert::isId($payer);

            $uri = 'transaction/' . $transactionKey . '/reserve/' . (string)$payer;
        }

        $responseData = $this->put($uri, $content);
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Sends FLASH SMS using API to the user to accept transaction
     *
     * @param string  $transactionKey
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function sendTransactionFlashSms($transactionKey, $walletId)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        Paysera_WalletApi_Util_Assert::isId($walletId);
        $responseData = $this->put('transaction/' . $transactionKey . '/flash/' . $walletId);
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * @param string $transactionKey
     *
     * @return Paysera_WalletApi_Entity_Inquiry_InquiryResult[]
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getInquiredInformation($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $responseData = $this->get('transaction/' . $transactionKey . '/inquired-information');
        return $this->mapper->decodeInquiryResults($responseData);
    }

    /**
     * Gets available types to accept transaction using API
     *
     * @param string  $transactionKey
     * @param integer $walletId
     *
     * @return string[]
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getAvailableTransactionTypes($transactionKey, $walletId)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        Paysera_WalletApi_Util_Assert::isId($walletId);
        return $this->get('transaction/' . $transactionKey . '/type/' . $walletId);
    }

    /**
     * Gets wallet by id using API
     *
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Wallet
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWallet($walletId)
    {
        Paysera_WalletApi_Util_Assert::isId($walletId);
        $responseData = $this->get('wallet/' . $walletId);
        return $this->mapper->decodeWallet($responseData);
    }

    /**
     * Gets wallet balance by id using API
     *
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Wallet_Balance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWalletBalance($walletId)
    {
        Paysera_WalletApi_Util_Assert::isId($walletId);
        $responseData = $this->get('wallet/' . $walletId . '/balance');
        return $this->mapper->decodeWalletBalance($responseData);
    }

    /**
     * Gets statements for wallet by id using API
     *
     * @param integer                                   $walletId
     * @param Paysera_WalletApi_Entity_Statement_SearchFilter $filter
     *
     * @return Paysera_WalletApi_Entity_Search_Result|Paysera_WalletApi_Entity_Statement[]
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWalletStatements($walletId, Paysera_WalletApi_Entity_Statement_SearchFilter $filter = null)
    {
        Paysera_WalletApi_Util_Assert::isId($walletId);
        if ($filter !== null) {
            $query = '?' . http_build_query($this->mapper->encodeStatementFilter($filter), null, '&');
        } else {
            $query = '';
        }
        return $this->mapper->decodeStatementSearchResult(
            $this->get('wallet/' . $walletId . '/statements' . $query)
        );
    }

    /**
     * Gets wallet by search parameters
     *
     * @param array $parameters
     *
     * @return Paysera_WalletApi_Entity_Wallet
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWalletBy(array $parameters)
    {
        $responseData = $this->get('wallet?' . http_build_query($parameters, null, '&'));
        return $this->mapper->decodeWallet($responseData);
    }

    /**
     * Gets wallets by contact list (emails or phone numbers)
     *
     * @param array $contacts
     * @param bool  $private  whether to send hashes of contacts to avoid sending private information
     *
     * @return Paysera_WalletApi_Entity_Wallet[] array keys are provided contacts (only the found ones are provided)
     */
    public function getWalletsByContacts(array $contacts, $private = false)
    {
        if (count($contacts) === 0) {
            return array();
        }

        $map = array();
        $email = array();
        $phone = array();
        foreach ($contacts as $contact) {
            if (strpos($contact, '@') !== false) {
                $formatted = strtolower($contact);
                if ($private) {
                    $formatted = sha1($formatted);
                }
                $email[] = $formatted;
            } else {
                $formatted = preg_replace('/[^\d]/', '', $contact);
                if ($private) {
                    $formatted = sha1($formatted);
                }
                $phone[] = $formatted;
            }
            $map[$formatted] = $contact;
        }
        $parameters = array();
        if (count($email) > 0) {
            $parameters[$private ? 'email_hash' : 'email'] = implode(',', $email);
        }
        if (count($phone) > 0) {
            $parameters[$private ? 'phone_hash' : 'phone'] = implode(',', $phone);
        }
        $responseData = $this->get('wallets?' . http_build_query($parameters, null, '&'));

        $result = array();
        foreach ($responseData as $key => $walletData) {
            $result[$map[$key]] = $this->mapper->decodeWallet($walletData);
        }
        return $result;
    }

    /**
     * Gets user by id using API
     *
     * @param integer $userId
     *
     * @return Paysera_WalletApi_Entity_User
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUser($userId)
    {
        Paysera_WalletApi_Util_Assert::isId($userId);
        $responseData = $this->get('user/' . $userId);
        return $this->mapper->decodeUser($responseData);
    }

    /**
     * Gets user's email by id using API
     *
     * @param integer $userId
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserEmail($userId)
    {
        Paysera_WalletApi_Util_Assert::isId($userId);
        return $this->get('user/' . $userId . '/email');
    }

    /**
     * Gets user's phone by id using API
     *
     * @param integer $userId
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserPhone($userId)
    {
        Paysera_WalletApi_Util_Assert::isId($userId);
        return $this->get('user/' . $userId . '/phone');
    }

    /**
     * Gets wallet barcode by search parameters
     *
     * @param array $parameters
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWalletBarcodeBy(array $parameters)
    {
        $responseData = $this->get('wallet/barcode?' . http_build_query($parameters, null, '&'));
        return $responseData['barcode'];
    }

    /**
     * Gets user's address by id using API
     *
     * @param integer $userId
     *
     * @return Paysera_WalletApi_Entity_User_Address
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserAddress($userId)
    {
        Paysera_WalletApi_Util_Assert::isId($userId);
        $responseData = $this->get('user/' . $userId . '/address');
        return $this->mapper->decodeAddress($responseData);
    }

    /**
     * Gets user's identity by id using API
     *
     * @param integer $userId
     *
     * @return Paysera_WalletApi_Entity_User_Identity
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserIdentity($userId)
    {
        Paysera_WalletApi_Util_Assert::isId($userId);
        $responseData = $this->get('user/' . $userId . '/identity');
        return $this->mapper->decodeIdentity($responseData);
    }

    /**
     * Gets user's wallets by id using API
     *
     * @param integer $userId
     *
     * @return Paysera_WalletApi_Entity_Wallet[]
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserWallets($userId)
    {
        Paysera_WalletApi_Util_Assert::isId($userId);
        $responseData = $this->get('user/' . $userId . '/wallets');
        return $this->mapper->decodeWallets($responseData);
    }

    /**
     * Get project by id
     *
     * @param int $projectId
     *
     * @return Paysera_WalletApi_Entity_Project
     */
    public function getProject($projectId)
    {
        $responseData = $this->get('project/' . $projectId);

        return $this->mapper->decodeProject($responseData);
    }

    /**
     * Creates project
     *
     * @param Paysera_WalletApi_Entity_Project $project
     *
     * @return Paysera_WalletApi_Entity_Project
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function saveProject(Paysera_WalletApi_Entity_Project $project)
    {
        if ($project->getId() === null) {
            throw new Paysera_WalletApi_Exception_LogicException("Project must have id property set");
        }

        $responseData = $this->put('project/' . $project->getId(), $this->mapper->encodeProject($project));

        return $this->mapper->decodeProject($responseData);
    }

    /**
     * Creates location
     *
     * @param int                               $projectId
     * @param Paysera_WalletApi_Entity_Location $location
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function createLocation($projectId, Paysera_WalletApi_Entity_Location $location)
    {
        $responseData = $this->post('project/'. $projectId .'/location', $this->mapper->encodeLocation($location));

        return $this->mapper->decodeLocation($responseData);
    }

    /**
     * Updates location
     *
     * @param Paysera_WalletApi_Entity_Location $location
     *
     * @return Paysera_WalletApi_Entity_Location
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function updateLocation(Paysera_WalletApi_Entity_Location $location)
    {
        if ($location->getId() === null) {
            throw new Paysera_WalletApi_Exception_LogicException("Location id has been not provided");
        }

        $responseData = $this->put('location/' . $location->getId(), $this->mapper->encodeLocation($location));

        return $this->mapper->decodeLocation($responseData);
    }

    /**
     * Get project locations
     *
     * @param int $projectId
     * @param Paysera_WalletApi_Entity_Location_SearchFilter $filter
     *
     * @return Paysera_WalletApi_Entity_Location[]
     */
    public function getProjectLocations(
        $projectId,
        Paysera_WalletApi_Entity_Location_SearchFilter $filter = null
    ) {
        if ($filter !== null) {
            $query = '?' . http_build_query($this->mapper->encodeLocationFilter($filter), null, '&');
        } else {
            $query = '';
        }

        $responseData = $this->get('project/' . $projectId . '/locations' . $query);

        $locations = array();
        foreach ($responseData as $item) {
            $locations[] = $this->mapper->decodeLocation($item);
        }

        return $locations;
    }

    /**
     * Get all locations
     *
     * @param Paysera_WalletApi_Entity_Location_SearchFilter $filter
     * @return Paysera_WalletApi_Entity_Search_Result|Paysera_WalletApi_Entity_Location[]
     */
    public function getLocations(Paysera_WalletApi_Entity_Location_SearchFilter $filter = null)
    {
        if ($filter !== null) {
            $query = '?' . http_build_query($this->mapper->encodeLocationFilter($filter), null, '&');
        } else {
            $query = '';
        }
        return $this->mapper->decodeLocationSearchResult(
            $this->get('locations' . $query)
        );
    }

    /**
     * Get Location pay categories
     *
     * @param $locale
     * @return Paysera_WalletApi_Entity_PayCategory[]
     */
    public function getLocationPayCategories($locale)
    {
        $query = '?' . http_build_query(array('locale' => $locale), null, '&');

        return $this->mapper->decodeLocationPayCategories(
            $this->get('locations/pay-categories' . $query)
        );
    }

    /**
     * If clientId is not provided will return current client
     *
     * @param null|int $clientId
     *
     * @return Paysera_WalletApi_Entity_Client
     */
    public function getClient($clientId = null)
    {
        $path = $clientId === null ? 'client' : 'client/' . $clientId;
        $responseData = $this->get($path);

        return $this->mapper->decodeClient($responseData);
    }

    /**
     * Gets Clients by specified Filter
     *
     * @param Paysera_WalletApi_Entity_Client_SearchFilter $filter
     *
     * @return Paysera_WalletApi_Entity_Client[]
     */
    public function getClients(Paysera_WalletApi_Entity_Client_SearchFilter $filter)
    {
        $query = '?' . http_build_query($this->mapper->encodeClientFilter($filter), null, '&');

         return $this->mapper->decodeClientSearchResult(
            $this->get('clients' . $query)
        );
    }

    /**
     * Create client
     *
     * @param Paysera_WalletApi_Entity_Client $client
     *
     * @return Paysera_WalletApi_Entity_Client
     */
    public function createClient(Paysera_WalletApi_Entity_Client $client)
    {
        $responseData = $this->post('client', $this->mapper->encodeClient($client));

        return $this->mapper->decodeClient($responseData);
    }

    /**
     * Update client
     *
     * @param Paysera_WalletApi_Entity_Client $client
     *
     * @return Paysera_WalletApi_Entity_Client
     */
    public function updateClient(Paysera_WalletApi_Entity_Client $client)
    {
        $responseData = $this->put('client/' . $client->getId(), $this->mapper->encodeClient($client));

        return $this->mapper->decodeClient($responseData);
    }
}
