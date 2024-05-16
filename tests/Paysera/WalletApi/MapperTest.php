<?php

namespace Paysera\WalletApi;

use DateTime;
use InvalidArgumentException;
use Paysera_WalletApi_Entity_Location_DayWorkingHours;
use Paysera_WalletApi_Entity_Location_Price;
use Paysera_WalletApi_Entity_Spot;
use Paysera_WalletApi_Entity_Allowance;
use Paysera_WalletApi_Entity_FundsSource;
use Paysera_WalletApi_Entity_Limit;
use Exception;
use Paysera_WalletApi_Entity_Client;
use Paysera_WalletApi_Entity_Client_Host;
use Paysera_WalletApi_Entity_ClientPermissions;
use Paysera_WalletApi_Entity_ClientPermissionsToWallet;
use Paysera_WalletApi_Entity_Location;
use Paysera_WalletApi_Entity_Location_SearchFilter;
use Paysera_WalletApi_Entity_MacAccessToken;
use Paysera_WalletApi_Entity_Money;
use Paysera_WalletApi_Entity_Payment;
use Paysera_WalletApi_Entity_Project;
use Paysera_WalletApi_Entity_Restriction_UserRestriction;
use Paysera_WalletApi_Entity_Restrictions;
use Paysera_WalletApi_Entity_Search_Result;
use Paysera_WalletApi_Entity_MacCredentials;
use Paysera_WalletApi_Entity_SpotInfo;
use Paysera_WalletApi_Entity_Time;
use Paysera_WalletApi_Entity_Transaction;
use Paysera_WalletApi_Entity_User_Identity;
use Paysera_WalletApi_Exception_LogicException;
use Paysera_WalletApi_Entity_Wallet;
use Paysera_WalletApi_Entity_Wallet_Account;
use Paysera_WalletApi_Mapper;
use Paysera_WalletApi_Mapper_IdentityMapper;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use Paysera_WalletApi_OAuth_Consumer;

class MapperTest extends PHPUnit_Framework_TestCase
{
    private $mapper;

    protected function setUp()
    {
        parent::setUp();
        $this->mapper = new Paysera_WalletApi_Mapper();
    }

    /**
    * @dataProvider accessTokenDataProvider
    */
    public function testDecodeAccessToken($data, $expectedException, $expectedResult)
    {
        if ($expectedException !== null) {
            $this->setExpectedException($expectedException);
        }

        $result = $this->mapper->decodeAccessToken($data);

        if ($expectedResult !== null) {
            $this->assertInstanceOf(Paysera_WalletApi_Entity_MacAccessToken::class, $result);
            $this->assertEquals($expectedResult->getMacId(), $result->getMacId());
            $this->assertEquals($expectedResult->getMacKey(), $result->getMacKey());
            $this->assertEquals($expectedResult->getRefreshToken(), $result->getRefreshToken());
        }
    }

    public function accessTokenDataProvider()
    {
        return [
            'valid data' => [
                'data' => [
                    'token_type' => 'mac',
                    'mac_algorithm' => 'hmac-sha-256',
                    'expires_in' => 3600,
                    'access_token' => 'test_token',
                    'mac_key' => 'test_key',
                    'refresh_token' => 'test_refresh_token',
                ],
                'expectedException' => null,
                'expectedResult' => Paysera_WalletApi_Entity_MacAccessToken::create()
                    ->setMacId('test_token')
                    ->setMacKey('test_key')
                    ->setRefreshToken('test_refresh_token')
            ],
            'invalid token type' => [
                'data' => [
                    'token_type' => 'invalid',
                    'mac_algorithm' => 'hmac-sha-256',
                    'expires_in' => 3600,
                    'access_token' => 'test_token',
                    'mac_key' => 'test_key',
                    'refresh_token' => 'test_refresh_token',
                ],
                'expectedException' => InvalidArgumentException::class,
                'expectedResult' => null
            ],
            'invalid mac_algorithm' => [
                'data' => [
                    'token_type' => 'mac',
                    'mac_algorithm' => 'invalid',
                    'expires_in' => 3600,
                    'access_token' => 'test_token',
                    'mac_key' => 'test_key',
                    'refresh_token' => 'test_refresh_token',
                ],
                'expectedException' => InvalidArgumentException::class,
                'expectedResult' => null
            ],
        ];
    }

    /**
     * @dataProvider paymentProvider
     */
    public function testEncodePayment($payment, $expectedOutput, $expectedException = null)
    {
        if ($expectedException !== null) {
            $this->setExpectedException($expectedException);
        }

        $result = $this->mapper->encodePayment($payment);

        if ($expectedOutput !== null) {
            $this->assertEquals($expectedOutput, $result);
        }
    }

    public function paymentProvider()
    {
        $payment1 = new Paysera_WalletApi_Entity_Payment();
        $payment1->setDescription("Test payment");
        $payment1->setPrice(new Paysera_WalletApi_Entity_Money(100, 'USD'));
        $payment1->setCashback(new Paysera_WalletApi_Entity_Money(10, 'USD'));

        $payment2 = new Paysera_WalletApi_Entity_Payment();
        $payment2->setDescription("Test payment");
        $payment2->setPrice(new Paysera_WalletApi_Entity_Money(100, 'USD'));

        $payment3 = new Paysera_WalletApi_Entity_Payment();
        $payment3->setDescription("Test payment");
        $payment3->setPrice(new Paysera_WalletApi_Entity_Money(100, 'USD'));
        $payment3->setCashback(new Paysera_WalletApi_Entity_Money(110, 'EUR'));

        $payment4 = new Paysera_WalletApi_Entity_Payment();
        $payment4->setPrice(new Paysera_WalletApi_Entity_Money(100, 'USD'));

        return [
            'valid data' => [
                'payment'=> $payment1,
                'expectedOutput' => [
                    'description' => 'Test payment',
                    'price_decimal' => 100,
                    'currency' => 'USD',
                    'cashback_decimal' => 10,
                ]
            ],
            [
                'payment'=> $payment2,
                'expectedOutput' => [
                    'description' => 'Test payment',
                    'price_decimal' => 100,
                    'currency' => 'USD',
                ]
            ],
            'Price and cashback currency must be the same' => [
                'payment'=> $payment3,
                'expectedOutput' => null,
                'expectedException' => 'Paysera_WalletApi_Exception_LogicException'
            ],
            'Description and price are required if items are not set' => [
                'payment'=> $payment4,
                'expectedOutput' => null,
                'expectedException' => 'Paysera_WalletApi_Exception_LogicException'
            ],
        ];
    }

