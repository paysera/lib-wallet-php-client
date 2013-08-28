<?php

/**
 * Class for encoding and decoding entities to and from arrays
 */
class Paysera_WalletApi_Mapper
{

    /**
     * Decodes access token object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     *
     * @throws InvalidArgumentException
     */
    public function decodeAccessToken($data)
    {
        if ($data['token_type'] !== 'mac' || $data['mac_algorithm'] !== 'hmac-sha-256') {
            throw new InvalidArgumentException('Only mac tokens with hmac-sha-256 algorithm are supported');
        }
        return Paysera_WalletApi_Entity_MacAccessToken::create()
            ->setExpiresAt(time() + $data['expires_in'])
            ->setMacId($data['access_token'])
            ->setMacKey($data['mac_key'])
            ->setRefreshToken(isset($data['refresh_token']) ? $data['refresh_token'] : null);
    }

    /**
     * Encodes payment object to array. Used for creating payment
     *
     * @param Paysera_WalletApi_Entity_Payment $payment
     *
     * @return array
     *
     * @throws Paysera_WalletApi_Exception_LogicException    if some fields are invalid, ie. payment already has an ID
     */
    public function encodePayment(Paysera_WalletApi_Entity_Payment $payment)
    {
        if ($payment->getId() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot create already existing payment');
        }
        $result = array();
        if (($description = $payment->getDescription()) !== null) {
            $result['description'] = $description;
        }
        $price = $payment->getPrice();
        if ($price !== null) {
            $result['price'] = $price->getAmountInCents();
            $result['currency'] = $price->getCurrency();
        }
        $cashback = $payment->getCashback();
        if ($cashback !== null) {
            if ($price !== null && $price->getCurrency() !== $cashback->getCurrency()) {
                throw new Paysera_WalletApi_Exception_LogicException('Price and cashback currency must be the same');
            }
            $result['cashback'] = $cashback->getAmountInCents();
            $result['currency'] = $cashback->getCurrency();
        }
        if ($payment->hasItems()) {
            $items = array();
            foreach ($payment->getItems() as $item) {
                $items[] = $this->encodeItem($item);
            }
            $result['items'] = $items;
        }
        if (($beneficiary = $payment->getBeneficiary()) !== null) {
            $result['beneficiary'] = $this->encodeWalletIdentifier($beneficiary);
        }
        if (($freezeFor = $payment->getFreezeFor()) !== null) {
            $result['freeze_for'] = $freezeFor;
        }
        if (($parameters = $payment->getParameters()) !== null) {
            $result['parameters'] = $parameters;
        }

        if (!(isset($result['description']) && isset($result['price']) || isset($result['items']))) {
            throw new Paysera_WalletApi_Exception_LogicException('Description and price are required if items are not set');
        }

        return $result;
    }

