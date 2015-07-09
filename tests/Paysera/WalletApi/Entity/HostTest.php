<?php


class Paysera_WalletApi_Entity_HostTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider buildRegexpDataProvider
     * @param integer $expected
     * @param string $uri
     * @param string $hostname
     * @param string $path
     * @param integer $port
     * @param string $protocol
     * @param boolean $anyPort
     * @param boolean $anySubdomain
     */
    public function testBuildRegexp($expected, $uri, $hostname, $path, $port, $protocol, $anyPort, $anySubdomain)
    {
        $host = new Paysera_WalletApi_Entity_Client_Host();
        $host->setHost($hostname);
        $host->setPath($path);
        $host->setPort($port);
        $host->setProtocol($protocol);
        if ($anyPort) {
            $host->markAsAnyPort();
        }
        if ($anySubdomain) {
            $host->markAsAnySubdomain();
        }
        $this->assertSame($expected, preg_match($host->buildRegexp(), $uri));
    }

    public function buildRegexpDataProvider()
    {
        return array(
            array(
                1,
                'https://www.example.com/path/abc?hello',
                'example.com',
                '/path',
                null,
                'https',
                true,
                true
            ),
            array(
                0,
                'https://www.example.com/path-other/abc?hello',
                'example.com',
                '/path',
                null,
                'https',
                true,
                true
            ),
            array(
                1,
                'https://www.example.com/path',
                'example.com',
                '/path',
                null,
                'https',
                true,
                true
            ),
            array(
                1,
                'mobile.protocol://',
                null,
                null,
                null,
                'mobile.protocol',
                true,
                false
            ),
            array(
                1,
                'mobile.protocol://path/abc',
                null,
                '/path',
                null,
                'mobile.protocol',
                true,
                false
            ),
            array(
                0,
                'mobile.protocol://other-path',
                null,
                '/path',
                null,
                'mobile.protocol',
                true,
                false
            ),
            array(
                0,
                'https://www.example.com/path-other/abc?hello',
                'example.com',
                '/path',
                null,
                'https',
                true,
                true
            ),
            array(
                1,
                'https://www.example.com/path',
                'www.example.com',
                '/path',
                null,
                'https',
                false,
                false
            ),
            array(
                0,
                'https://www.example.com:1010/path',
                'www.example.com',
                '/path',
                null,
                'https',
                false,
                false
            ),
            array(
                0,
                'https://a.www.example.com/path',
                'www.example.com',
                '/path',
                null,
                'https',
                false,
                false
            ),
        );
    }
}