    public function testEncodeFundsSources()
    {
        $fundsSource1 = new Paysera_WalletApi_Entity_FundsSource();
        $fundsSource1->setType("Credit Card");
        $fundsSource1->setDetails("Test details1");

        $fundsSource2 = new Paysera_WalletApi_Entity_FundsSource();
        $fundsSource2->setType("Bank Transfer");
        $fundsSource2->setDetails("Test details2");

        $fundsSources = [$fundsSource1, $fundsSource2];

        $expected = [
            'funds_sources' => [
                [
                    'type' => 'Credit Card',
                    'details' => 'Test details1',
                ],
                [
                    'type' => 'Bank Transfer',
                    'details' => 'Test details2',
                ],
            ]
        ];

        $result = $this->mapper->encodeFundsSources($fundsSources);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider fundsSourceProvider
     */
    public function testEncodeFundsSource($fundsSource, $expectedOutput, $expectedException = null)
    {
        if ($expectedException !== null) {
            $this->setExpectedException($expectedException);
        }

        $result = $this->mapper->encodeFundsSource($fundsSource);

        if ($expectedOutput !== null) {
            $this->assertEquals($expectedOutput, $result);
        }
    }

    public function fundsSourceProvider()
    {
        $fundsSource1 = new Paysera_WalletApi_Entity_FundsSource();
        $fundsSource1->setType("Credit Card");
        $fundsSource1->setDetails("Test details");

        $fundsSource2 = new Paysera_WalletApi_Entity_FundsSource();

        return [
            [
                'fundsSource' => $fundsSource1,
                'expectedOutput' => [
                    'type' => 'Credit Card',
                    'details' => 'Test details',
                ]
            ],
            [
                'fundsSource' => $fundsSource2,
                'expectedOutput' => null,
                'expectedException' => 'Paysera_WalletApi_Exception_LogicException'
            ],
        ];
    }

    /**
     * @dataProvider fundsSourceDataProvider
     */
    public function testDecodeFundsSource($data, $expectedOutput)
    {
        $result = $this->mapper->decodeFundsSource($data);

        $this->assertEquals($expectedOutput->getType(), $result->getType());
        $this->assertEquals($expectedOutput->getDetails(), $result->getDetails());

    }

    public function fundsSourceDataProvider()
    {
        $fundsSource1 = new Paysera_WalletApi_Entity_FundsSource();
        $fundsSource1->setType("Credit Card");
        $fundsSource1->setDetails("Test details");

        $fundsSource2 = new Paysera_WalletApi_Entity_FundsSource();
        $fundsSource2->setDetails("Test details");

        $fundsSource3 = new Paysera_WalletApi_Entity_FundsSource();
        $fundsSource3->setType("Credit Card");

        return [
            'all data' => [
                'data' => [
                    'type' => 'Credit Card',
                    'details' => 'Test details',
                ],
                'expectedOutput' => $fundsSource1,
            ],
            'empty type' => [
                'data' => [
                    'details' => 'Test details',
                ],
                'expectedOutput' => $fundsSource2,
            ],
            'empty details' => [
                'data' => [
                    'type' => 'Credit Card',
                ],
                'expectedOutput' => $fundsSource3,
            ],
        ];
    }

    /**
     * @dataProvider restrictionsProvider
     */
    public function testEncodeRestrictions($restrictions, $expected)
    {
        $result = $this->mapper->encodeRestrictions($restrictions);

        $this->assertEquals($expected, $result);
    }

    public function restrictionsProvider()
    {
        $userRestriction1 = new Paysera_WalletApi_Entity_Restriction_UserRestriction();
        $userRestriction1->setIdentityRequired(true);
        $userRestriction1->setType('type1');
        $userRestriction1->setLevel('level1');
        $restriction1 = new Paysera_WalletApi_Entity_Restrictions();
        $restriction1->setAccountOwnerRestriction($userRestriction1);

        $userRestriction2 = new Paysera_WalletApi_Entity_Restriction_UserRestriction();
        $userRestriction2->setIdentityRequired(false);
        $userRestriction2->setType('type2');
        $userRestriction2->setLevel('level2');
        $restriction2 = new Paysera_WalletApi_Entity_Restrictions();
        $restriction2->setAccountOwnerRestriction($userRestriction2);

        $restriction3 = new Paysera_WalletApi_Entity_Restrictions();

        return [
            'restriction with identity required' => [
                'restrictions' => $restriction1,
                'expected' => [
                    'account_owner' => [
                        'type' => 'type1',
                        'requirements' => ['identity'],
                        'level' => 'level1',
                    ],
                ],
            ],
            'restriction without identity required' => [
                'restrictions' => $restriction2,
                'expected' => [
                    'account_owner' => [
                        'type' => 'type2',
                        'requirements' => [],
                        'level' => 'level2',
                    ],
                ],
            ],
            'no restriction' => [
                'restrictions' => $restriction3,
                'expected' => [],
            ],
        ];
    }

    /**
     * @dataProvider encodeProjectDataProvider
     */
    public function testEncodeProject($project, $expectedOutput, $expectedException = null)
    {
        if ($expectedException !== null) {
            $this->setExpectedException($expectedException);
        }

        $result = $this->mapper->encodeProject($project);

        if ($expectedOutput !== null) {
            $this->assertEquals($expectedOutput, $result);
        }
    }

    public function encodeProjectDataProvider()
    {
        $project1 = new Paysera_WalletApi_Entity_Project();
        $project1->setId(1);
        $project1->setTitle("Test project");
        $project1->setDescription("Test description");
        $project1->setWalletId(12345);

        $project2 = new Paysera_WalletApi_Entity_Project();
        $project2->setId(2);
        $project2->setDescription("Test description");
        $project2->setWalletId(12345);

        $project3 = new Paysera_WalletApi_Entity_Project();
        $project3->setId(3);
        $project3->setTitle("Test project");
        $project3->setWalletId(12345);

        $project4 = new Paysera_WalletApi_Entity_Project();
        $project4->setId(4);
        $project4->setTitle("Test project");
        $project4->setDescription("Test description");

        return [
            'all data' => [
                'project' => $project1,
                'expectedOutput' => [
                    'title' => 'Test project',
                    'description' => 'Test description',
                    'wallet_id' => 12345,
                ],
                'expectedException' => null
            ],
            'title null' => [
                'project' => $project2,
                'expectedOutput' => null,
                'expectedException' => Paysera_WalletApi_Exception_LogicException::class
            ],
            'no description' => [
                'project' => $project3,
                'expectedOutput' => [
                    'title' => 'Test project',
                    'wallet_id' => 12345,
                ],
                'expectedException' => null
            ],
            'no wallet_id' => [
                'project' => $project4,
                'expectedOutput' => [
                    'title' => 'Test project',
                    'description' => 'Test description',
                ],
                'expectedException' => null
            ],
        ];
    }

    /**
     * @dataProvider decodeProjectDataProvider
     */
    public function testDecodeProject($data, $expectedOutput)
    {
        $result = $this->mapper->decodeProject($data);

        if ($expectedOutput !== null) {
            $this->assertEquals($expectedOutput->getId(), $result->getId());
            $this->assertEquals($expectedOutput->getTitle(), $result->getTitle());
            $this->assertEquals($expectedOutput->getDescription(), $result->getDescription());
            $this->assertEquals($expectedOutput->getWalletId(), $result->getWalletId());
        }
    }

    public function decodeProjectDataProvider()
    {
        $project1 = new Paysera_WalletApi_Entity_Project();
        $project1->setId(1);
        $project1->setTitle("Test project");
        $project1->setDescription("Test description");
        $project1->setWalletId(12345);

        $project2 = new Paysera_WalletApi_Entity_Project();
        $project2->setId(2);
        $project2->setTitle("Test project");
        $project2->setWalletId(12345);

        $project3 = new Paysera_WalletApi_Entity_Project();
        $project3->setId(3);
        $project3->setTitle("Test project");
        $project3->setDescription("Test description");

        return [
            'all data' => [
                'data' => [
                    'id' => 1,
                    'title' => 'Test project',
                    'description' => 'Test description',
                    'wallet_id' => 12345,
                ],
                'expectedOutput' => $project1,
            ],
            'empty description' => [
                'data' => [
                    'id' => 2,
                    'title' => 'Test project',
                    'wallet_id' => 12345,
                ],
                'expectedOutput' => $project2,
            ],
            'empty wallet_id' => [
                'data' => [
                    'id' => 3,
                    'title' => 'Test project',
                    'description' => 'Test description',
                ],
                'expectedOutput' => $project3,
            ],
        ];
    }
    public function testDecodePaymentAllData()
    {
        $data = [
            'id' => '1',
            'transaction_key' => 'key',
            'created_at' => time(),
            'status' => 'status',
            'price_decimal' => '10',
            'currency' => 'USD',
            'commission' => ['amount' => '1.00'],
            'cashback_decimal' => '2',
            'wallet' => 'wallet_id',
            'confirmed_at' => time(),
            'freeze_until' => time(),
            'freeze_for' => 'freeze_reason',
            'description' => 'description',
            'items' => [
                [
                    'title' => 'item1',
                    'price_decimal' => '5.00',
                    'currency' => 'USD',
                    'quantity' => 1,
                ]
            ],
            'beneficiary' => ['email' => 'test@example.com'],
            'parameters' => ['param' => 'value'],
            'password' => ['value' => 'password'],
            'price_rules' => [['amount' => '5.00']],
            'purpose' => 'purpose',
            'funds_source' => [
                'type' => 'type',
            ],
        ];

        $payment = $this->mapper->decodePayment($data);

        $this->assertSame($data['id'], $payment->getId());
        $this->assertSame($data['transaction_key'], $payment->getTransactionKey());
        $this->assertSame($data['status'], $payment->getStatus());
        $this->assertSame($data['price_decimal'], $payment->getPrice()->getAmount());
        $this->assertSame($data['currency'], $payment->getPrice()->getCurrency());
        $this->assertSame($data['cashback_decimal'], $payment->getCashback()->getAmount());
        $this->assertSame($data['currency'], $payment->getCashback()->getCurrency());
        $this->assertSame($data['wallet'], $payment->getWalletId());
        $this->assertSame($data['description'], $payment->getDescription());
        $this->assertSame($data['items'][0]['title'], $payment->getItems()[0]->getTitle());
        $this->assertSame($data['beneficiary']['email'], $payment->getBeneficiary()->getEmail());
        $this->assertSame($data['parameters']['param'], $payment->getParameters()['param']);
        $this->assertSame($data['purpose'], $payment->getPurpose());
        $this->assertSame($data['funds_source']['type'], $payment->getFundsSource()->getType());
    }

    public function testDecodePaymentMandatoryData()
    {
        $minData = [
            'id' => '2',
            'transaction_key' => 'key2',
            'created_at' => time(),
            'status' => 'status2',
            'price_decimal' => '20.00',
            'currency' => 'EUR',
        ];

        $minPayment = $this->mapper->decodePayment($minData);

        $this->assertEquals($minData['id'], $minPayment->getId());
        $this->assertEquals($minData['transaction_key'], $minPayment->getTransactionKey());
        $this->assertEquals($minData['status'], $minPayment->getStatus());
        $this->assertEquals($minData['price_decimal'], $minPayment->getPrice()->getAmount());
        $this->assertEquals($minData['currency'], $minPayment->getPrice()->getCurrency());
    }

    public function testDecodePaymentSearchResult()
    {
        $data = [
            'payments' => [],
            '_metadata' => [
                'total' => 10,
                'offset' => 0,
                'limit' => 5,
            ],
        ];

        $result = $this->mapper->decodePaymentSearchResult($data);

        $this->assertInstanceOf(Paysera_WalletApi_Entity_Search_Result::class, $result);
        $this->assertEquals(10, $result->getTotal());
        $this->assertEquals(0, $result->getOffset());
        $this->assertEquals(5, $result->getLimit());
    }

    /**
     * @dataProvider encodeAllowanceDataProvider
     */
    public function testEncodeAllowance($allowance, $expectedOutput, $expectedException = null, $expectedExceptionMessage = null)
    {
        if ($expectedException !== null) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
        }

        $result = $this->mapper->encodeAllowance($allowance);

        if ($expectedException === null) {
            $this->assertEquals($expectedOutput, $result);
        }
    }