    /**
     * Decodes payment object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function decodePayment($data)
    {
        $payment = new Paysera_WalletApi_Entity_Payment();
        $this->setProperty($payment, 'id', $data['id']);
        $this->setProperty($payment, 'transactionKey', $data['transaction_key']);
        $this->setProperty($payment, 'createdAt', $this->createDateTimeFromTimestamp($data['created_at']));
        $this->setProperty($payment, 'status', $data['status']);

        $payment->setPrice(
            Paysera_WalletApi_Entity_Money::create()->setAmountInCents($data['price'])->setCurrency($data['currency'])
        );
        if (isset($data['cashback'])) {
            $payment->setCashback(
                Paysera_WalletApi_Entity_Money::create()->setAmountInCents($data['cashback'])->setCurrency($data['currency'])
            );
        }
        if (isset($data['wallet'])) {
            $this->setProperty($payment, 'walletId', $data['wallet']);
        }
        if (isset($data['confirmed_at'])) {
            $this->setProperty($payment, 'confirmedAt', $this->createDateTimeFromTimestamp($data['confirmed_at']));
        }
        if (isset($data['freeze_until'])) {
            $payment->setFreezeUntil($this->createDateTimeFromTimestamp($data['freeze_until']));
        }
        if (isset($data['freeze_for'])) {
            $payment->setFreezeFor($data['freeze_for']);
        }
        if (isset($data['description'])) {
            $payment->setDescription($data['description']);
        }
        if (isset($data['items'])) {
            $items = array();
            foreach ($data['items'] as $item) {
                $items[] = $this->decodeItem($item);
            }
            $payment->setItems($items);
        }
        if (isset($data['beneficiary'])) {
            $payment->setBeneficiary($this->decodeWalletIdentifier($data['beneficiary']));
        }
        if (isset($data['parameters'])) {
            $payment->setParameters($data['parameters']);
        }

        return $payment;
    }

    /**
     * Encodes item object to array
     *
     * @param Paysera_WalletApi_Entity_Item $item
     *
     * @return array
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    protected function encodeItem(Paysera_WalletApi_Entity_Item $item)
    {
        $result = array();
        if ($item->getTitle() === null || $item->getPrice() === null) {
            throw new Paysera_WalletApi_Exception_LogicException('Each item must have title and price');
        }
        $result['title'] = $item->getTitle();
        if (($description = $item->getDescription()) !== null) {
            $result['description'] = $description;
        }
        if (($imageUri = $item->getImageUri()) !== null) {
            $result['image_uri'] = $imageUri;
        }
        $result['price'] = $item->getPrice()->getAmountInCents();
        $result['currency'] = $item->getPrice()->getCurrency();
        $result['quantity'] = $item->getQuantity();
        if (($parameters = $item->getParameters()) !== null) {
            $result['parameters'] = $parameters;
        }
        return $result;
    }

    /**
     * Decodes item object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Item
     */
    protected function decodeItem($data)
    {
        $item = new Paysera_WalletApi_Entity_Item();
        $item->setTitle($data['title']);
        $price = Paysera_WalletApi_Entity_Money::create()
            ->setAmountInCents($data['price'])
            ->setCurrency($data['currency']);
        $item->setPrice($price);
        $item->setQuantity($data['quantity']);
        if (isset($data['description'])) {
            $item->setDescription($data['description']);
        }
        if (isset($data['image_uri'])) {
            $item->setImageUri($data['image_uri']);
        }
        if (isset($data['parameters'])) {
            $item->setParameters($data['parameters']);
        }
        return $item;
    }

    /**
     * Encodes allowance object to array. Used for creating allowance
     *
     * @param Paysera_WalletApi_Entity_Allowance $allowance
     *
     * @return array
     *
     * @throws Paysera_WalletApi_Exception_LogicException    if some fields are invalid, ie. allowance already has an ID
     */
    public function encodeAllowance(Paysera_WalletApi_Entity_Allowance $allowance)
    {
        if ($allowance->getId() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot create already existing allowance');
        }
        $result = array();
        if (($description = $allowance->getDescription()) !== null) {
            $result['description'] = $description;
        }
        $maxPrice = $allowance->getMaxPrice();
        if ($maxPrice === null && !$allowance->hasLimits()) {
            throw new Paysera_WalletApi_Exception_LogicException('Allowance must have max price or limits set');
        }

        $currency = null;
        if ($maxPrice !== null) {
            $currency = $maxPrice->getCurrency();
        }
        foreach ($allowance->getLimits() as $limit) {
            $currentCurrency = $limit->getMaxPrice()->getCurrency();
            if ($currency === null) {
                $currency = $currentCurrency;
            } elseif ($currency !== $currentCurrency) {
                throw new Paysera_WalletApi_Exception_LogicException('All sums in allowance must have the same currency');
            }
        }
        $result['currency'] = $currency;

        if ($maxPrice !== null) {
            $result['max_price'] = $maxPrice->getAmountInCents();
        }

        if ($allowance->getValidFor() !== null && $allowance->getValidUntil() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Only one of validFor and validUntil can be provided');
        } elseif ($allowance->getValidFor() !== null) {
            $result['valid_for'] = $allowance->getValidFor();
        } elseif ($allowance->getValidUntil() !== null) {
            $result['valid_until'] = $allowance->getValidUntil()->getTimestamp();
        }

        if ($allowance->hasLimits()) {
            $limitList = array();
            foreach ($allowance->getLimits() as $limit) {
                if ($limit->getMaxPrice() === null || $limit->getPeriod() === null) {
                    throw new Paysera_WalletApi_Exception_LogicException('At least one limit has no price or no period');
                }
                $limitList[] = array(
                    'max_price' => $limit->getMaxPrice()->getAmountInCents(),
                    'period' => $limit->getPeriod(),
                );
            }
            $result['limits'] = $limitList;
        }

        return $result;
    }

