<?php

class Paysera_WalletApi_Auth_MacTest extends PHPUnit_Framework_TestCase
{
    protected $service;

    /** @var Paysera_WalletApi_Auth_Mac|PHPUnit_Framework_MockObject_MockObject */
    protected $mock;

    public function setUp()
    {
        $this->service = new Paysera_WalletApi_Auth_Mac('wkVd93h2uS', 'IrdTc8uQodU7PRpLzzLTW6wqZAO6tAMU');

        $this->mock = $this->getMock(
            'Paysera_WalletApi_Auth_Mac',
            array('getTimestamp', 'generateNonce'),
            array('wkVd93h2uS', 'IrdTc8uQodU7PRpLzzLTW6wqZAO6tAMU')
        );
        $this->mock->expects($this->any())->method('getTimestamp')->will($this->returnValue('1343818800'));
        $this->mock->expects($this->any())->method('generateNonce')->will($this->returnValue('nQnNaSNyubfPErjRO55yaaEYo9YZfKHN'));
    }

    /**
     *
     * @param string $uri
     * @param string $method
     * @param string $content
     * @param string $mac
     * @param string $ext
     *
     * @dataProvider authProvider
     */
    public function testSignRequest($uri, $method, $content, $mac, $ext)
    {
        $request = new Paysera_WalletApi_Http_Request(
            'https://wallet.paysera.com' . $uri,
            $method,
            $content
        );
        $this->mock->signRequest($request);

        $authHeader = $request->getHeaderBag()->getHeader('Authorization');

        $this->assertSame(
            'MAC id="wkVd93h2uS", ts="1343818800", nonce="nQnNaSNyubfPErjRO55yaaEYo9YZfKHN", mac="'
                . $mac . '"' . ($ext === '' ? '' : ', ext="' . $ext . '"'),
            $authHeader
        );
    }

    public function authProvider()
    {
        return array(
            array(
                '/wallet/rest/v1/payment/10145',
                'GET',
                null,
                'd1OlqI77u2P1IVYIv2ppL3hnrBhyaQ+gDqYMxCH+0e0=',
                '',
            ),
            array(
                '/wallet/oauth/v1/',
                'POST',
                'grant_type=authorization_code&code=SplxlOBeZQQYbYS6WxSbIA&redirect_uri=http%3A%2F%2Flocalhost%2Fabc',
                'LAdTwsIVsaz7pKy9fO713d/kzBFKyhYjFqsugUtFj+k=',
                '21fb73c40b589622d0c78e9cd8900f89d9472aa724d0e5c3eca9ac1cd9d2a6d5',
            ),
        );
    }
}