    public function encodeAllowanceDataProvider()
    {
        $allowance1 = new Paysera_WalletApi_Entity_Allowance();
        $allowance1->setDescription("Test allowance");
        $allowance1->setMaxPrice(new Paysera_WalletApi_Entity_Money(100, 'USD'));
        $allowance1->setValidFor(3600);

        $allowance2 = clone $allowance1;
        $this->setProperty($allowance2, 'id', 1);

        $allowance3 = clone $allowance1;
        $limit = new Paysera_WalletApi_Entity_Limit();
        $limit->setMaxPrice(new Paysera_WalletApi_Entity_Money(100, 'EUR'));
        $limit->setTime(3600);
        $allowance3->addLimit($limit);

        $allowance4 = clone $allowance1;
        $allowance4->setValidUntil(new DateTime());

        $allowance5 = clone $allowance1;
        $limit = new Paysera_WalletApi_Entity_Limit();
        $limit->setMaxPrice(new Paysera_WalletApi_Entity_Money(100, 'USD'));
        $allowance5->addLimit($limit);

        return [
            'all valid data' => [
                'allowance' => $allowance1,
                'expectedOutput' => [
                    'description' => 'Test allowance',
                    'max_price' => 10000,
                    'currency' => 'USD',
                    'valid_for' => 3600,
                ],
            ],
            'exception on non null id' => [
                'allowance' => $allowance2,
                'expectedOutput' => null,
                'expectedException' => 'Paysera_WalletApi_Exception_LogicException',
                'expectedExceptionMessage' => 'Cannot create already existing allowance',
            ],
            'exception on different currency on allowance and limits' => [
                'allowance' => $allowance3,
                'expectedOutput' => null,
                'expectedException' => 'Paysera_WalletApi_Exception_LogicException',
                'expectedExceptionMessage' => 'All sums in allowance must have the same currency',
            ],
            'exception on non null validfor and validUntil' => [
                'allowance' => $allowance4,
                'expectedOutput' => null,
                'expectedException' => 'Paysera_WalletApi_Exception_LogicException',
                'expectedExceptionMessage' => 'Only one of validFor and validUntil can be provided',
            ],
            'exception on at least one limit no price or not time' => [
                'allowance' => $allowance5,
                'expectedOutput' => null,
                'expectedException' => 'Paysera_WalletApi_Exception_LogicException',
                'expectedExceptionMessage' => 'At least one limit has no price or no time',
            ],
        ];
    }

    /**
     * @dataProvider decodeAllowanceDataProvider
     */
    public function testDecodeAllowance($data, $expectedOutput)
    {
        $result = $this->mapper->decodeAllowance($data);
        $this->assertEquals($expectedOutput, $result);
    }