    /**
     * Decodes allowance object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function decodeAllowance($data)
    {
        $allowance = new Paysera_WalletApi_Entity_Allowance();
        $this->setProperty($allowance, 'id', $data['id']);
        $this->setProperty($allowance, 'transactionKey', $data['transaction_key']);
        $this->setProperty($allowance, 'createdAt', $this->createDateTimeFromTimestamp($data['created_at']));
        $this->setProperty($allowance, 'status', $data['status']);
        if (isset($data['wallet'])) {
            $this->setProperty($allowance, 'wallet', $data['wallet']);
        }
        if (isset($data['confirmed_at'])) {
            $this->setProperty($allowance, 'confirmedAt', $this->createDateTimeFromTimestamp($data['confirmed_at']));
        }
        if (isset($data['valid_until'])) {
            $allowance->setValidUntil($this->createDateTimeFromTimestamp($data['valid_until']));
        }
        if (isset($data['valid_for'])) {
            $allowance->setValidFor($data['valid_for']);
        }
        if (isset($data['description'])) {
            $allowance->setDescription($data['description']);
        }
        if (isset($data['max_price'])) {
            $allowance->setMaxPrice(
                Paysera_WalletApi_Entity_Money::create()->setAmountInCents($data['max_price'])->setCurrency($data['currency'])
            );
        }
        if (isset($data['limits'])) {
            foreach ($data['limits'] as $limitInfo) {
                $price = Paysera_WalletApi_Entity_Money::create()
                    ->setCurrency($data['currency'])
                    ->setAmountInCents($limitInfo['max_price']);
                $limit = Paysera_WalletApi_Entity_Limit::create()
                    ->setPeriod($limitInfo['period'])
                    ->setMaxPrice($price);
                $allowance->addLimit($limit);
            }
        }

        return $allowance;
    }

    /**
     * Encodes transaction object to array. Used for creating transaction
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     *
     * @return array
     *
     * @throws Paysera_WalletApi_Exception_LogicException    if some fields are invalid, ie. transaction already has a key
     */
    public function encodeTransaction(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        if ($transaction->getKey() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Cannot create already existing transaction');
        }
        if (
            count($transaction->getPayments()) === 0
            && count($transaction->getPaymentIdList()) === 0
            && $transaction->getAllowance() === null
            && $transaction->getAllowanceId() === null
        ) {
            throw new Paysera_WalletApi_Exception_LogicException('Transaction must have at least one payment or allowance');
        }

        $result = array();

        $payments = array();
        foreach ($transaction->getPayments() as $payment) {
            if ($payment->getId() === null) {
                $payments[] = $this->encodePayment($payment);
            } else {
                $payments[] = $payment->getId();
            }
        }
        foreach ($transaction->getPaymentIdList() as $paymentId) {
            $payments[] = $paymentId;
        }

        if (count($payments) > 0) {
            $result['payments'] = $payments;
        }

        if ($transaction->getReserveFor() !== null && $transaction->getReserveUntil() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Only one of reserveFor and reserveUntil can be provided');
        } elseif ($transaction->getReserveFor() !== null) {
            $result['reserve_for'] = $transaction->getReserveFor();
        } elseif ($transaction->getReserveUntil() !== null) {
            $result['reserve_until'] = $transaction->getReserveUntil()->getTimestamp();
        }

        $allowance = null;
        $allowanceId = null;
        if ($transaction->getAllowance() !== null) {
            if ($transaction->getAllowance()->getId() === null) {
                $allowance = $this->encodeAllowance($transaction->getAllowance());
            } else {
                $allowanceId = $transaction->getAllowance()->getId();
            }
        } elseif ($transaction->getAllowanceId() !== null) {
            $allowanceId = $transaction->getAllowanceId();
        }
        if ($allowance !== null) {
            $result['allowance'] = array('data' => $allowance, 'optional' => $transaction->getAllowanceOptional());
        } elseif ($allowanceId !== null) {
            $result['allowance'] = array('id' => $allowanceId, 'optional' => $transaction->getAllowanceOptional());
        }

        $result['use_allowance'] = $transaction->getUseAllowance();
        $result['suggest_allowance'] = $transaction->getSuggestAllowance();

        if ($transaction->isAutoConfirm() !== null) {
            $result['auto_confirm'] = $transaction->isAutoConfirm();
        }
        if ($transaction->getRedirectUri() !== null) {
            $result['redirect_uri'] = $transaction->getRedirectUri();
        }
        if ($transaction->isCallbacksDisabled()) {
            $result['callback_uri'] = false;
        } elseif ($transaction->getCallbackUri() !== null) {
            $result['callback_uri'] = $transaction->getCallbackUri();
        }
        if ($transaction->getUserInformation() !== null) {
            $result['user'] = $this->encodeUserInformation($transaction->getUserInformation());
        }

        return $result;
    }

