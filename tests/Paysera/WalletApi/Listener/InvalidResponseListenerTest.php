<?php

class Paysera_WalletApi_Listener_InvalidResponseListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Paysera_WalletApi_Http_ClientInterface
     */
    protected $webClient;

    /**
     * @var Paysera_WalletApi_Client_BasicClient
     */
    protected $service;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->webClient = $this->getMock('Paysera_WalletApi_Http_ClientInterface');

        $dispatcher = new Paysera_WalletApi_EventDispatcher_EventDispatcher();
        $dispatcher->addSubscriber(new Paysera_WalletApi_Listener_InvalidResponseListener());

        $this->service = new Paysera_WalletApi_Client_BasicClient(
            $this->webClient,
            $dispatcher
        );
    }

    /**
     * @dataProvider dataProviderForTestInvalidResponseFallback
     */
    public function testSingleInvalidResponseFallback($uri, $json)
    {
        $this->webClient
            ->expects($this->at(0))
            ->method('makeRequest')
            ->with(new Paysera_WalletApi_Http_Request($uri))
            ->will($this->returnValue(new Paysera_WalletApi_Http_Response(
                502,
                array(),
                '<html>
                    <head><title>502 Bad Gateway</title></head>
                    <body bgcolor="white">
                    <center><h1>502 Bad Gateway</h1></center>
                    <hr><center>nginx/1.0.15</center>
                    </body>
                </html>
                '
            )));

        $this->webClient
            ->expects($this->at(1))
            ->method('makeRequest')
            ->with(new Paysera_WalletApi_Http_Request($uri))
            ->will($this->returnValue(new Paysera_WalletApi_Http_Response(
                200,
                array(),
                $json
            )));

        $this->assertSame(
            $this->service->makeRequest(new Paysera_WalletApi_Http_Request($uri)),
            $json
        );
    }


    public function testDoubleInvalidResponse()
    {
        $this->webClient
            ->expects($this->exactly(2))
            ->method('makeRequest')
            ->with(new Paysera_WalletApi_Http_Request(''))
            ->will($this->returnValue(new Paysera_WalletApi_Http_Response(
                502,
                array(),
                '<html>
                    <head><title>502 Bad Gateway</title></head>
                    <body bgcolor="white">
                    <center><h1>502 Bad Gateway</h1></center>
                    <hr><center>nginx/1.0.15</center>
                    </body>
                </html>
                '
            )));

        $this->setExpectedException('Paysera_WalletApi_Exception_ResponseException');
        $this->service->makeRequest(new Paysera_WalletApi_Http_Request(''));
    }

    public function dataProviderForTestInvalidResponseFallback()
    {
        $data = array(
            'id' => 123,
            'email' => 'user@domain.com',
            'display_name' => 'Username',
        );

        return array(
            array('user/me', json_encode($data))
        );
    }
}