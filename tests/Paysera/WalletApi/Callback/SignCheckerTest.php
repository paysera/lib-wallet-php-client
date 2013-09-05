<?php

class Paysera_WalletApi_Callback_SignCheckerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Paysera_WalletApi_Callback_SignChecker
     */
    protected $service;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Paysera_WalletApi_Http_ClientInterface
     */
    protected $webClient;

    public function setUp()
    {
        $this->webClient = $this->getMock('Paysera_WalletApi_Http_ClientInterface');
        $this->service = new Paysera_WalletApi_Callback_SignChecker('http://publickey.abc', $this->webClient);
    }

    /**
     *
     * @param string $data
     * @param string $sign
     * @param string $publicKey
     *
     * @dataProvider validDataProvider
     */
    public function testCheckSign($data, $sign, $publicKey)
    {
        $this->webClient
            ->expects($this->once())
            ->method('makeRequest')
            ->with(new Paysera_WalletApi_Http_Request('http://publickey.abc'))
            ->will($this->returnValue(new Paysera_WalletApi_Http_Response(200, array(), $publicKey)));

        $this->assertTrue($this->service->checkSign($data, $sign));
    }

    /**
     *
     * @param string $data
     * @param string $sign
     * @param string $publicKey
     *
     * @dataProvider invalidDataProvider
     */
    public function testCheckSignWithInvalid($data, $sign, $publicKey)
    {
        $this->webClient
            ->expects($this->once())
            ->method('makeRequest')
            ->with(new Paysera_WalletApi_Http_Request('http://publickey.abc'))
            ->will($this->returnValue(new Paysera_WalletApi_Http_Response(200, array(), $publicKey)));

        $this->assertFalse($this->service->checkSign($data, $sign));
    }

    public function validDataProvider()
    {
        return array(
            array (
                '{"type":"reserved","object":"transaction","data":{"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"reserved","type":"page","wallet":3,"payments":[{"id":1163,"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"reserved","price":501,"currency":"LTL","wallet":3,"freeze_for":24,"description":"Nerastas - timeout"}],"allowance":{"data":{"id":539,"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"active","currency":"LTL","wallet":3,"valid_until":1353325170,"description":"Leidimo paskirtis bla bla","max_price":1000,"limits":[{"max_price":1000,"period":7},{"max_price":3000,"period":144},{"max_price":4000,"period":75720}]},"optional":false},"redirect_uri":"http:\\/\\/sandbox.loc\\/wallet\\/success.php","callback_uri":"http:\\/\\/sandbox.loc\\/wallet\\/callback.php"}}',
                'Jr5C7QD+CD6Cu81RJhWbEcZMMAzk2u313QwtIJ9os5eMFjuJwzhJ23muhUCe0IAK6f1wwYOGfO97K6uP2Cs6ceBfx6NXlPVE3DoTzHkV15urZALg1n+WlegNZbGZq8ymcYqDX+19uIq/hnJJ9w0Uwa2PLM5kpAyDvuv9jVdzdV9qkTjKAQXnnviqRJCLhIbs819HKTeVeXfvzb4nTb7fS4c5+c09Bt+P7rTCD9tNittIcrc1LGOJGQDzkkHigEMhCEx5cjSpUOsmy/pX+c9LQ6mkmomuaIdEUwXs0Vp5tuY1Tftm7egZaDWMXAdE0vbN5c+Vrl9yi4fQvdecrX8dxw==',
                '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq9GIGKEI0A5vu8QKFbG8
3+Gdi6OJg/V5IIy3ZmV0xBrZ8dqkHxkkdr8F+FtFYO1RaAQZikDS8vsftZ4y7de2
Cfvel7d/XOsKvlYLxLVnA3EfacOrOu4CnTncKzySDvYmDfhAdF9TCEgQ/dltcFZI
NtzapAX1/zpgxnlFqAAgqxjkVh1v40pluzpm4zzGJwRgtX0zG4ZtytKABiGPYVKn
AcExVyJ25IDebml8kVp3Unvdf0EZ+iVk2i7DpMXMBi1Rp7Nhjq7jpDa9tKzXpF07
+0gGiu89FbLGDb/zvhmD4D/zhXvLZIlGKvpFKTvncwASVtWY90IarjLRucpT0MgZ
HwIDAQAB
-----END PUBLIC KEY-----
',
            ),
        );
    }

    public function invalidDataProvider()
    {
        return array(
            array (
                ' {"type":"reserved","object":"transaction","data":{"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"reserved","type":"page","wallet":3,"payments":[{"id":1163,"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"reserved","price":501,"currency":"LTL","wallet":3,"freeze_for":24,"description":"Nerastas - timeout"}],"allowance":{"data":{"id":539,"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"active","currency":"LTL","wallet":3,"valid_until":1353325170,"description":"Leidimo paskirtis bla bla","max_price":1000,"limits":[{"max_price":1000,"period":7},{"max_price":3000,"period":144},{"max_price":4000,"period":75720}]},"optional":false},"redirect_uri":"http:\\/\\/sandbox.loc\\/wallet\\/success.php","callback_uri":"http:\\/\\/sandbox.loc\\/wallet\\/callback.php"}}',
                'Jr5C7QD+CD6Cu81RJhWbEcZMMAzk2u313QwtIJ9os5eMFjuJwzhJ23muhUCe0IAK6f1wwYOGfO97K6uP2Cs6ceBfx6NXlPVE3DoTzHkV15urZALg1n+WlegNZbGZq8ymcYqDX+19uIq/hnJJ9w0Uwa2PLM5kpAyDvuv9jVdzdV9qkTjKAQXnnviqRJCLhIbs819HKTeVeXfvzb4nTb7fS4c5+c09Bt+P7rTCD9tNittIcrc1LGOJGQDzkkHigEMhCEx5cjSpUOsmy/pX+c9LQ6mkmomuaIdEUwXs0Vp5tuY1Tftm7egZaDWMXAdE0vbN5c+Vrl9yi4fQvdecrX8dxw==',
                '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq9GIGKEI0A5vu8QKFbG8
3+Gdi6OJg/V5IIy3ZmV0xBrZ8dqkHxkkdr8F+FtFYO1RaAQZikDS8vsftZ4y7de2
Cfvel7d/XOsKvlYLxLVnA3EfacOrOu4CnTncKzySDvYmDfhAdF9TCEgQ/dltcFZI
NtzapAX1/zpgxnlFqAAgqxjkVh1v40pluzpm4zzGJwRgtX0zG4ZtytKABiGPYVKn
AcExVyJ25IDebml8kVp3Unvdf0EZ+iVk2i7DpMXMBi1Rp7Nhjq7jpDa9tKzXpF07
+0gGiu89FbLGDb/zvhmD4D/zhXvLZIlGKvpFKTvncwASVtWY90IarjLRucpT0MgZ
HwIDAQAB
-----END PUBLIC KEY-----
',
            ),
            array (
                '{"type":"reserved","object":"transaction","data":{"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"reserved","type":"page","wallet":3,"payments":[{"id":1163,"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"reserved","price":501,"currency":"LTL","wallet":3,"freeze_for":24,"description":"Nerastas - timeout"}],"allowance":{"data":{"id":539,"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"active","currency":"LTL","wallet":3,"valid_until":1353325170,"description":"Leidimo paskirtis bla bla","max_price":1000,"limits":[{"max_price":1000,"period":7},{"max_price":3000,"period":144},{"max_price":4000,"period":75720}]},"optional":false},"redirect_uri":"http:\\/\\/sandbox.loc\\/wallet\\/success.php","callback_uri":"http:\\/\\/sandbox.loc\\/wallet\\/callback.php"}}',
                'jr5C7QD+CD6Cu81RJhWbEcZMMAzk2u313QwtIJ9os5eMFjuJwzhJ23muhUCe0IAK6f1wwYOGfO97K6uP2Cs6ceBfx6NXlPVE3DoTzHkV15urZALg1n+WlegNZbGZq8ymcYqDX+19uIq/hnJJ9w0Uwa2PLM5kpAyDvuv9jVdzdV9qkTjKAQXnnviqRJCLhIbs819HKTeVeXfvzb4nTb7fS4c5+c09Bt+P7rTCD9tNittIcrc1LGOJGQDzkkHigEMhCEx5cjSpUOsmy/pX+c9LQ6mkmomuaIdEUwXs0Vp5tuY1Tftm7egZaDWMXAdE0vbN5c+Vrl9yi4fQvdecrX8dxw==',
                '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq9GIGKEI0A5vu8QKFbG8
3+Gdi6OJg/V5IIy3ZmV0xBrZ8dqkHxkkdr8F+FtFYO1RaAQZikDS8vsftZ4y7de2
Cfvel7d/XOsKvlYLxLVnA3EfacOrOu4CnTncKzySDvYmDfhAdF9TCEgQ/dltcFZI
NtzapAX1/zpgxnlFqAAgqxjkVh1v40pluzpm4zzGJwRgtX0zG4ZtytKABiGPYVKn
AcExVyJ25IDebml8kVp3Unvdf0EZ+iVk2i7DpMXMBi1Rp7Nhjq7jpDa9tKzXpF07
+0gGiu89FbLGDb/zvhmD4D/zhXvLZIlGKvpFKTvncwASVtWY90IarjLRucpT0MgZ
HwIDAQAB
-----END PUBLIC KEY-----
',
            ),
            array (
                '{"type":"reserved","object":"transaction","data":{"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"reserved","type":"page","wallet":3,"payments":[{"id":1163,"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"reserved","price":501,"currency":"LTL","wallet":3,"freeze_for":24,"description":"Nerastas - timeout"}],"allowance":{"data":{"id":539,"transaction_key":"9fPQMV4oZOPHwGN04oQQDT5sDpNIknet","created_at":1352881293,"status":"active","currency":"LTL","wallet":3,"valid_until":1353325170,"description":"Leidimo paskirtis bla bla","max_price":1000,"limits":[{"max_price":1000,"period":7},{"max_price":3000,"period":144},{"max_price":4000,"period":75720}]},"optional":false},"redirect_uri":"http:\\/\\/sandbox.loc\\/wallet\\/success.php","callback_uri":"http:\\/\\/sandbox.loc\\/wallet\\/callback.php"}}',
                'Jr5C7QD+CD6Cu81RJhWbEcZMMAzk2u313QwtIJ9os5eMFjuJwzhJ23muhUCe0IAK6f1wwYOGfO97K6uP2Cs6ceBfx6NXlPVE3DoTzHkV15urZALg1n+WlegNZbGZq8ymcYqDX+19uIq/hnJJ9w0Uwa2PLM5kpAyDvuv9jVdzdV9qkTjKAQXnnviqRJCLhIbs819HKTeVeXfvzb4nTb7fS4c5+c09Bt+P7rTCD9tNittIcrc1LGOJGQDzkkHigEMhCEx5cjSpUOsmy/pX+c9LQ6mkmomuaIdEUwXs0Vp5tuY1Tftm7egZaDWMXAdE0vbN5c+Vrl9yi4fQvdecrX8dxw==',
                '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq9GIGKEI0A5vu8QKFbG8
3+Gdi6OJg/V5IIy3ZmV0xBrZ8dqkHxkkdr8F+FtFYO1RaAQZikDS8vsftZ4y7de2
Cfvel7d/XOsKvlYLxLVnA3EfacOrOu4CnTncKzySDvYmDfhAdF9TCEgQ/dltcFZI
NtzapAX1/zpgxnlFqAAgqxjkVh1v40pluzpm4zzGJwRgtX0zG4ZtytKABiGPYVKn
AcExVyJ25IDebml8kVp3Unvdf0EZ+iVk2i7DpMXMBi1Rp7Nhjq7jpDa9tKzXpF07
+0gGiu89FbLGDb/zvhmD4D/zhXvLZIlGKvpFKTvncwASVtWY90IarjLRucpT0Mgz
HwIDAQAB
-----END PUBLIC KEY-----
',
            ),
        );
    }
}