    /**
     * Decodes transaction object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function decodeTransaction($data)
    {
        $transaction = new Paysera_WalletApi_Entity_Transaction();
        $this->setProperty($transaction, 'key', $data['transaction_key']);
        $this->setProperty($transaction, 'createdAt', $this->createDateTimeFromTimestamp($data['created_at']));
        $this->setProperty($transaction, 'status', $data['status']);
        if (isset($data['type'])) {
            $this->setProperty($transaction, 'type', $data['type']);
        }
        if (isset($data['wallet'])) {
            $this->setProperty($transaction, 'wallet', $data['wallet']);
        }
        if (isset($data['confirmed_at'])) {
            $this->setProperty($transaction, 'confirmedAt', $this->createDateTimeFromTimestamp($data['confirmed_at']));
        }
        if (isset($data['correlation_key'])) {
            $this->setProperty($transaction, 'correlationKey', $data['correlation_key']);
        }
        if (isset($data['payments'])) {
            $payments = array();
            $paymentIdList = array();
            foreach ($data['payments'] as $paymentInfo) {
                $payment = $this->decodePayment($paymentInfo);
                $payments[] = $payment;
                $paymentIdList[] = $payment->getId();
            }
            $this->setProperty($transaction, 'payments', $payments);
            $this->setProperty($transaction, 'paymentIdList', $paymentIdList);
        }
        if (isset($data['allowance'])) {
            $allowance = $this->decodeAllowance($data['allowance']['data']);
            $this->setProperty($transaction, 'allowance', $allowance);
            $transaction->setAllowanceOptional($data['allowance']['optional']);
        }
        if (isset($data['use_allowance'])) {
            $transaction->setUseAllowance($data['use_allowance']);
        }
        if (isset($data['suggest_allowance'])) {
            $transaction->setSuggestAllowance($data['suggest_allowance']);
        }
        if (isset($data['auto_confirm'])) {
            $transaction->setAutoConfirm($data['auto_confirm']);
        }
        if (isset($data['redirect_uri'])) {
            $transaction->setRedirectUri($data['redirect_uri']);
        }
        if (isset($data['callback_uri'])) {
            $transaction->setCallbackUri($data['callback_uri']);
        }
        if (isset($data['user'])) {
            $transaction->setUserInformation($this->decodeUserInformation($data['user']));
        }

        return $transaction;
    }

    /**
     * Encodes payer
     *
     * @param Paysera_WalletApi_Entity_WalletIdentifier $walletIdentifier
     *
     * @return array
     */
    public function encodePayer(Paysera_WalletApi_Entity_WalletIdentifier $walletIdentifier)
    {
        return array(
            'payer' => $this->encodeIdentifier($walletIdentifier),
        );
    }

