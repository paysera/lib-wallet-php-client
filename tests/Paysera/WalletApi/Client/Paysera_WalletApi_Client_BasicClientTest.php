<?php

class Paysera_WalletApi_Client_BasicClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider test_makeRequest_correctly_handles_makeRequest_return_value_provider
     *
     * @param int $status
     * @param string $content
     *
     * @throws Paysera_WalletApi_Exception_ResponseException
     */
    public function test_makeRequest_correctly_handles_webClient_makeRequest_corrupt_response(
        $status,
        $content
    ) {
        $webClient = $this->getMock('\Paysera_WalletApi_Http_ClientInterface', array('makeRequest'));
        $webClient->expects($this->any())->method('makeRequest')->will($this->returnValue(
            new Paysera_WalletApi_Http_Response($status, array(), $content)
        ));
        $basicClient = new Paysera_WalletApi_Client_BasicClient(
            $webClient,
            $this->getMock('\Paysera_WalletApi_EventDispatcher_EventDispatcher')
        );

        $this->setExpectedException('\Paysera_WalletApi_Exception_ApiException');
        $basicClient->makeRequest(
            new Paysera_WalletApi_Http_Request(
                'http://example.com/',
                \Paysera_WalletApi_Http_Request::METHOD_GET
            )
        );
    }

    public function test_makeRequest_correctly_handles_makeRequest_return_value_provider()
    {
        return array(
            array(401, ''),
            array(401, null),
            array(401, 'str'),

            array(200, ''),
            array(200, null),
        );
    }
}
