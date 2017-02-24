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
        $commission = $payment->getCommission();
        if ($commission !== null) {
            $result['commission'] = $this->encodeCommission($commission);
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
        if (($password = $payment->getPaymentPassword()) !== null) {
            $result['password'] = $this->encodePaymentPassword($password);
        }
        if (($priceRules = $payment->getPriceRules()) !== null) {
            $result['price_rules'] = $this->encodePriceRules($priceRules);
        }
        if (($purpose = $payment->getPurpose()) !== null) {
            $result['purpose'] = $purpose;
        }
        if (($fundsSource = $payment->getFundsSource()) !== null) {
            $result['funds_source'] = $this->encodeFundsSource($fundsSource);
        }

        if (!(isset($result['description']) && isset($result['price']) || isset($result['items']))) {
            throw new Paysera_WalletApi_Exception_LogicException(
                'Description and price are required if items are not set'
            );
        }

        return $result;
    }

    /**
     * Encodes transaction funds sources to array
     *
     * @param Paysera_WalletApi_Entity_FundsSource[] $fundsSources
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     *
     * @return array
     */
    public function encodeFundsSources(array $fundsSources)
    {
        $result = array();

        foreach ($fundsSources as $key => $fundsSource) {
            $result[$key] = $this->encodeFundsSource($fundsSource);
        }

        return array('funds_sources' => $result);
    }

    /**
     * Encodes payment funds source to array
     *
     * @param Paysera_WalletApi_Entity_FundsSource $fundsSource
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     *
     * @return array
     */
    public function encodeFundsSource(Paysera_WalletApi_Entity_FundsSource $fundsSource)
    {
        if (0 === strlen($fundsSource->getType()) && 0 === strlen($fundsSource->getDetails())) {
            throw new Paysera_WalletApi_Exception_LogicException("Funds source type or details required");
        }

        $result = array();

        if ($fundsSource->getType() !== null) {
            $result['type'] = $fundsSource->getType();
        }

        if (strlen($fundsSource->getDetails()) > 0) {
            $result['details'] = $fundsSource->getDetails();
        }

        return $result;
    }

    /**
     * Decodes funds source object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_FundsSource
     */
    public function decodeFundsSource($data)
    {
        $fundsSource = new Paysera_WalletApi_Entity_FundsSource();

        if (!empty($data['type'])) {
            $fundsSource->setType($data['type']);
        }
        if (!empty($data['details'])) {
            $fundsSource->setDetails($data['details']);
        }

        return $fundsSource;
    }

    /**
     * Encodes transaction restriction object to array
     *
     * @param Paysera_WalletApi_Entity_Restrictions $restrictions
     * @return array
     */
    public function encodeRestrictions(Paysera_WalletApi_Entity_Restrictions $restrictions)
    {
        $result = array();
        if ($accountOwnerRestriction = $restrictions->getAccountOwnerRestriction()) {
            $requirements = array();
            if ($accountOwnerRestriction->isIdentityRequired()) {
                $requirements[] = 'identity';
            }

            $result['account_owner'] = array(
                'type' => $accountOwnerRestriction->getType(),
                'requirements' => $requirements
            );
        }

        return $result;
    }

    /**
     * Encodes project object to array
     *
     * @param Paysera_WalletApi_Entity_Project $project
     *
     * @return array
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function encodeProject(Paysera_WalletApi_Entity_Project $project)
    {
        $result = array();

        if ($project->getTitle() === null) {
            throw new Paysera_WalletApi_Exception_LogicException("Project must have title property set");
        }
        $result['title'] = $project->getTitle();

        if ($project->getDescription()) {
            $result['description'] = $project->getDescription();
        }

        if ($project->getWalletId()) {
            $result['wallet_id'] = $project->getWalletId();
        }

        return $result;
    }

    /**
     * Decodes project object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Project
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function decodeProject($data)
    {
        $project = new Paysera_WalletApi_Entity_Project();

        $this->setProperty($project, 'id', $data['id']);
        $this->setProperty($project, 'title', $data['title']);
        if (!empty($data['description'])) {
            $this->setProperty($project, 'description', $data['description']);
        }
        if (!empty($data['wallet_id'])) {
            $this->setProperty($project, 'walletId', $data['wallet_id']);
        }

        return $project;
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
        if (isset($data['commission'])) {
            $payment->setCommission($this->decodeCommission($data['commission'], $data['currency']));
        }
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
        if (isset($data['password'])) {
            $payment->setPaymentPassword($this->decodePaymentPassword($data['password']));
        }
        if (isset($data['price_rules'])) {
            $payment->setPriceRules($this->decodePriceRules($data['price_rules'], $data['currency']));
        }
        if (isset($data['purpose'])) {
            $payment->setPurpose($data['purpose']);
        }
        if (isset($data['funds_source'])) {
            $payment->setFundsSource($this->decodeFundsSource($data['funds_source']));
        }

        return $payment;
    }

    /**
     * @param array $data
     * @return Paysera_WalletApi_Entity_Search_Result
     */
    public function decodePaymentSearchResult($data)
    {
        $payments = array();
        foreach ($data['payments'] as $payment) {
            $payments[] = $this->decodePayment($payment);
        }

        $result = new Paysera_WalletApi_Entity_Search_Result($payments);
        $metadata = $data['_metadata'];
        $this->setProperty($result, 'total', $metadata['total']);
        $this->setProperty($result, 'offset', $metadata['offset']);
        $this->setProperty($result, 'limit', $metadata['limit']);
        return $result;
    }

    /**
     * Encodes payment commission to array
     *
     * @param Paysera_WalletApi_Entity_Commission $commission
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     *
     * @return array
     */
    protected function encodeCommission(Paysera_WalletApi_Entity_Commission $commission)
    {
        if (null === $commission->getOutCommission() && null === $commission->getInCommission()) {
            throw new Paysera_WalletApi_Exception_LogicException("In our Out commission required");
        }

        $result = array();

        if ($commission->getOutCommission() !== null) {
            $result['out_commission'] = $commission->getOutCommission()->getAmountInCents();
        }

        if ($commission->getInCommission() !== null) {
            $result['in_commission'] = $commission->getInCommission()->getAmountInCents();
        }

        return $result;
    }

    /**
     * Decodes payment commssion object from array
     *
     * @param $data
     * @param $currency
     *
     * @return Paysera_WalletApi_Entity_Commission
     */
    protected function decodeCommission($data, $currency)
    {
        $commission = new Paysera_WalletApi_Entity_Commission();

        if (isset($data['in_commission'])) {
            $commission->setInCommission(
                Paysera_WalletApi_Entity_Money::create()->setAmountInCents($data['in_commission'])->setCurrency($currency)
            );
        }

        if (isset($data['out_commission'])) {
            $commission->setOutCommission(
                Paysera_WalletApi_Entity_Money::create()->setAmountInCents($data['out_commission'])->setCurrency($currency)
            );
        }

        return $commission;
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
        if ($item->getTotalPrice() !== null) {
            $result['total_price'] = $item->getTotalPrice()->getAmountInCents();
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
        if (isset($data['total_price'])) {
            $item->setTotalPrice(
                Paysera_WalletApi_Entity_Money::create()
                    ->setAmountInCents($data['total_price'])
                    ->setCurrency($data['currency'])
            );
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
            $result['limits'] = array();
            foreach ($allowance->getLimits() as $limit) {
                if ($limit->getMaxPrice() === null || $limit->getTime() === null) {
                    throw new Paysera_WalletApi_Exception_LogicException('At least one limit has no price or no time');
                }
                $limitData = array(
                    'max_price' => $limit->getMaxPrice()->getAmountInCents(),
                    'time' => $limit->getTime(),
                );
                $result['limits'][] = $limitData;
            }
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
                    ->setTime($limitInfo['time'])
                    ->setMaxPrice($price)
                ;
                $allowance->addLimit($limit);
            }
        }

        return $allowance;
    }

    /**
     * Decodes transaction restriction object from array
     *
     * @param $data
     *
     * @return Paysera_WalletApi_Entity_Restrictions|null
     */
    public function decodeRestrictions($data)
    {
        if (isset($data['account_owner'])) {
            return
                Paysera_WalletApi_Entity_Restrictions::create()
                    ->setAccountOwnerRestriction(
                        Paysera_WalletApi_Entity_Restriction_UserRestriction::create()
                            ->setType(isset($data['account_owner']['type']) ? $data['account_owner']['type'] : null)
                            ->setIdentityRequired(
                                isset($data['account_owner']['requirements'])
                                && in_array('identity', $data['account_owner']['requirements'])
                            )
                    )
                ;
        }

        return null;
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

        if ($restrictions = $transaction->getRestrictions()) {
            $result['restrictions'] = $this->encodeRestrictions($restrictions);
        }
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
        if (count($transaction->getInquiries()) > 0) {
            foreach ($transaction->getInquiries() as $inquiry) {
                $result['inquiries'][] = $this->encodeInquiry($inquiry);
            }
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
        if (isset($data['restrictions'])) {
            $restrictions = $this->decodeRestrictions($data['restrictions']);
            $this->setProperty($transaction, 'restrictions', $restrictions);
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
        if (isset($data['location_id'])) {
            $transaction->setLocationId($data['location_id']);
        }
        if (isset($data['manager_id'])) {
            $this->setProperty($transaction, 'managerId', $data['manager_id']);
        }
        if (isset($data['inquiries'])) {
            foreach ($data['inquiries'] as $inquiryData) {
                $transaction->addInquiry($this->decodeInquiry($inquiryData));
            }
        }

        return $transaction;
    }

    /**
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Inquiry_InquiryResult
     */
    public function decodeInquiryResult($data)
    {
        $valueProviders = array(
            Paysera_WalletApi_Entity_Inquiry_InquiryItem::TYPE_USER_IDENTITY =>
                new Paysera_WalletApi_Mapper_IdentityMapper(),
            Paysera_WalletApi_Entity_Inquiry_InquiryItem::TYPE_PERSON_CODE =>
                new Paysera_WalletApi_Mapper_PlainValueMapper()
        );

        $mapper = new Paysera_WalletApi_Mapper_InquiryResultMapper($valueProviders);
        return $mapper->mapToEntity($data);
    }

    /**
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Inquiry_InquiryResult[]
     */
    public function decodeInquiryResults($data)
    {
        $inquiryItems = array();

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $inquiryItems[] = $this->decodeInquiryResult($item);
            }
        }

        return $inquiryItems;
    }

    /**
     * @param Paysera_WalletApi_Entity_TransactionPrice[] $transactionPrices
     *
     * @return array
     */
    public function encodeTransactionPrices(array $transactionPrices)
    {
        $result = array();
        foreach ($transactionPrices as $transactionPrice) {
            $result[$transactionPrice->getPaymentId()] = array(
                'price' => $transactionPrice->getPrice()->getAmountInCents(),
                'currency' => $transactionPrice->getPrice()->getCurrency()
            );
        }

        return $result;
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

        return array();
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
     * Encodes user object to array
     *
     * @param Paysera_WalletApi_Entity_User $user
     *
     * @return array
     */
    public function encodeUser(Paysera_WalletApi_Entity_User $user)
    {
        $data = array();
        if ($user->getEmail() !== null) {
            $data['email'] = $user->getEmail();
        }
        if ($user->getPhone() !== null) {
            $data['phone'] = $user->getPhone();
        }
        if ($identity = $user->getIdentity()) {
            $data['identity'] = $this->encodeUserIdentity($identity);
        }
        if ($address = $user->getAddress()) {
            $data['address'] = $this->encodeUserAddress($address);
        }
        return $data;
    }

    /**
     * Encodes user identity object to array
     *
     * @param Paysera_WalletApi_Entity_User_Identity $identity
     *
     * @return array
     */
    public function encodeUserIdentity(Paysera_WalletApi_Entity_User_Identity $identity)
    {
        $mapper = new Paysera_WalletApi_Mapper_IdentityMapper();
        return $mapper->mapFromEntity($identity);
    }

    /**
     * Encodes user address object to array
     *
     * @param Paysera_WalletApi_Entity_User_Address $address
     *
     * @return array
     */
    public function encodeUserAddress(Paysera_WalletApi_Entity_User_Address $address)
    {
        $data = array();
        if ($address->getCountry() !== null) {
            $data['country'] = $address->getCountry();
        }
        if ($address->getCity() !== null) {
            $data['city'] = $address->getCity();
        }
        if ($address->getStreet() !== null) {
            $data['street'] = $address->getStreet();
        }
        if ($address->getPostIndex() !== null) {
            $data['post_index'] = $address->getPostIndex();
        }
        return $data;
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
     * Encodes money
     *
     * @param Paysera_WalletApi_Entity_Money $money
     *
     * @return array
     */
    public function encodeMoney(Paysera_WalletApi_Entity_Money $money)
    {
        return array('amount' => $money->getAmountInCents(), 'currency' => $money->getCurrency());
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
     * @throws InvalidArgumentException
     */
    public function encodeWalletIdentifier(Paysera_WalletApi_Entity_WalletIdentifier $walletIdentifier)
    {
        if ($walletIdentifier->getId() !== null) {
            return array('id' => $walletIdentifier->getId());
        } elseif ($walletIdentifier->getEmail() !== null) {
            return array('email' => $walletIdentifier->getEmail());
        } elseif ($walletIdentifier->getPhone() !== null) {
            return array('phone' => $walletIdentifier->getPhone());
        } elseif ($walletIdentifier->getBarcode() !== null) {
            return array('barcode' => $walletIdentifier->getBarcode());
        } else {
            throw new InvalidArgumentException('Wallet identifier has no identifier set');
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
     * Encodes paymentPassword object to array
     *
     * @param Paysera_WalletApi_Entity_PaymentPassword $paymentPassword
     *
     * @return array
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function encodePaymentPassword(Paysera_WalletApi_Entity_PaymentPassword $paymentPassword)
    {
        $result = array();

        if ($paymentPassword->getType() === null) {
            throw new Paysera_WalletApi_Exception_LogicException('Password type must be provided');
        }
        if (
            $paymentPassword->getType() === Paysera_WalletApi_Entity_PaymentPassword::TYPE_PROVIDED
            && $paymentPassword->getValue() === null
        ) {
            throw new Paysera_WalletApi_Exception_LogicException('Password value must be provided');
        }
        if ($paymentPassword->getStatus() !== null) {
            throw new Paysera_WalletApi_Exception_LogicException('Status for payment password is read only');
        }

        $result['type'] = $paymentPassword->getType();

        if ($paymentPassword->getValue() !== null) {
            $result['value'] = $paymentPassword->getValue();
        }
        if ($paymentPassword->getOptional() !== null) {
            $result['optional'] = $paymentPassword->getOptional();
        }
        if ($paymentPassword->getCancelable() !== null) {
            $result['cancelable'] = $paymentPassword->getCancelable();
        }

        return $result;
    }

    /**
     * Decodes paymentPassword object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_PaymentPassword
     */
    public function decodePaymentPassword($data)
    {
        $paymentPassword = new Paysera_WalletApi_Entity_PaymentPassword();

        if (isset($data['type'])) {
            $paymentPassword->setType($data['type']);
        }
        if (isset($data['status'])) {
            $paymentPassword->setStatus($data['status']);
        }

        return $paymentPassword;
    }

    /**
     * Encodes priceRules object to array
     *
     * @param Paysera_WalletApi_Entity_PriceRules $priceRules
     *
     * @return array
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function encodePriceRules(Paysera_WalletApi_Entity_PriceRules $priceRules)
    {
        $result = array();

        if ($priceRules->getMin() !== null) {
            $result['min'] = $priceRules->getMin()->getAmountInCents();
        }
        if ($priceRules->getMax() !== null) {
            $result['max'] = $priceRules->getMax()->getAmountInCents();
        }
        if (count($priceRules->getChoices()) > 0) {
            $result['choices'] = array();
            foreach ($priceRules->getChoices() as $choice) {
                $result['choices'][] = $choice->getAmountInCents();
            }
        }

        return $result;
    }

    /**
     * Decodes priceRules object from array
     *
     * @param array  $data
     * @param string $currency
     *
     * @return Paysera_WalletApi_Entity_PriceRules
     */
    public function decodePriceRules($data, $currency)
    {
        $priceRules = new Paysera_WalletApi_Entity_PriceRules();

        if (isset($data['min'])) {
            $priceRules->setMin(
                Paysera_WalletApi_Entity_Money::create()
                    ->setAmountInCents($data['min'])
                    ->setCurrency($currency)
            );
        }
        if (isset($data['max'])) {
            $priceRules->setMax(
                Paysera_WalletApi_Entity_Money::create()
                    ->setAmountInCents($data['max'])
                    ->setCurrency($currency)
            );
        }
        if (isset($data['choices'])) {
            foreach ($data['choices'] as $choice) {
                $priceRules->addChoice(
                    Paysera_WalletApi_Entity_Money::create()
                        ->setAmountInCents($choice)
                        ->setCurrency($currency)
                );
            }
        }

        return $priceRules;
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
        if (isset($data['type'])) {
            $this->setProperty($account, 'type', $data['type']);
        }
        if (isset($data['user_id'])) {
            $this->setProperty($account, 'userId', $data['user_id']);
        }

        if (isset($data['ibans'])) {
            $this->setProperty($account, 'ibans', $this->decodeIbans($data['ibans']));
        }

        return $account;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function decodeIbans($data)
    {
        $result = array();
        foreach ($data as $key => $item) {
            $result[$key] = $this->decodeIban($item);
        }
        return $result;
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function decodeIban($data)
    {
        if (is_string($data)) {
            $data = trim($data);
        }

        return $data;
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
     * @return Paysera_WalletApi_Entity_Search_Result|Paysera_WalletApi_Entity_Statement[]
     */
    public function decodeStatementSearchResult($data)
    {
        $statements = array();
        foreach ($data['statements'] as $statementData) {
            $statements[] = $this->decodeStatement($statementData);
        }
        $result = new Paysera_WalletApi_Entity_Search_Result($statements);
        $metadata = $data['_metadata'];
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
        $this->setProperty($statement, 'direction', $data['direction']);
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

    /**
     * Decodes statement party object from array
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Statement_Party
     */
    public function decodeStatementParty($data)
    {
        $party = new Paysera_WalletApi_Entity_Statement_Party();
        $this->setProperties($party, $data, array('name', 'code', 'bic'));
        if (isset($data['account_number'])) {
            $this->setProperty($party, 'accountNumber', $data['account_number']);
        }
        if (!empty($data['display_name']) && empty($data['name'])) {
            $this->setProperty($party, 'name', $data['display_name']);
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
        if (isset($data['display_name'])) {
            $this->setProperty($user, 'displayName', $data['display_name']);
        }
        if (isset($data['dob'])) {
            $this->setProperty($user, 'dob', $data['dob']);
        }
        if (isset($data['gender'])) {
            $this->setProperty($user, 'gender', $data['gender']);
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
        if (isset($data['type'])) {
            $this->setProperty($user, 'type', $data['type']);
        }
        if (isset($data['company_code'])) {
            $this->setProperty($user, 'companyCode', $data['company_code']);
        }
        if (isset($data['identification_level'])) {
            $this->setProperty($user, 'identificationLevel', $data['identification_level']);
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
        $mapper = new Paysera_WalletApi_Mapper_IdentityMapper();
        return $mapper->mapToEntity($data);
    }

    /**
     * Decode day working hours
     *
     * @param string $day
     * @param array  $data
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function decodeDayWorkingHours($day, array $data)
    {
        return Paysera_WalletApi_Entity_Location_DayWorkingHours::create()
            ->setOpeningTime($this->decodeTime($data['opening_time']))
            ->setClosingTime($this->decodeTime($data['closing_time']))
            ->setDay($day);
    }

    /**
     * Encode day working hours
     *
     * @param Paysera_WalletApi_Entity_Location_DayWorkingHours $object
     *
     * @return array
     */
    public function encodeDayWorkingHours(Paysera_WalletApi_Entity_Location_DayWorkingHours $object)
    {
        return array(
            $object->getDay() => array(
                'opening_time' => $this->encodeTime($object->getOpeningTime()),
                'closing_time' => $this->encodeTime($object->getClosingTime()),
            ),
        );
    }

    /**
     * Encode location
     *
     * @param Paysera_WalletApi_Entity_Location $object
     *
     * @return array
     */
    public function encodeLocation(Paysera_WalletApi_Entity_Location $object)
    {
        $workingHours = array();
        foreach ($object->getWorkingHours() as $dayWorkingHours) {
            $workingHours = array_merge($workingHours, $this->encodeDayWorkingHours($dayWorkingHours));
        }

        $prices = array();
        foreach ($object->getPrices() as $price) {
            $prices[] = $this->encodeLocationPrice($price);
        }

        $services = $this->encodeLocationServices(
            $object->getServices(),
            $object->getPayCategories(),
            $object->getCashInTypes(),
            $object->getCashOutTypes()
        );
        return array(
            'id'            => $object->getId(),
            'title'         => $object->getTitle(),
            'description'   => $object->getDescription(),
            'address'       => $object->getAddress(),
            'lat'           => $object->getLat(),
            'lng'           => $object->getLng(),
            'radius'        => $object->getRadius(),
            'working_hours' => $workingHours,
            'prices'        => $prices,
            'status'        => $object->getStatus(),
            'services'      => $services,
            'public'        => $object->getPublic(),
        );
    }

    /**
     * Decodes location
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Location
     */
    public function decodeLocation(array $data)
    {
        $location = new Paysera_WalletApi_Entity_Location();

        $location->setId($data['id']);
        $location->setTitle($data['title']);
        if (!empty($data['description'])) {
            $location->setDescription($data['description']);
        }
        $location->setAddress($data['address']);
        $location->setRadius($data['radius']);

        if (!empty($data['lat'])) {
            $location->setLat((float) $data['lat']);
        }

        if (!empty($data['lng'])) {
            $location->setLng((float) $data['lng']);
        }

        if (!empty($data['prices'])) {
            $result = array();
            foreach ($data['prices'] as $price) {
                $result[] = $this->decodeLocationPrice($price);
            }
            $location->setPrices($result);
        }

        if (!empty($data['working_hours'])) {
            foreach ($data['working_hours'] as $day => $dayWorkingHour) {
                $location->addWorkingHours(
                    $this->decodeDayWorkingHours($day, $dayWorkingHour)
                );
            }
        }

        if (!empty($data['images']['pin_open'])) {
            $location->setImagePinOpen($data['images']['pin_open']);
        }
        if (!empty($data['images']['pin_closed'])) {
            $location->setImagePinClosed($data['images']['pin_closed']);
        }

        if (!empty($data['services'])) {
            $services = array();
            foreach ($data['services'] as $key => $val) {
                if (!empty($val['available']) && $val['available'] === true) {
                    $services[] = $key;
                }
            }
            $location->setServices($services);
            if (!empty($data['services'][Paysera_WalletApi_Entity_Location::SERVICE_TYPE_PAY]['categories'])) {
                $location->setPayCategories(
                    $data['services'][Paysera_WalletApi_Entity_Location::SERVICE_TYPE_PAY]['categories']
                );
            }
            if (!empty($data['services'][Paysera_WalletApi_Entity_Location::SERVICE_TYPE_CASH_IN]['types'])) {
                $location->setCashInTypes(
                    $data['services'][Paysera_WalletApi_Entity_Location::SERVICE_TYPE_CASH_IN]['types']
                );
            }
            if (!empty($data['services'][Paysera_WalletApi_Entity_Location::SERVICE_TYPE_CASH_OUT]['types'])) {
                $location->setCashOutTypes(
                    $data['services'][Paysera_WalletApi_Entity_Location::SERVICE_TYPE_CASH_OUT]['types']
                );
            }
        }

        if (!empty($data['status'])) {
            $location->setStatus($data['status']);
        }

        if (isset($data['public'])) {
            $location->setPublic((bool)$data['public']);
        }

        return $location;
    }

    /**
     * @param Paysera_WalletApi_Entity_Location_SearchFilter $filter
     * @return array
     */
    public function encodeLocationFilter(Paysera_WalletApi_Entity_Location_SearchFilter $filter)
    {
        $data = array();
        if ($filter->getLimit() !== null) {
            $data['limit'] = $filter->getLimit();
        }
        if ($filter->getOffset() !== null) {
            $data['offset'] = $filter->getOffset();
        }

        if ($filter->getLat() !== null && $filter->getLng() !== null) {
            $data['lat'] = $filter->getLat();
            $data['lng'] = $filter->getLng();

            if ($filter->getDistance() !== null) {
                $data['distance'] = $filter->getDistance();
            }
        }
        if (count($filter->getStatuses()) > 0) {
            $data['status'] = implode(',', $filter->getStatuses());
        }
        if ($filter->getUpdatedAfter() !== null) {
            $data['updated_after'] = $filter->getUpdatedAfter()->getTimestamp();
        }
        if ($filter->getPayCategory() !== null) {
            $data['pay_category'] = implode(',', $filter->getPayCategory());
        }
        if ($filter->getLocationServices() !== null) {
            $data['service'] = implode(',', $filter->getLocationServices());
        }

        return $data;
    }

    /**
     * @param Paysera_WalletApi_Entity_Client_SearchFilter $filter
     * @return array
     */
    public function encodeClientFilter(Paysera_WalletApi_Entity_Client_SearchFilter $filter)
    {
        $data = array();
        if ($filter->getLimit() !== null) {
            $data['limit'] = $filter->getLimit();
        }
        if ($filter->getOffset() !== null) {
            $data['offset'] = $filter->getOffset();
        }

        if ($filter->getProjectId() !== null) {
            $data['project_id'] = $filter->getProjectId();
        }

        return $data;
    }

    /**
     * @param array $data
     * @return Paysera_WalletApi_Entity_Search_Result|Paysera_WalletApi_Entity_Location[]
     */
    public function decodeLocationSearchResult($data)
    {
        $locations = array();
        foreach ($data['locations'] as $locationData) {
            $locations[] = $this->decodeLocation($locationData);
        }

        $result = new Paysera_WalletApi_Entity_Search_Result($locations);
        $metadata = $data['_metadata'];
        $this->setProperty($result, 'total', $metadata['total']);
        $this->setProperty($result, 'offset', $metadata['offset']);
        $this->setProperty($result, 'limit', $metadata['limit']);
        return $result;
    }

    /**
     * @param array $data
     * @return Paysera_WalletApi_Entity_Search_Result|Paysera_WalletApi_Entity_Client[]
     */
    public function decodeClientSearchResult(array $data)
    {
        $clients = array();
        foreach ($data['clients'] as $clientData) {
            $clients[] = $this->decodeClient($clientData);
        }

        $result = new Paysera_WalletApi_Entity_Search_Result($clients);
        $metadata = $data['_metadata'];
        $this->setProperty($result, 'total', $metadata['total']);
        $this->setProperty($result, 'offset', $metadata['offset']);
        $this->setProperty($result, 'limit', $metadata['limit']);

        return $result;
    }

    /**
     * @param array $data
     * @return Paysera_WalletApi_Entity_PayCategory[]
     */
    public function decodeLocationPayCategories($data)
    {
        $result = array();

        foreach ($data as $payCategoryData) {
            $payCategory = new Paysera_WalletApi_Entity_PayCategory();
            $payCategory->setId($payCategoryData['id']);
            $payCategory->setTitle($payCategoryData['title']);
            if (!empty($payCategoryData['parent_id'])) {
                $payCategory->setParentId($payCategoryData['parent_id']);
            }
            $result[$payCategory->getId()] = $payCategory;
        }

        /** @var Paysera_WalletApi_Entity_PayCategory $cat */
        foreach ($result as $cat) {
            if ($cat->getParentId() !== null) {
                $cat->setParent($result[$cat->getParentId()]);
            }
        }

        return $result;
    }


    /**
     * Encodes time
     *
     * @param Paysera_WalletApi_Entity_Time $object
     *
     * @return string
     */
    public function encodeTime(Paysera_WalletApi_Entity_Time $object)
    {
        return $object->getHours() . ':' . $object->getMinutes();
    }

    /**
     * Decode time
     *
     * @param string $input
     *
     * @return Paysera_WalletApi_Entity_Time
     */
    public function decodeTime($input)
    {
        $time = explode(':', $input);

        return new Paysera_WalletApi_Entity_Time(
            (int) $time[0] !== 24 ? $time[0] : 0,
            $time[1]
        );
    }

    /**
     * Encodes location price
     *
     * @param Paysera_WalletApi_Entity_Location_Price $locationPrice
     *
     * @return array
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function encodeLocationPrice(Paysera_WalletApi_Entity_Location_Price $locationPrice)
    {
        if ($locationPrice->getType() === null) {
            throw new Paysera_WalletApi_Exception_LogicException("Location price must have type property set");
        }

        $result = array(
            'title' => $locationPrice->getTitle(),
            'type' => $locationPrice->getType(),
        );

        if ($locationPrice->isPrice()) {
            $result['price'] = $this->encodeMoney($locationPrice->getPrice());
        }

        return $result;
    }

    /**
     * Decodes location price
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Location_Price
     */
    public function decodeLocationPrice(array $data)
    {
        $locationPrice = new Paysera_WalletApi_Entity_Location_Price();

        $locationPrice->setTitle($data['title']);

        if ($data['type'] == Paysera_WalletApi_Entity_Location_Price::TYPE_PRICE) {
            $locationPrice->setPrice($this->decodeMoney($data['price']));
            $locationPrice->markAsPrice();
        } else {
            $locationPrice->markAsOffer();
        }

        return $locationPrice;
    }

    /**
     * Convert service list to associative array
     *
     * @param array $services
     * @param array $categories
     * @param array $cashInTypes
     * @param array $cashOutTypes
     *
     * @return array
     */
    public function encodeLocationServices(
        array $services,
        array $categories,
        array $cashInTypes,
        array $cashOutTypes
    ) {
        $data = array();
        foreach ($services as $serviceName) {
            $data[$serviceName] = array('available' => true);
        }

        if (isset($data[Paysera_WalletApi_Entity_Location::SERVICE_TYPE_PAY]) && count($categories) > 0) {
            $data[Paysera_WalletApi_Entity_Location::SERVICE_TYPE_PAY]['categories'] = $categories;
        }
        if (isset($data[Paysera_WalletApi_Entity_Location::SERVICE_TYPE_CASH_IN]) && count($cashInTypes) > 0) {
            $data[Paysera_WalletApi_Entity_Location::SERVICE_TYPE_CASH_IN]['types'] = $cashInTypes;
        }
        if (isset($data[Paysera_WalletApi_Entity_Location::SERVICE_TYPE_CASH_OUT]) && count($cashOutTypes) > 0) {
            $data[Paysera_WalletApi_Entity_Location::SERVICE_TYPE_CASH_OUT]['types'] = $cashOutTypes;
        }

        return $data;
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

    /**
     * Encodes client permissions
     *
     * @param Paysera_WalletApi_Entity_ClientPermissions $permissions
     *
     * @return array
     */
    public function encodeClientPermissions(Paysera_WalletApi_Entity_ClientPermissions $permissions)
    {
        return $permissions->getScopes();
    }

    /**
     * Decodes client permissions
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_ClientPermissions
     */
    public function decodeClientPermissions(array $data)
    {
        return Paysera_WalletApi_Entity_ClientPermissions::create()
            ->setScopes($data);
    }

    /**
     * Decodes client
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Client
     */
    public function decodeClient(array $data)
    {
        $client = Paysera_WalletApi_Entity_Client::create()
            ->setId($data['id'])
            ->setType($data['type'])
            ->setPermissions($this->decodeClientPermissions($data['permissions']));

        $this->setProperty($client, 'title', $data['title']);

        if (!empty($data['project'])) {
            $client->setMainProject($this->decodeProject($data['project']));
            $client->setMainProjectId($client->getMainProject()->getId());
        }

        $hosts = array();
        foreach ($data['hosts'] as $host) {
            $hosts[] = $this->decodeHost($host);
        }
        $client->setHosts($hosts);

        if (!empty($data['credentials'])) {
            $client->setCredentials($this->decodeMacCredentials($data['credentials']));
        }

        return $client;
    }

    /**
     * Encodes client
     *
     * @param Paysera_WalletApi_Entity_Client $client
     *
     * @return array
     */
    public function encodeClient(Paysera_WalletApi_Entity_Client $client)
    {
        if ($client->getMainProject() && $client->getMainProject()->getId()) {
            $projectId = $client->getMainProject()->getId();
        } else {
            $projectId = $client->getMainProjectId();
        }

        $result = array(
            'type' => $client->getType(),
        );

        if (count($client->getPermissions()->getScopes()) > 0) {
            $result['permissions'] = $this->encodeClientPermissions($client->getPermissions());

        }
        if (!empty($projectId)) {
            $result['project_id'] = $projectId;
        }

        if (count($client->getHosts()) > 0) {
            $result['hosts'] = array();
            foreach ($client->getHosts() as $host) {
                $result['hosts'][] = $this->encodeHost($host);
            }
        }

        return $result;
    }

    /**
     * Decodes host
     *
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_Client_Host
     */
    public function decodeHost(array $data)
    {
        $host = Paysera_WalletApi_Entity_Client_Host::create()->setHost($data['host']);

        if (!empty($data['path'])) {
            $host->setPath($data['path']);
        }

        if (!empty($data['port'])) {
            $host->setPort($data['port']);
        }

        if (!empty($data['protocol'])) {
            $host->setProtocol($data['protocol']);
        }

        if (!empty($data['any_port'])) {
            $host->markAsAnyPort();
        }

        if (!empty($data['any_subdomain'])) {
            $host->markAsAnySubdomain();
        }

        return $host;
    }

    /**
     * Encodes host
     *
     * @param Paysera_WalletApi_Entity_Client_Host $host
     *
     * @return array
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function encodeHost(Paysera_WalletApi_Entity_Client_Host $host)
    {
        if (!$host->getHost()) {
            throw new Paysera_WalletApi_Exception_LogicException('Host must be provided');
        }

        return array(
            'host'          => $host->getHost(),
            'port'          => $host->getPort(),
            'path'          => $host->getPath(),
            'protocol'      => $host->getProtocol(),
            'any_port'      => $host->isAnyPort(),
            'any_subdomain' => $host->isAnySubdomain(),
        );
    }

    /**
     * @param array $data
     *
     * @return Paysera_WalletApi_Entity_MacCredentials
     */
    public function decodeMacCredentials(array $data)
    {
        return Paysera_WalletApi_Entity_MacCredentials::create()
            ->setMacId($data['access_token'])
            ->setMacKey($data['mac_key'])
            ->setAlgorithm($data['mac_algorithm']);
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

    public function decodeInquiry(array $inquiryData)
    {
        $inquiry = new Paysera_WalletApi_Entity_Inquiry_Inquiry();

        if (isset($inquiryData['identifier'])) {
            $inquiry->setIdentifier($inquiryData['identifier']);
        }
        if (isset($inquiryData['type'])) {
            $inquiry->setType($inquiryData['type']);
        }
        if (isset($inquiryData['description'])) {
            $inquiry->setDescription($inquiryData['description']);
        }
        if (isset($inquiryData['status'])) {
            $inquiry->setStatus($inquiryData['status']);
        }
        if (isset($inquiryData['items'])) {
            foreach ($inquiryData['items'] as $itemData) {
                $inquiry->addInquiryItem($this->decodeInquiryItem($itemData));
            }
        }

        return $inquiry;
    }

    public function decodeInquiryItem(array $itemData)
    {
        $item = new Paysera_WalletApi_Entity_Inquiry_InquiryItem();

        if (isset($itemData['identifier'])) {
            $item->setIdentifier($itemData['identifier']);
        }
        if (isset($itemData['type'])) {
            $item->setType($itemData['type']);
        }
        if (isset($itemData['title'])) {
            $item->setTitle($itemData['title']);
        }

        return $item;
    }

    public function encodeInquiryItem(Paysera_WalletApi_Entity_Inquiry_InquiryItem $item)
    {
        $data = array(
            'identifier' => $item->getIdentifier(),
            'type' => $item->getType(),
            'title' => $item->getTitle(),
        );

        return array_filter($data);
    }

    public function encodeInquiry(Paysera_WalletApi_Entity_Inquiry_Inquiry $inquiry)
    {
        $data = array(
            'identifier' => $inquiry->getIdentifier(),
            'type' => $inquiry->getType(),
            'description' => $inquiry->getDescription(),
        );

        foreach ($inquiry->getInquiryItems() as $item) {
            $data['items'][] = $this->encodeInquiryItem($item);
        }

        return array_filter($data);
    }
}