    /**
     * Encodes identifier
     *
     * @param Paysera_WalletApi_Entity_WalletIdentifier $walletIdentifier
     *
     * @return array
     */
    public function encodeIdentifier(Paysera_WalletApi_Entity_WalletIdentifier $walletIdentifier)
    {
        if ($walletIdentifier->getCard() !== null) {
            return array(
                'card' => array(
                    'issuer' => $walletIdentifier->getCard()->getIssuer(),
                    'number' => $walletIdentifier->getCard()->getNumber(),
                ),
            );
        }

        if ($walletIdentifier->getEmail() !== null) {
            return array(
                'email' => $walletIdentifier->getEmail(),
            );
        }

        if ($walletIdentifier->getId() !== null) {
            return array(
                'id' => $walletIdentifier->getId(),
            );
        }

        if ($walletIdentifier->getPhone() !== null) {
            return array(
                'phone' => $walletIdentifier->getPhone(),
            );
        }
    }

    /**
     * Encodes user information object to array
     *
     * @param Paysera_WalletApi_Entity_UserInformation $userInformation
     *
     * @return array
     */
    public function encodeUserInformation(Paysera_WalletApi_Entity_UserInformation $userInformation)
    {
        return array('email' => $userInformation->getEmail());
    }

    /**
     * Encodes money object to array
     *
     * @param Paysera_WalletApi_Entity_Money $price
     *
     * @return array
     */
    public function encodePrice(Paysera_WalletApi_Entity_Money $price)
    {
        return array('price' => $price->getAmountInCents(), 'currency' => $price->getCurrency());
    }

    /**
     * Decodes user information object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_UserInformation
     */
    public function decodeUserInformation($data)
    {
        return Paysera_WalletApi_Entity_UserInformation::create()
            ->setEmail($data['email']);
    }

    /**
     * Encodes wallet identifier object to array
     *
     * @param Paysera_WalletApi_Entity_WalletIdentifier $walletIdentifier
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function encodeWalletIdentifier(Paysera_WalletApi_Entity_WalletIdentifier $walletIdentifier)
    {
        if ($walletIdentifier->getId() !== null) {
            return array('id' => $walletIdentifier->getId());
        } elseif ($walletIdentifier->getEmail() !== null) {
            return array('email' => $walletIdentifier->getEmail());
        } elseif ($walletIdentifier->getPhone() !== null) {
            return array('phone' => $walletIdentifier->getPhone());
        } else {
            throw new \InvalidArgumentException('Wallet identifier has no identifier set');
        }
    }

    /**
     * Decodes wallet identifier object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_WalletIdentifier
     */
    public function decodeWalletIdentifier($data)
    {
        $walletIdentifier = new Paysera_WalletApi_Entity_WalletIdentifier();
        if (is_int($data)) {
            $walletIdentifier->setId($data);
        } elseif (is_array($data)) {
            if (isset($data['id'])) {
                $walletIdentifier->setId($data['id']);
            }
            if (isset($data['email'])) {
                $walletIdentifier->setEmail($data['email']);
            }
            if (isset($data['phone'])) {
                $walletIdentifier->setPhone($data['phone']);
            }
        }
        return $walletIdentifier;
    }

