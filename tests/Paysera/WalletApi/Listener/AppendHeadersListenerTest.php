<?php

class Paysera_WalletApi_Listener_AppendHeadersListenerTest extends PHPUnit_Framework_TestCase
{
    public function testHeadersIsDefined()
    {
        $eventDispatcher = $this->getEventDispatcher(
            array(
                'headers' => array(
                    'Header-Name' => 'value',
                    'Header-Another-Name' => 'value',
                )
            )
        );

        $request = new Paysera_WalletApi_Http_Request(
            'https://test.dev/rest/v1/wallet/me/balance'
        );

        $event = new Paysera_WalletApi_Event_RequestEvent($request, array());
        $eventDispatcher->dispatch(Paysera_WalletApi_Events::BEFORE_REQUEST, $event);

        $this->assertEquals(
            'value',
            $request->getHeaderBag()->getHeader('Header-Name')
        );

        $this->assertEquals(
            'value',
            $request->getHeaderBag()->getHeader('Header-Another-Name')
        );
    }

    public function testHeadersIsNotDefined()
    {
        $eventDispatcher = $this->getEventDispatcher(array('random' => 'rand'));

        $request = new Paysera_WalletApi_Http_Request(
            'https://test.dev/rest/v1/wallet/me/balance'
        );

        $event = new Paysera_WalletApi_Event_RequestEvent($request, array());
        $eventDispatcher->dispatch(Paysera_WalletApi_Events::BEFORE_REQUEST, $event);

        $this->assertNull($request->getHeaderBag()->getHeader('random'));
    }

    private function getEventDispatcher(array $parameters = array())
    {
        $requestSigner = new Paysera_WalletApi_Listener_RequestSigner(
            new Paysera_WalletApi_Auth_Mac('123', '555')
        );

        $dispatcher = new Paysera_WalletApi_Container();
        $eventDispatcher = $dispatcher->createDispatcherForClient(
            'https://test.dev/',
            $requestSigner,
            $parameters
        );

        return $eventDispatcher;
    }
}
