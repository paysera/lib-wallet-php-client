<?php

class Paysera_WalletApi_OAuth_ConsumerTest extends PHPUnit_Framework_TestCase
{
    const CURRENT_URI = 'https://current.bank.paysera.com';
    const REDIRECT_URI = 'https://redirect.com';
    const CANCEL_URI = 'https://cancel.com';
    const TRANSFER_ID = 'TEST123';

    /**
     * @var Paysera_WalletApi_OAuth_Consumer
     */
    private $consumer;

    protected function setUp()
    {
        parent::setUp();

        $requestInfo = $this->getSimpleMockForClass('Paysera_WalletApi_Util_RequestInfo');
        $requestInfo
            ->method('getCurrentUri')
            ->willReturn(self::CURRENT_URI)
        ;

        $this->consumer = new Paysera_WalletApi_OAuth_Consumer(
            123,
            $this->getSimpleMockForClass('Paysera_WalletApi_Client_OAuthClient'),
            new Paysera_WalletApi_Util_Router(),
            new Paysera_WalletApi_State_SessionStatePersister('abc'),
            $requestInfo
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
        $queryParams = array();
        parse_str($parsedUrl['query'], $queryParams);

        $this->assertEquals($expected['response_type'], $queryParams['response_type']);
        $this->assertEquals($expected['client_id'], $queryParams['client_id']);
        $this->assertEquals($expected['scope'], $queryParams['scope']);
        $this->assertArrayHasKey('state', $queryParams);

        if ($redirectUri !== null) {
            $this->assertEquals($redirectUri, $queryParams['redirect_uri']);
        }

        $this->assertEquals($parsedUrl['scheme'], 'https');
        $this->assertEquals($parsedUrl['host'], 'bank.paysera.com');
        $this->assertEquals($parsedUrl['path'], '/frontend/transaction/confirm-with-oauth/' . $transactionKey);
    }

    public function testGetAuthorizationWithTransactionConfirmationUriDataProvider()
    {
        return array(
            array(
                'abc123',
                'https://bank.paysera.com',
                array('scope'),
                array(
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => 'scope',
                    'redirect_uri' => 'https://bank.paysera.com',
                ),
            ),
            array(
                'abc123',
                'https://bank.paysera.com',
                array('scope_1', 'scope_2'),
                array(
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => 'scope_1 scope_2',
                ),
            ),
            array(
                'abc123',
                'https://bank.paysera.com',
                array(),
                array(
                    'response_type' => 'code',
                    'client_id' => '123',
                    'scope' => '',
                ),
            ),
        );
    }

    /**
     * @param string $transferId
     * @param string $redirectUri
     * @param string $cancelUri
     * @param array $expected

     * @dataProvider testGetTransferSignRedirectUrlDataProvider
     */
    public function testGetTransferSignRedirectUrl($transferId, $redirectUri, $cancelUri, $expected)
    {
        $parsedUrl = parse_url(
            $this->consumer->getTransferSignRedirectUri($transferId, $redirectUri, $cancelUri)
        );

        $queryParams = array();
        parse_str($parsedUrl['query'], $queryParams);

        $this->assertEquals($expected['redirect_uri'], $queryParams['redirect_uri']);

        if ($cancelUri !== null) {
            $this->assertEquals($expected['cancel_uri'], $queryParams['cancel_uri']);
        }
    }

    public function testGetTransferSignRedirectUrlDataProvider()
    {
        return [
            'No redirect url' => [
                self::TRANSFER_ID,
                null,
                null,
                [
                    'redirect_uri' => self::CURRENT_URI
                ]
            ],
            'With redirect url' => [
                self::TRANSFER_ID,
                self::REDIRECT_URI,
                null,
                [
                    'redirect_uri' => self::REDIRECT_URI
                ]
            ],
            'With cancel url' => [
                self::TRANSFER_ID,
                self::REDIRECT_URI,
                self::CANCEL_URI,
                [
                    'redirect_uri' => self::REDIRECT_URI,
                    'cancel_uri' => self::CANCEL_URI
                ]
            ]
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
