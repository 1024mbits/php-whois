<?php

namespace Iodev\Whois;

use FakeSocketLoader;
use Iodev\Whois\Loaders\SocketLoader;

class WhoisTest extends \PHPUnit_Framework_TestCase
{
    /** @var Whois */
    private $whois;

    /** @var ServerProvider */
    private $provider;

    /** @var FakeSocketLoader */
    private $loader;

    /**
     * @return Whois
     */
    private function getWhois()
    {
        $this->provider = new ServerProvider(Server::fromDataList(Config::getServersData()));
        $this->loader = new FakeSocketLoader();
        $this->whois = new Whois($this->provider, $this->loader);
        return $this->whois;
    }

    private function loadTestDataInfo($domain, $filename)
    {
        $w = $this->getWhois();
        $l = $this->loader;
        $l->text = \TestData::loadContent($filename);
        return $w->loadInfo($domain);
    }

    private static function sort($a)
    {
        sort($a);
        return $a;
    }


    public function testConstruct()
    {
        new Whois(new ServerProvider([]), new SocketLoader());
    }

    public function testGetServerProvider()
    {
        $w = $this->getWhois();
        self::assertSame($this->provider, $w->getServerProvider());
    }

    public function testGetLoader()
    {
        $w = $this->getWhois();
        self::assertSame($this->loader, $w->getLoader());
    }
}