    /**
     * Decodes wallet object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Wallet
     */
    public function decodeWallet($data)
    {
        $wallet = new Paysera_WalletApi_Entity_Wallet();

        $this->setProperty($wallet, 'id', $data['id']);
        $this->setProperty($wallet, 'owner', $data['owner']);
        if (isset($data['account'])) {
            $this->setProperty($wallet, 'account', $this->decodeAccount($data['account']));
        }

        return $wallet;
    }

    /**
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Wallet_Account
     */
    protected function decodeAccount($data)
    {
        $account = new Paysera_WalletApi_Entity_Wallet_Account();

        $this->setProperty($account, 'number', $data['number']);
        if (isset($data['owner_title'])) {
            $this->setProperty($account, 'ownerTitle', $data['owner_title']);
        }
        if (isset($data['owner_display_name'])) {
            $this->setProperty($account, 'ownerDisplayName', $data['owner_display_name']);
        }
        if (isset($data['description'])) {
            $this->setProperty($account, 'description', $data['description']);
        }

        return $account;
    }

    /**
     * @param $data
     *
     * @return Paysera_WalletApi_Entity_Wallet[]
     */
    public function decodeWallets($data)
    {
        $result = array();
        foreach ($data as $key => $item) {
            $result[$key] = $this->decodeWallet($item);
        }
        return $result;
    }

    /**
     * Decodes wallet balance object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Wallet_Balance
     */
    public function decodeWalletBalance($data)
    {
        $balance = new Paysera_WalletApi_Entity_Wallet_Balance();

        foreach ($data as $currency => $balanceData) {
            $balance->setCurrencyBalance(
                $currency,
                isset($balanceData['at_disposal']) ? $balanceData['at_disposal'] : 0,
                isset($balanceData['reserved']) ? $balanceData['reserved'] : 0
            );
        }

        return $balance;
    }

    /**
     * Encodes statement filter entity to an array
     *
     * @param Paysera_WalletApi_Entity_Statement_SearchFilter $filter
     *
     * @return array
     */
    public function encodeStatementFilter(Paysera_WalletApi_Entity_Statement_SearchFilter $filter)
    {
        $data = array();
        if ($filter->getLimit() !== null) {
            $data['limit'] = $filter->getLimit();
        }
        if ($filter->getOffset() !== null) {
            $data['offset'] = $filter->getOffset();
        }
        if (count($filter->getCurrencies()) > 0) {
            $data['currency'] = implode(',', $filter->getCurrencies());
        }
        if ($filter->getFromDate() !== null) {
            $data['from'] = $filter->getFromDate()->getTimestamp();
        }
        if ($filter->getToDate() !== null) {
            $data['to'] = $filter->getToDate()->getTimestamp();
        }
        return $data;
    }

    /**
     * Decodes statement search result from data array
     *
     * @param $data
     *
     * @return Paysera_WalletApi_Entity_Statement_SearchResult
     */
    public function decodeStatementSearchResult($data)
    {
        $result = new Paysera_WalletApi_Entity_Statement_SearchResult();
        $statements = array();
        foreach ($data['statements'] as $statementData) {
            $statements[] = $this->decodeStatement($statementData);
        }
        $metadata = $data['_metadata'];
        $this->setProperty($result, 'statements', $statements);
        $this->setProperty($result, 'total', $metadata['total']);
        $this->setProperty($result, 'offset', $metadata['offset']);
        $this->setProperty($result, 'limit', $metadata['limit']);
        return $result;
    }

