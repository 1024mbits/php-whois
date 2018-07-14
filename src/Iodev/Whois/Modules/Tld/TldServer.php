<?php

namespace Iodev\Whois\Modules\Tld;

use InvalidArgumentException;
use Iodev\Whois\Helpers\DomainHelper;

/**
 * Immutable data object
 */
class TldServer
{
    /**
     * @param array $data
     * @param TldParser $defaultParser
     * @return TldServer
     */
    public static function fromData($data, TldParser $defaultParser = null)
    {
        /* @var $parser TldParser */
        $parser = $defaultParser;
        if (isset($data['parserClass'])) {
            $parser = TldParser::createByClass($data['parserClass'], isset($data['parserType']) ? $data['parserType'] : null);
        } elseif (isset($data['parserType'])) {
            $parser = TldParser::create($data['parserType']);
        }
        return new TldServer(
            isset($data['zone']) ? $data['zone'] : '',
            isset($data['host']) ? $data['host'] : '',
            !empty($data['centralized']),
            $parser ? $parser : TldParser::create(),
            isset($data['queryFormat']) ? $data['queryFormat'] : null
        );
    }

    /**
     * @param array $dataList
     * @param TldParser $defaultParser
     * @return TldServer[]
     */
    public static function fromDataList($dataList, TldParser $defaultParser = null)
    {
        $defaultParser = $defaultParser ? $defaultParser : TldParser::create();
        $servers = [];
        foreach ($dataList as $data) {
            $servers[] = self::fromData($data, $defaultParser);
        }
        return $servers;
    }

    /**
     * @param string $zone
     * @param string $host
     * @param bool $centralized
     * @param TldParser $parser
     * @param string $queryFormat
     * @throws InvalidArgumentException
     */
    public function __construct($zone, $host, $centralized, TldParser $parser, $queryFormat = null)
    {
        $this->zone = strval($zone);
        if (empty($this->zone)) {
            throw new InvalidArgumentException("Zone must be specified");
        }
        $this->host = strval($host);
        if (empty($this->host)) {
            throw new InvalidArgumentException("Host must be specified");
        }
        $this->centralized = (bool)$centralized;
        $this->parser = $parser;
        $this->queryFormat = !empty($queryFormat) ? strval($queryFormat) : "%s\r\n";
    }

    /** @var string */
    private $zone;

    /** @var bool */
    private $centralized;

    /** @var string */
    private $host;
    
    /** @var TldParser */
    private $parser;

    /** @var string */
    private $queryFormat;

    /**
     * @return bool
     */
    public function isCentralized()
    {
        return (bool)$this->centralized;
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function isDomainZone($domain)
    {
        return $this->matchDomainZone($domain) > 0;
    }

    /**
     * @param string $domain
     * @return int
     */
    public function matchDomainZone($domain)
    {
        $zone = $this->zone;
        $pos = mb_strrpos($domain, $zone);
        return (int)($pos !== false && $pos == (mb_strlen($domain) - mb_strlen($zone)));
    }

    /**
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return TldParser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @return string
     */
    public function getQueryFormat()
    {
        return $this->queryFormat;
    }

    /**
     * @param string $domain
     * @param bool $strict
     * @return string
     */
    public function buildDomainQuery($domain, $strict = false)
    {
        $query = sprintf($this->queryFormat, $domain);
        return $strict ? "=$query" : $query;
    }
}