    public function decodeAllowanceDataProvider()
    {
        $allowance1 = new Paysera_WalletApi_Entity_Allowance();
        $allowance1->setDescription("Test allowance");
        $allowance1->setMaxPrice(new Paysera_WalletApi_Entity_Money(100, 'USD'));
        $allowance1->setValidFor(3600);
        $this->setProperty($allowance1, 'wallet', 1);
        $this->setProperty($allowance1, 'id', 1);
        $this->setProperty($allowance1, 'transactionKey', 'key');
        $this->setProperty($allowance1, 'createdAt', DateTime::createFromFormat('U', 123));
        $this->setProperty($allowance1, 'status', 'status');

        $allowance2 = new Paysera_WalletApi_Entity_Allowance();
        $allowance2->setDescription("Test allowance");
        $this->setProperty($allowance2, 'id', 1);
        $this->setProperty($allowance2, 'transactionKey', 'key');
        $this->setProperty($allowance2, 'createdAt', DateTime::createFromFormat('U', 123));
        $this->setProperty($allowance2, 'status', 'status');

        return [
            'all data' => [
                'data' => [
                    'description' => 'Test allowance',
                    'max_price' => 10000,
                    'currency' => 'USD',
                    'valid_for' => 3600,
                    'wallet' => 1,
                    'transaction_key' => 'key',
                    'created_at' => 123,
                    'status' => 'status',
                    'id' => 1,
                ],
                'expectedOutput' => $allowance1,
            ],
            'necessary data' => [
                'data' => [
                    'description' => 'Test allowance',
                    'transaction_key' => 'key',
                    'created_at' => 123,
                    'status' => 'status',
                    'id' => 1,
                ],
                'expectedOutput' => $allowance2,
            ],
        ];
    }

    public function testDecodeRestrictions()
    {
        $data = array(
            'account_owner' => array(
                'type' => 'test_type',
                'requirements' => array('identity'),
                'level' => 'test_level',
            ),
        );

        $restrictions = $this->mapper->decodeRestrictions($data);

        $this->assertInstanceOf(Paysera_WalletApi_Entity_Restrictions::class, $restrictions);
        $this->assertEquals('test_type', $restrictions->getAccountOwnerRestriction()->getType());
        $this->assertTrue($restrictions->getAccountOwnerRestriction()->isIdentityRequired());
        $this->assertEquals('test_level', $restrictions->getAccountOwnerRestriction()->getLevel());
    }

    public function testMapperJoinsLocationSearchFilterStatusesArray()
    {
        $filter = new Paysera_WalletApi_Entity_Location_SearchFilter();
        $filter->setStatuses(['a','b']);

        $encoded = $this->mapper->encodeLocationFilter($filter);

        $statuses = explode(',', $encoded['status']);
        $this->assertCount(2, $statuses);
        $this->assertContains('a', $statuses);
        $this->assertContains('b', $statuses);
    }

    public function testIdentityMapperEncoding()
    {
        $identity = new Paysera_WalletApi_Entity_User_Identity();
        $identity
            ->setName('Name')
            ->setSurname("Surname")
            ->setCode(9999999)
            ->setNationality("LT")
        ;

        $mapper = new Paysera_WalletApi_Mapper_IdentityMapper();
        $result = $mapper->mapFromEntity($identity);

        $this->assertSame($identity->getName(), $result['name']);
        $this->assertSame($identity->getSurname(), $result['surname']);
        $this->assertSame($identity->getCode(), $result['code']);
        $this->assertSame($identity->getNationality(), $result['nationality']);
    }

    public function testIdentityMapperDecoding()
    {
        $identity = [
            'name' => 'Name',
            'surname' => 'Surname',
            'code' => 9999999,
            'nationality' => 'LT'
        ];

        $mapper = new Paysera_WalletApi_Mapper_IdentityMapper();
        $result = $mapper->mapToEntity($identity);

        $this->assertSame($identity['name'], $result->getName());
        $this->assertSame($identity['surname'], $result->getSurname());
        $this->assertSame($identity['code'], $result->getCode());
        $this->assertSame($identity['nationality'], $result->getNationality());
    }

    public function testDecodesTransactionWithReserveUntil()
    {
        $until = new DateTime('+1 day');
        $data = [
            'transaction_key' => 'abc',
            'created_at' => (new DateTime('-1 day'))->getTimestamp(),
            'status' => Paysera_WalletApi_Entity_Transaction::STATUS_NEW,
            'reserve' => [
                'until' => $until->getTimestamp(),
            ],
        ];

        $transaction = $this->mapper->decodeTransaction($data);

        $this->assertEquals($until->getTimestamp(), $transaction->getReserveUntil()->getTimestamp());
    }

    public function testDecodesTransactionWithReserveFor()
    {
        $for = 10;
        $data = [
            'transaction_key' => 'abc',
            'created_at' => (new DateTime('-1 day'))->getTimestamp(),
            'status' => Paysera_WalletApi_Entity_Transaction::STATUS_NEW,
            'reserve' => [
                'for' => $for,
            ],
        ];

        $transaction = $this->mapper->decodeTransaction($data);

        $this->assertEquals($for, $transaction->getReserveFor());
    }

    public function testDecodesPep()
    {
        $data = [
            'name' => 'nameValue',
            'relation' => 'relationValue',
            'positions' => [
                'positionAValue',
            ],
        ];

        $pepObj = $this->mapper->decodePep($data);
        self::assertEquals('nameValue', $pepObj->getName());
        self::assertEquals('relationValue', $pepObj->getRelation());
        self::assertEquals('positionAValue', $pepObj->getPositions()[0]);
    }

    /**
     * @dataProvider testDecodeClientDataProvider
     */
    public function testDecodeClient($clientData, $expected)
    {
        self::assertEquals($expected, $this->mapper->decodeClient($clientData));
    }

