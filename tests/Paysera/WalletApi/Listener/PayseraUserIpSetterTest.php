<?php

class Paysera_WalletApi_Listener_ActAsUserSetterTest extends PHPUnit_Framework_TestCase
{
    public function testPayseraUserIpIsDefined()
    {
        $eventDispatcher = new Paysera_WalletApi_EventDispatcher_EventDispatcher();
        $eventDispatcher->addSubscriber(
            new Paysera_WalletApi_Listener_PayseraUserIpSetter(array('Paysera-User-Ip' => '127.0.0.1'))
        );

        $request = new Paysera_WalletApi_Http_Request(
            'https://test.dev/rest/v1/wallet/me/balance'
        );

        $event = new Paysera_WalletApi_Event_RequestEvent($request, array());
        $eventDispatcher->dispatch(Paysera_WalletApi_Events::BEFORE_REQUEST, $event);

        $this->assertNotNull($request->getHeaderBag()->getHeader('Paysera-User-Ip'));

        $this->assertEquals(
            '127.0.0.1',
            $request->getHeaderBag()->getHeader('Paysera-User-Ip')
        );
    }

    public function testPayseraUserIpIsNotDefined()
    {
        $eventDispatcher = new Paysera_WalletApi_EventDispatcher_EventDispatcher();
        $eventDispatcher->addSubscriber(
            new Paysera_WalletApi_Listener_PayseraUserIpSetter($parameters = array('random' => 'rand'))
        );

        $request = new Paysera_WalletApi_Http_Request(
            'https://test.dev/rest/v1/wallet/me/balance'
        );

        $event = new Paysera_WalletApi_Event_RequestEvent($request, array());
        $eventDispatcher->dispatch(Paysera_WalletApi_Events::BEFORE_REQUEST, $event);

        $this->assertNull($request->getHeaderBag()->getHeader('Paysera-User-Ip'));
        $this->assertNull($request->getHeaderBag()->getHeader('random'));
    }
}
