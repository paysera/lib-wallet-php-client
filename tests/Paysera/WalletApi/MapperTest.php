<?php

namespace Paysera\WalletApi;

use DateTime;
use Exception;
use Paysera_WalletApi_Entity_Client;
use Paysera_WalletApi_Entity_Client_Host;
use Paysera_WalletApi_Entity_ClientPermissions;
use Paysera_WalletApi_Entity_ClientPermissionsToWallet;
use Paysera_WalletApi_Entity_Location_SearchFilter;
use Paysera_WalletApi_Entity_MacCredentials;
use Paysera_WalletApi_Entity_Project;
use Paysera_WalletApi_Entity_Transaction;
use Paysera_WalletApi_Entity_User_Identity;
use Paysera_WalletApi_Entity_Wallet;
use Paysera_WalletApi_Entity_Wallet_Account;
use Paysera_WalletApi_Exception_LogicException;
use Paysera_WalletApi_Mapper;
use Paysera_WalletApi_Mapper_IdentityMapper;
use Paysera_WalletApi_OAuth_Consumer;
use ReflectionProperty;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    private $mapper;

    public function setUp()
    {
        parent::setUp();
        $this->mapper = new Paysera_WalletApi_Mapper();
    }

    public function testMapperJoinsLocationSearchFilterStatusesArray()
    {
        $filter = new Paysera_WalletApi_Entity_Location_SearchFilter();
        $filter->setStatuses(['a','b']);

        $mapper = new Paysera_WalletApi_Mapper();
        $encoded = $mapper->encodeLocationFilter($filter);

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

        $mapper = new Paysera_WalletApi_Mapper();
        $transaction = $mapper->decodeTransaction($data);

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

        $mapper = new Paysera_WalletApi_Mapper();
        $transaction = $mapper->decodeTransaction($data);

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

        $mapper = new Paysera_WalletApi_Mapper();
        $pepObj = $mapper->decodePep($data);
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
