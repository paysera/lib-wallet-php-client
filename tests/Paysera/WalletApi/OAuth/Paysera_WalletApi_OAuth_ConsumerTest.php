<?php

class Paysera_WalletApi_OAuth_ConsumerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Paysera_WalletApi_OAuth_Consumer
     */
    private $consumer;

    protected function setUp()
    {
        parent::setUp();

        $this->consumer = new Paysera_WalletApi_OAuth_Consumer(
            123,
            $this->getSimpleMockForClass(Paysera_WalletApi_Client_OAuthClient::class),
            new Paysera_WalletApi_Util_Router(),
            new Paysera_WalletApi_State_SessionStatePersister('abc'),
            $this->getSimpleMockForClass(Paysera_WalletApi_Util_RequestInfo::class)
        );
    }

    /**
     * @param string $transactionKey
     * @param string $redirectUri
     * @param array $scopes
     * @param array $expected
     *
     * @dataProvider testGetAuthorizationWithTransactionConfirmationUriDataProvider
     */
    public function testGetAuthorizationWithTransactionConfirmationUri(
        $transactionKey,
        $redirectUri,
        $scopes,
        $expected
    ) {
        $parsedUrl = parse_url(
            $this->consumer->getAuthorizationWithTransactionConfirmationUri($transactionKey, $redirectUri, $scopes)
        );
        $queryParams = [];
        parse_str($parsedUrl['query'], $queryParams);

        $this->assertEquals($expected['response_type'], $queryParams['response_type']);
        $this->assertEquals($expected['client_id'], $queryParams['client_id']);
        $this->assertEquals($expected['scope'], $queryParams['scope']);
        $this->assertArrayHasKey('state', $queryParams);

        if ($redirectUri !== null) {
            $this->assertEquals($redirectUri, $queryParams['redirect_uri']);
        }

        $this->assertEquals($parsedUrl['scheme'], 'https');
        $this->assertEquals($parsedUrl['host'], 'www.paysera.com');
        $this->assertEquals($parsedUrl['path'], '/frontend/transaction/confirm-with-oauth/' . $transactionKey);
    }

    public function testGetAuthorizationWithTransactionConfirmationUriDataProvider()
    {
        return [
            [
                'abc123',
                'https://www.paysera.com',
                ['scope'],
                [
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => 'scope',
                    'redirect_uri' => 'https://www.paysera.com',
                ],
            ],
            [
                'abc123',
                'https://www.paysera.com',
                ['scope_1', 'scope_2'],
                [
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => 'scope_1 scope_2',
                ],
            ],
            [
                'abc123',
                'https://www.paysera.com',
                [],
                [
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => '',
                ],
            ],
        ];
    }

    private function getSimpleMockForClass($class)
    {
        $builder = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
        ;

        return $builder->getMock();
    }
}