    /**
     * Decodes statement entity from data array
     *
     * @param $data
     *
     * @return Paysera_WalletApi_Entity_Statement
     */
    public function decodeStatement($data)
    {
        $statement = new Paysera_WalletApi_Entity_Statement();
        $this->setProperty($statement, 'id', $data['id']);
        $this->setProperty($statement, 'amount', new Paysera_WalletApi_Entity_Money($data['amount'], $data['currency']));
        $this->setProperty($statement, 'date', new DateTime('@' . $data['date']));
        $this->setProperty($statement, 'details', $data['details']);
        if (isset($data['type'])) {
            $this->setProperty($statement, 'type', $data['type']);
        }
        if (isset($data['other_party'])) {
            $this->setProperty($statement, 'otherParty', $this->decodeStatementParty($data['other_party']));
        }
        if (isset($data['transfer_id'])) {
            $this->setProperty($statement, 'transferId', $data['transfer_id']);
        }
        return $statement;
    }

    public function decodeStatementParty($data)
    {
        $party = new Paysera_WalletApi_Entity_Statement_Party();
        $this->setProperties($party, $data, array('name', 'code', 'bic'));
        if (isset($data['account_number'])) {
            $this->setProperty($party, 'accountNumber', $data['account_number']);
        }
        return $party;
    }

    /**
     * Decodes user object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_User
     */
    public function decodeUser($data)
    {
        $user = new Paysera_WalletApi_Entity_User();

        $this->setProperty($user, 'id', $data['id']);
        if (isset($data['email'])) {
            $this->setProperty($user, 'email', $data['email']);
        }
        if (isset($data['phone'])) {
            $this->setProperty($user, 'phone', $data['phone']);
        }
        if (isset($data['address'])) {
            $this->setProperty($user, 'address', $this->decodeAddress($data['address']));
        }
        if (isset($data['identity'])) {
            $this->setProperty($user, 'identity', $this->decodeIdentity($data['identity']));
        }
        if (isset($data['wallets'])) {
            $this->setProperty($user, 'wallets', $data['wallets']);
        }

        return $user;
    }

    /**
     * @param $data
     *
     * @return Paysera_WalletApi_Entity_User_Address
     */
    public function decodeAddress($data)
    {
        $address = new Paysera_WalletApi_Entity_User_Address();

        $this->setProperty($address, 'country', $data['country']);
        $this->setProperty($address, 'city', $data['city']);
        $this->setProperty($address, 'street', $data['street']);
        if (isset($data['post_index'])) {
            $this->setProperty($address, 'postIndex', $data['post_index']);
        }

        return $address;
    }

    /**
     * @param $data
     *
     * @return Paysera_WalletApi_Entity_User_Identity
     */
    public function decodeIdentity($data)
    {
        $identity = new Paysera_WalletApi_Entity_User_Identity();

        $this->setProperty($identity, 'name', $data['name']);
        $this->setProperty($identity, 'surname', $data['surname']);
        if (isset($data['nationality'])) {
            $this->setProperty($identity, 'nationality', $data['nationality']);
        }
        if (isset($data['code'])) {
            $this->setProperty($identity, 'code', $data['code']);
        }

        return $identity;
    }

    /**
     * @param $pin
     *
     * @return array
     */
    public function encodePin($pin)
    {
        return array('pin' => $pin);
    }

    /**
     * Decodes Money object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function decodeMoney($data)
    {
        return Paysera_WalletApi_Entity_Money::create()->setAmountInCents($data['amount'])->setCurrency($data['currency']);
    }


    protected function setProperties($object, array $data, array $properties)
    {
        foreach ($properties as $propertyName) {
            if (isset($data[$propertyName])) {
                $this->setProperty($object, $propertyName, $data[$propertyName]);
            }
        }
    }

    /**
     * Sets property to object. Property can be inaccessible (protected/private)
     *
     * @param object $object
     * @param string $property
     * @param mixed  $value
     */
    protected function setProperty($object, $property, $value)
    {
        $reflectionObject = new ReflectionObject($object);
        $reflectionProperty = $reflectionObject->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * Creates DateTime object from integer UNIX timestamp
     *
     * @param integer $timestamp
     *
     * @return DateTime
     */
    protected function createDateTimeFromTimestamp($timestamp)
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        return $dateTime;
    }
}