    public function testDecodeClientDataProvider()
    {
        return [
            'Case 1 Basic empty dataset' => [
                'clientData' => [
                    'id' => 1,
                    'type' => 'private',
                    'permissions' => [],
                    'title' => 'Title_1',
                    'hosts' => [],
                ],
                'expected' => $this->createClient(1),
            ],
            'Case 2 Previous dataset + permissions' => [
                'clientData' => [
                    'id' => 2,
                    'type' => 'private',
                    'permissions' => [
                        'test1',
                        'test2',
                        'test3',
                    ],
                    'title' => 'Title_2',
                    'hosts' => [],
                ],
                'expected' => $this->createClient(2)
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test1',
                                    'test2',
                                    'test3',
                                ]
                            )
                    ),
            ],
            'Case 3 Previous dataset + hosts' => [
                'clientData' => [
                    'id' => 3,
                    'type' => 'private',
                    'permissions' => [
                        'test1',
                        'test2',
                        'test3',
                    ],
                    'title' => 'Title_3',
                    'hosts' => [
                        ['host' => 'test.com'],
                        ['host' => 'www.test.net'],
                    ],
                ],
                'expected' => $this->createClient(3)
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test1',
                                    'test2',
                                    'test3',
                                ]
                            )
                    )
                    ->setHosts(
                        [
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('test.com'),
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('www.test.net'),
                        ]
                    ),
            ],
            'Case 4 Previous dataset + project' => [
                'clientData' => [
                    'id' => 4,
                    'type' => 'private',
                    'permissions' => [
                        'test1',
                        'test2',
                        'test3',
                    ],
                    'title' => 'Title_4',
                    'hosts' => [
                        ['host' => 'test.com'],
                        ['host' => 'www.test.net'],
                    ],
                    'project' => [
                        'id' => 1,
                        'title' => 'Project title 1',
                        'description' => 'Project description 1',
                        'wallet_id' => 1,
                    ],
                ],
                'expected' => $this->createClient(4)
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test1',
                                    'test2',
                                    'test3',
                                ]
                            )
                    )
                    ->setHosts(
                        [
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('test.com'),
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('www.test.net'),
                        ]
                    )
                    ->setMainProject(
                        Paysera_WalletApi_Entity_Project::create()
                            ->setId(1)
                            ->setWalletId(1)
                            ->setTitle('Project title 1')
                            ->setDescription('Project description 1')
                    )
                    ->setMainProjectId(1),
            ],
            'Case 5 Previous dataset + permissions_to_wallets' => [
                'clientData' => [
                    'id' => 4,
                    'type' => 'private',
                    'permissions' => [
                        'test1',
                        'test2',
                        'test3',
                    ],
                    'title' => 'Title_4',
                    'hosts' => [
                        ['host' => 'test.com'],
                        ['host' => 'www.test.net'],
                    ],
                    'project' => [
                        'id' => 1,
                        'title' => 'Project title 1',
                        'description' => 'Project description 1',
                        'wallet_id' => 1,
                    ],
                    'permissions_to_wallets' => [
                        [
                            'wallet' => [
                                'id' => 1,
                                'owner' => 1,
                                'account' => [
                                    'number' => 'EVP1'
                                ],
                            ],
                            'scopes' => [
                                'balance'
                            ]
                        ],
                        [
                            'wallet' => [
                                'id' => 2,
                                'owner' => 2,
                                'account' => [
                                    'number' => 'EVP2'
                                ],
                            ],
                            'scopes' => [
                                'statements'
                            ]
                        ],
                    ],
                ],
                'expected' => $this->createClient(4)
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test1',
                                    'test2',
                                    'test3',
                                ]
                            )
                    )
                    ->setHosts(
                        [
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('test.com'),
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('www.test.net'),
                        ]
                    )
                    ->setMainProject(
                        Paysera_WalletApi_Entity_Project::create()
                            ->setId(1)
                            ->setWalletId(1)
                            ->setTitle('Project title 1')
                            ->setDescription('Project description 1')
                    )
                    ->setMainProjectId(1)
                    ->setPermissionsToWallets(
                        [
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(1, 1, $this->createAccount('EVP1')))
                                ->setScopes(['balance']),
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(2, 2, $this->createAccount('EVP2')))
                                ->setScopes(['statements']),
                        ]
                    ),
            ],
            'Case 6 Previous dataset + credentials' => [
                'clientData' => [
                    'id' => 4,
                    'type' => 'private',
                    'permissions' => [
                        'test1',
                        'test2',
                        'test3',
                    ],
                    'title' => 'Title_4',
                    'hosts' => [
                        ['host' => 'test.com'],
                        ['host' => 'www.test.net'],
                    ],
                    'project' => [
                        'id' => 1,
                        'title' => 'Project title 1',
                        'description' => 'Project description 1',
                        'wallet_id' => 1,
                    ],
                    'permissions_to_wallets' => [
                        [
                            'wallet' => [
                                'id' => 1,
                                'owner' => 1,
                                'account' => [
                                    'number' => 'EVP1'
                                ],
                            ],
                            'scopes' => [
                                'balance'
                            ]
                        ],
                        [
                            'wallet' => [
                                'id' => 2,
                                'owner' => 2,
                                'account' => [
                                    'number' => 'EVP2'
                                ],
                            ],
                            'scopes' => [
                                'statements'
                            ]
                        ],
                    ],
                    'credentials' => [
                        'access_token' => 'token',
                        'mac_key' => 'key',
                        'mac_algorithm' => 'algo',
                    ],
                ],
                'expected' => $this->createClient(4)
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test1',
                                    'test2',
                                    'test3',
                                ]
                            )
                    )
                    ->setHosts(
                        [
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('test.com'),
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('www.test.net'),
                        ]
                    )
                    ->setMainProject(
                        Paysera_WalletApi_Entity_Project::create()
                            ->setId(1)
                            ->setWalletId(1)
                            ->setTitle('Project title 1')
                            ->setDescription('Project description 1')
                    )
                    ->setMainProjectId(1)
                    ->setPermissionsToWallets(
                        [
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(1, 1, $this->createAccount('EVP1')))
                                ->setScopes(['balance']),
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(2, 2, $this->createAccount('EVP2')))
                                ->setScopes(['statements']),
                        ]
                    )
                    ->setCredentials(
                        Paysera_WalletApi_Entity_MacCredentials::create()
                            ->setAlgorithm('algo')
                            ->setMacId('token')
                            ->setMacKey('key')
                    ),
            ],
            'Case 7 Previous dataset + credentials' => [
                'clientData' => [
                    'id' => 4,
                    'type' => 'private',
                    'permissions' => [
                        'test1',
                        'test2',
                        'test3',
                    ],
                    'title' => 'Title_4',
                    'hosts' => [
                        ['host' => 'test.com'],
                        ['host' => 'www.test.net'],
                    ],
                    'project' => [
                        'id' => 1,
                        'title' => 'Project title 1',
                        'description' => 'Project description 1',
                        'wallet_id' => 1,
                    ],
                    'permissions_to_wallets' => [
                        [
                            'wallet' => [
                                'id' => 1,
                                'owner' => 1,
                                'account' => [
                                    'number' => 'EVP1'
                                ],
                            ],
                            'scopes' => [
                                'balance'
                            ]
                        ],
                        [
                            'wallet' => [
                                'id' => 2,
                                'owner' => 2,
                                'account' => [
                                    'number' => 'EVP2'
                                ],
                            ],
                            'scopes' => [
                                'statements'
                            ]
                        ],
                    ],
                    'credentials' => [
                        'access_token' => 'token',
                        'mac_key' => 'key',
                        'mac_algorithm' => 'algo',
                    ],
                    'service_agreement_id' => 1
                ],
                'expected' => $this->createClient(4)
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test1',
                                    'test2',
                                    'test3',
                                ]
                            )
                    )
                    ->setHosts(
                        [
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('test.com'),
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('www.test.net'),
                        ]
                    )
                    ->setMainProject(
                        Paysera_WalletApi_Entity_Project::create()
                            ->setId(1)
                            ->setWalletId(1)
                            ->setTitle('Project title 1')
                            ->setDescription('Project description 1')
                    )
                    ->setMainProjectId(1)
                    ->setPermissionsToWallets(
                        [
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(1, 1, $this->createAccount('EVP1')))
                                ->setScopes(['balance']),
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(2, 2, $this->createAccount('EVP2')))
                                ->setScopes(['statements']),
                        ]
                    )
                    ->setCredentials(
                        Paysera_WalletApi_Entity_MacCredentials::create()
                            ->setAlgorithm('algo')
                            ->setMacId('token')
                            ->setMacKey('key')
                    )
                    ->setServiceAgreementId(1),
            ],
        ];
    }

    /**
     * @dataProvider testEncodeClientDataProvider
     */
    public function testEncodeClient($client, $expected)
    {
        self::assertEquals($expected, $this->mapper->encodeClient($client));
    }

    public function testEncodeClientDataProvider()
    {
        return [
            'Case 1 Empty client' => [
                'client' => Paysera_WalletApi_Entity_Client::create(),
                'expected' => [
                    'type' => null,
                ],
            ],
            'Case 2 Not empty client' => [
                'client' => $this->createClient(1),
                'expected' => [
                    'type' => 'private',
                ],
            ],
            'Case 3 Previous client + projectId' => [
                'client' => $this->createClient(1)
                    ->setMainProjectId(1),
                'expected' => [
                    'type' => 'private',
                    'project_id' => 1
                ],
            ],
            'Case 4 Previous client + project' => [
                'client' => $this->createClient(1)
                    ->setMainProjectId(1)
                    ->setMainProject(
                        Paysera_WalletApi_Entity_Project::create()
                            ->setId(2)
                            ->setWalletId(2)
                            ->setTitle('Project title 2')
                            ->setDescription('Project description 2')
                    ),
                'expected' => [
                    'type' => 'private',
                    'project_id' => 2
                ],
            ],
            'Case 5 Previous client + project' => [
                'client' => $this->createClient(1)
                    ->setMainProjectId(1)
                    ->setMainProject(
                        Paysera_WalletApi_Entity_Project::create()
                            ->setId(2)
                            ->setWalletId(2)
                            ->setTitle('Project title 2')
                            ->setDescription('Project description 2')
                    )
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test_1',
                                    'test_2',
                                ]
                            )
                    ),
                'expected' => [
                    'type' => 'private',
                    'project_id' => 2,
                    'permissions' => [
                        'test_1',
                        'test_2',
                    ]
                ],
            ],
            'Case 6 Previous client + permissions to wallets' => [
                'client' => $this->createClient(1)
                    ->setMainProjectId(1)
                    ->setMainProject(
                        Paysera_WalletApi_Entity_Project::create()
                            ->setId(2)
                            ->setWalletId(2)
                            ->setTitle('Project title 2')
                            ->setDescription('Project description 2')
                    )
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test_1',
                                    'test_2',
                                ]
                            )
                    )
                    ->setPermissionsToWallets(
                        [
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(1, 1, $this->createAccount('EVP1')))
                                ->setScopes(['balance']),
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(2, 2, $this->createAccount('EVP2')))
                                ->setScopes(['statements']),
                        ]
                    ),
                'expected' => [
                    'type' => 'private',
                    'project_id' => 2,
                    'permissions' => [
                        'test_1',
                        'test_2',
                    ],
                    'permissions_to_wallets' => [
                        [
                            'wallet_id' => 1,
                            'account_number' => 'EVP1',
                            'scopes' => [
                                'balance'
                            ],
                        ],
                        [
                            'wallet_id' => 2,
                            'account_number' => 'EVP2',
                            'scopes' => [
                                'statements'
                            ],
                        ]
                    ]
                ],
            ],
            'Case 7 Previous client + hosts' => [
                'client' => $this->createClient(1)
                    ->setMainProjectId(1)
                    ->setMainProject(
                        Paysera_WalletApi_Entity_Project::create()
                            ->setId(2)
                            ->setWalletId(2)
                            ->setTitle('Project title 2')
                            ->setDescription('Project description 2')
                    )
                    ->setPermissions(
                        Paysera_WalletApi_Entity_ClientPermissions::create()
                            ->setScopes(
                                [
                                    'test_1',
                                    'test_2',
                                ]
                            )
                    )
                    ->setPermissionsToWallets(
                        [
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(1, 1, $this->createAccount('EVP1')))
                                ->setScopes(['balance']),
                            (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                                ->setWallet($this->createWallet(2, 2, $this->createAccount('EVP2')))
                                ->setScopes(['statements']),
                        ]
                    )
                    ->setHosts(
                        [
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('test.com'),
                            Paysera_WalletApi_Entity_Client_Host::create()->setHost('www.test.net'),
                        ]
                    ),
                'expected' => [
                    'type' => 'private',
                    'project_id' => 2,
                    'permissions' => [
                        'test_1',
                        'test_2',
                    ],
                    'permissions_to_wallets' => [
                        [
                            'wallet_id' => 1,
                            'account_number' => 'EVP1',
                            'scopes' => [
                                'balance'
                            ],
                        ],
                        [
                            'wallet_id' => 2,
                            'account_number' => 'EVP2',
                            'scopes' => [
                                'statements'
                            ],
                        ]
                    ],
                    'hosts' => [
                        [
                            'host' => 'test.com',
                            'port' => null,
                            'path' => null,
                            'protocol' => null,
                            'any_port' => false,
                            'any_subdomain' => false,
                        ],
                        [
                            'host' => 'www.test.net',
                            'port' => null,
                            'path' => null,
                            'protocol' => null,
                            'any_port' => false,
                            'any_subdomain' => false,
                        ],
                    ]
                ],
            ],
        ];
    }

    /**
     * @param $input
     * @param $expectedPermission
     * @param $isBalanceGranted
     * @param $isStatementsGranted
     * @return void
     * @dataProvider decodeClientPermissionsToWalletDataProvider
     */
    public function testDecodeClientPermissionsToWallet($input, $expectedPermission, $isBalanceGranted, $isStatementsGranted)
    {
        $permission = $this->mapper->decodeClientPermissionsToWallet($input);
        self::assertEquals($expectedPermission, $permission);
        self::assertEquals($isBalanceGranted, $permission->isBalanceGranted());
        self::assertEquals($isStatementsGranted, $permission->isStatementsGranted());
    }

    /**
     * @param $input
     * @param $expected
     * @return void
     * @dataProvider encodeClientPermissionsToWalletDataProvider
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function testEncodeClientPermissionsToWallet($input, $expected)
    {
        if ($expected instanceof Exception) {
            self::setExpectedException(get_class($expected), $expected->getMessage());
        }
        self::assertEquals($expected, $this->mapper->encodeClientPermissionsToWallet($input));
    }

    public function decodeClientPermissionsToWalletDataProvider()
    {
        return [
            'Case 1 No scopes' => [
                'input' => [
                    'wallet' => [
                        'id' => 1,
                        'owner' => null,
                        'account' => [
                            'number' => 'EVP1'
                        ]
                    ],
                    'scopes' => [],
                ],
                'expectedPermission' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(1, $this->getAccount('EVP1')))
                    ->setScopes([]),
                'isBalanceGranted' => false,
                'isStatementsGranted' => false,
            ],
            'Case 2 Balance only' => [
                'input' => [
                    'wallet' => [
                        'id' => 2,
                        'owner' => null,
                        'account' => [
                            'number' => 'EVP2'
                        ]
                    ],
                    'scopes' => [
                        'balance'
                    ],
                ],
                'expectedPermission' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(2, $this->getAccount('EVP2')))
                    ->setScopes([Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE]),
                'isBalanceGranted' => true,
                'isStatementsGranted' => false,
            ],
            'Case 3 Statements only' => [
                'input' => [
                    'wallet' => [
                        'id' => 3,
                        'owner' => null,
                        'account' => [
                            'number' => 'EVP3'
                        ]
                    ],
                    'scopes' => [
                        'statements'
                    ],
                ],
                'expectedPermission' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(3, $this->getAccount('EVP3')))
                    ->setScopes([Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS]),
                'isBalanceGranted' => false,
                'isStatementsGranted' => true,
            ],
            'Case 4 Both balance and statements' => [
                'input' => [
                    'wallet' => [
                        'id' => 4,
                        'owner' => null,
                        'account' => [
                            'number' => 'EVP4'
                        ]
                    ],
                    'scopes' => [
                        'balance',
                        'statements',
                    ],
                ],
                'expectedPermission' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(4, $this->getAccount('EVP4')))
                    ->setScopes([
                        Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE,
                        Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS,
                    ]),
                'isBalanceGranted' => true,
                'isStatementsGranted' => true,
            ],
            'Case 5 Unknown scope' => [
                'input' => [
                    'wallet' => [
                        'id' => 5,
                        'owner' => null,
                        'account' => [
                            'number' => 'EVP5'
                        ]
                    ],
                    'scopes' => [
                        'unknown'
                    ],
                ],
                'expectedPermission' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(5, $this->getAccount('EVP5')))
                    ->setScopes([
                        'unknown'
                    ]),
                'isBalanceGranted' => false,
                'isStatementsGranted' => false,
            ],
        ];
    }

    public function encodeClientPermissionsToWalletDataProvider()
    {
        return [
            'Case 1 Expect empty wallet exception' => [
                'input' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setScopes([]),
                'expected' => new Paysera_WalletApi_Exception_LogicException('Wallet must be provided'),
            ],
            'Case 2 Expect empty wallet ID exception' => [
                'input' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(null, null))
                    ->setScopes([]),
                'expected' => new Paysera_WalletApi_Exception_LogicException('Wallet ID must be provided'),
            ],
            'Case 3 Expect empty account exception' => [
                'input' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(1, null))
                    ->setScopes([]),
                'expected' => new Paysera_WalletApi_Exception_LogicException('Account must be provided'),
            ],
            'Case 4 Expect empty account number exception' => [
                'input' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(1, $this->getAccount(null)))
                    ->setScopes([]),
                'expected' => new Paysera_WalletApi_Exception_LogicException('Account number must be provided'),
            ],
            'Case 3 No scopes' => [
                'input' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(2, $this->getAccount('EVP2')))
                    ->setScopes([]),
                'expected' => [
                    'wallet_id' => 2,
                    'account_number' => 'EVP2',
                    'scopes' => [],
                ],
            ],
            'Case 4 Balance only' => [
                'input' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(3, $this->getAccount('EVP3')))
                    ->setScopes([Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE]),
                'expected' => [
                    'wallet_id' => 3,
                    'account_number' => 'EVP3',
                    'scopes' => [Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE],
                ],
            ],
            'Case 5 Statements only' => [
                'input' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(4, $this->getAccount('EVP4')))
                    ->setScopes([Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS]),
                'expected' => [
                    'wallet_id' => 4,
                    'account_number' => 'EVP4',
                    'scopes' => [Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS],
                ],
            ],
            'Case 6 Both balance and statements' => [
                'input' => (new Paysera_WalletApi_Entity_ClientPermissionsToWallet())
                    ->setWallet($this->getWallet(5, $this->getAccount('EVP5')))
                    ->setScopes([
                        Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE,
                        Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS,
                    ]),
                'expected' => [
                    'wallet_id' => 5,
                    'account_number' => 'EVP5',
                    'scopes' => [
                        Paysera_WalletApi_OAuth_Consumer::SCOPE_BALANCE,
                        Paysera_WalletApi_OAuth_Consumer::SCOPE_STATEMENTS,
                    ],
                ],
            ],
        ];
    }

    public function testDecodeLocation() {
        $data = [
            'id' => 1,
            'title' => 'Test Location',
            'description' => 'This is a test location',
            'address' => 'Test Address',
            'radius' => 100,
            'lat' => 54.6872,
            'lng' => 25.2797,
            'prices' => [
                ['title' => 'a', 'type' => 'price', 'price' => ['amount' => 10000, 'currency' => 'EUR']],
                ['title' => 'b', 'type' => 'price', 'price' => ['amount' => 20000, 'currency' => 'USD']]
            ],
            'working_hours' => [
                'monday' => ['opening_time' => '09:00', 'closing_time' => '17:00'],
                'tuesday' => ['opening_time' => '09:00', 'closing_time' => '17:00']
            ],
            'images' => [
                'pin_open' => 'http://example.com/pin_open.png',
                'pin_closed' => 'http://example.com/pin_closed.png'
            ],
            'services' => [
                'pay' => ['available' => true, 'categories' => ['category1', 'category2']],
                'cash_in' => ['available' => true, 'types' => ['type1', 'type2']],
                'cash_out' => ['available' => true, 'types' => ['type1', 'type2']]
            ],
            'status' => 'active',
            'public' => true,
            'spots' => [
                [
                    'id' => 1,
                    'status' => 'active',
                    'identifier' => 'spot1',
                    'place_info' => [
                        'title' => 'Spot 1',
                        'description' => 'This is spot 1',
                        'address' => 'Spot 1 Address',
                        'logo_uri' => 'http://example.com/spot1.png',
                        'location_id' => 1
                    ]
                ]
            ]
        ];

        /** @var Paysera_WalletApi_Entity_Location $location */
        $location = $this->mapper->decodeLocation($data);

        $this->assertInstanceOf(Paysera_WalletApi_Entity_Location::class, $location);
        $this->assertEquals(1, $location->getId());
        $this->assertEquals('Test Location', $location->getTitle());
        $this->assertEquals('This is a test location', $location->getDescription());
        $this->assertEquals('Test Address', $location->getAddress());
        $this->assertEquals(100, $location->getRadius());
        $this->assertEquals(54.6872, $location->getLat());
        $this->assertEquals(25.2797, $location->getLng());

        $prices = $location->getPrices();
        $this->assertCount(2, $prices);
        $this->assertEquals(100, $prices[0]->getPrice()->getAmount());
        $this->assertEquals('EUR', $prices[0]->getPrice()->getCurrency());
        $this->assertEquals(200, $prices[1]->getPrice()->getAmount());
        $this->assertEquals('USD', $prices[1]->getPrice()->getCurrency());

        $workingHours = $location->getWorkingHours();
        $this->assertCount(2, $workingHours);
        $this->assertEquals('monday', $workingHours[0]->getDay());
        $this->assertEquals('tuesday', $workingHours[1]->getDay());
        $this->assertEquals('09:00', $workingHours[0]->getOpeningTime()->getFormatted());
        $this->assertEquals('17:00', $workingHours[0]->getClosingTime()->getFormatted());
        $this->assertEquals('09:00', $workingHours[1]->getOpeningTime()->getFormatted());
        $this->assertEquals('17:00', $workingHours[1]->getClosingTime()->getFormatted());

        $this->assertEquals('http://example.com/pin_open.png', $location->getImagePinOpen());
        $this->assertEquals('http://example.com/pin_closed.png', $location->getImagePinClosed());

        $services = $location->getServices();
        $this->assertCount(3, $services);
        $this->assertContains('pay', $services);
        $this->assertContains('cash_in', $services);
        $this->assertContains('cash_out', $services);

        $payCategories = $location->getPayCategories();
        $this->assertCount(2, $payCategories);
        $this->assertContains('category1', $payCategories);
        $this->assertContains('category2', $payCategories);

        $cashInTypes = $location->getCashInTypes();
        $this->assertCount(2, $cashInTypes);
        $this->assertContains('type1', $cashInTypes);
        $this->assertContains('type2', $cashInTypes);

        $cashOutTypes = $location->getCashOutTypes();
        $this->assertCount(2, $cashOutTypes);
        $this->assertContains('type1', $cashOutTypes);
        $this->assertContains('type2', $cashOutTypes);

        $this->assertEquals('active', $location->getStatus());
        $this->assertTrue($location->getPublic());

        $spots = $location->getSpots();
        $spot = $spots[0];
        $this->assertCount(1, $spots);
        $this->assertEquals(1, $spot->getId());
        $this->assertEquals('active', $spot->getStatus());
        $this->assertEquals('spot1', $spot->getIdentifier());

        $spotInfo = $spot->getSpotInfo();
        $this->assertEquals('Spot 1', $spotInfo->getTitle());
        $this->assertEquals('This is spot 1', $spotInfo->getDescription());
        $this->assertEquals('Spot 1 Address', $spotInfo->getAddress());
        $this->assertEquals('http://example.com/spot1.png', $spotInfo->getLogoUri());
        $this->assertEquals(1, $spotInfo->getLocationId());
    }

    public function testEncodeLocation() {
        // Create a location entity
        $location = new Paysera_WalletApi_Entity_Location();
        $location->setId(1);
        $location->setTitle('Test Location');
        $location->setDescription('This is a test location');
        $location->setAddress('Test Address');
        $location->setRadius(100);
        $location->setLat(54.6872);
        $location->setLng(25.2797);
    
        // Add prices
        $price1 = new Paysera_WalletApi_Entity_Location_Price();
        $price1->setTitle('a');
        $price1->markAsPrice();
        $price1->setPrice(new Paysera_WalletApi_Entity_Money(10000, 'EUR'));
        $location->addPrice($price1);
    
        $price2 = new Paysera_WalletApi_Entity_Location_Price();
        $price2->setTitle('b');
        $price2->markAsPrice();
        $price2->setPrice(new Paysera_WalletApi_Entity_Money(20000, 'USD'));
        $location->addPrice($price2);
    
        // Add working hours
        $openingTime = new Paysera_WalletApi_Entity_Time('9', '0');
        $closingTime = new Paysera_WalletApi_Entity_Time('17', '0');
        $workingHours1 = new Paysera_WalletApi_Entity_Location_DayWorkingHours();
        $workingHours1->markAsMonday();
        $workingHours1->setOpeningTime($openingTime);
        $workingHours1->setClosingTime($closingTime);
        $location->addWorkingHours($workingHours1);
    
        $workingHours2 = new Paysera_WalletApi_Entity_Location_DayWorkingHours();
        $workingHours2->markAsTuesday();
        $workingHours2->setOpeningTime($openingTime);
        $workingHours2->setClosingTime($closingTime);
        $location->addWorkingHours($workingHours2);
    
        // Set images
        $location->setImagePinOpen('http://example.com/pin_open.png');
        $location->setImagePinClosed('http://example.com/pin_closed.png');
    
        // Add services
        $location->setServices([
            'pay',
            'cash_in',
            'cash_out',
        ]);
    
        // Set status and public
        $location->setStatus('active');
        $location->setPublic(true);
    
        // Add a spot
        $spot = new Paysera_WalletApi_Entity_Spot();
        $spot->setId(1);
        $spot->setStatus('active');
        $spot->setIdentifier('spot1');
    
        // Set spot info
        $spotInfo = new Paysera_WalletApi_Entity_SpotInfo();
        $spotInfo->setTitle('Spot 1');
        $spotInfo->setDescription('This is spot 1');
        $spotInfo->setAddress('Spot 1 Address');
        $spotInfo->setLogoUri('http://example.com/spot1.png');
        $spotInfo->setLocationId(1);
        $spot->setSpotInfo($spotInfo);
    
        $location->setSpots([$spot]);
    
        // Encode the location entity
        $encodedLocation = $this->mapper->encodeLocation($location);
    
        $expectedData = [
            'id' => 1,
            'title' => 'Test Location',
            'description' => 'This is a test location',
            'address' => 'Test Address',
            'radius' => 100,
            'lat' => 54.6872,
            'lng' => 25.2797,
            'prices' => [
                ['title' => 'a', 'type' => 'price', 'price' => ['amount' => 1000000, 'currency' => 'EUR']],
                ['title' => 'b', 'type' => 'price', 'price' => ['amount' => 2000000, 'currency' => 'USD']]
            ],
            'working_hours' => [
                'monday' => ['opening_time' => '9:0', 'closing_time' => '17:0'],
                'tuesday' => ['opening_time' => '9:0', 'closing_time' => '17:0']
            ],
            'services' => [
                'pay' => ['available' => true],
                'cash_in' => ['available' => true],
                'cash_out' => ['available' => true]
            ],
            'status' => 'active',
            'public' => true,
            'spots' => [
                [
                    'id' => 1,
                    'status' => 'active',
                    'identifier' => 'spot1',
                    'place_info' => [
                        'title' => 'Spot 1',
                        'description' => 'This is spot 1',
                        'address' => 'Spot 1 Address',
                        'logo_uri' => 'http://example.com/spot1.png',
                        'location_id' => 1
                    ]
                ]
            ]
        ];
        $this->assertEquals($expectedData, $encodedLocation);
    }

    private function setProperty($object, $property, $value)
    {
        $reflectionProperty = new ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);

        return $this;
    }

    private function createClient($id)
    {
        $client = Paysera_WalletApi_Entity_Client::create()->setId($id)->setType('private');
        $this->setProperty($client, 'title', sprintf('Title_%d', $id));

        return $client;
    }

    private function createWallet($id, $owner, $account)
    {
        $wallet = Paysera_WalletApi_Entity_Wallet::create();

        $this->setProperty($wallet, 'id', $id)
            ->setProperty($wallet, 'owner', $owner)
            ->setProperty($wallet, 'account', $account)
        ;

        return $wallet;
    }

    private function createAccount($number)
    {
        $account = Paysera_WalletApi_Entity_Wallet_Account::create();
        $this->setProperty($account, 'number', $number);

        return $account;
    }

    private function getWallet($id, $account)
    {
        $wallet = Paysera_WalletApi_Entity_Wallet::create();

        $idProperty = new ReflectionProperty(Paysera_WalletApi_Entity_Wallet::class, 'id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($wallet, $id);

        $accountProperty = new ReflectionProperty(Paysera_WalletApi_Entity_Wallet::class, 'account');
        $accountProperty->setAccessible(true);
        $accountProperty->setValue($wallet, $account);

        return $wallet;
    }

    private function getAccount($number)
    {
        $account = Paysera_WalletApi_Entity_Wallet_Account::create();
        $numberProperty = new ReflectionProperty(Paysera_WalletApi_Entity_Wallet_Account::class, 'number');

        $numberProperty->setAccessible(true);
        $numberProperty->setValue($account, $number);

        return $account;
    }
}
