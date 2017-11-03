<?php

namespace Iodev\Whois;

use Tools\FakeSocketLoader;

class WhoisTest extends \PHPUnit_Framework_TestCase
{
    /** @var Whois */
    private $whois;

    /** @var ServerProvider */
    private $provider;

    /** @var FakeSocketLoader */
    private $loader;

    private function getWhois()
    {
        $this->provider = new ServerProvider([]);
        $this->loader = new FakeSocketLoader();
        $this->whois = new Whois($this->provider, $this->loader);
        return $this->whois;
    }


    public function testConstruct()
    {
        self::assertInstanceOf(Whois::class, self::getWhois());
    }
}
