<?php

namespace Iodev\Whois\Helpers;

use Iodev\Whois\ResponseGroup;

class ResponseHelper
{
    /**
     * @param string $content
     * @return ResponseGroup[]
     */
    public static function contentToGroups($content)
    {
        $groups = [];
        $splits = preg_split('/([\s\t]*\r?\n){2,}/', $content);
        foreach ($splits as $split) {
            $data = [];
            preg_match_all('/^\s*(( *[\w-]+)+):[ \t]+(.+)$/mui', $split, $m);
            foreach ($m[1] as $index => $key) {
                $data = array_merge_recursive($data, [ $key => $m[3][$index] ]);
            }
            if (count($data) > 2) {
                $groups[] = new ResponseGroup($data);
            }
        }
        return $groups;
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @param bool $ignoreCase
     * @return mixed|bool
     */
    public static function firstGroupMatch($group, $keys, $ignoreCase = true)
    {
        $kDict = [];
        foreach ($keys as $k) {
            $k = $ignoreCase ? strtolower($k) : $k;
            $kDict[$k] = 1;
        }
        foreach ($group as $k => $v) {
            $k = $ignoreCase ? strtolower($k) : $k;
            if (isset($kDict[$k])) {
                return $v;
            }
        }
        return false;
    }

    /**
     * @param array $groups
     * @param string $domain
     * @param string[] $domainParseKeys
     * @return array
     */
    public static function findDomainGroup($groups, $domain, $domainParseKeys = null)
    {
        foreach ($groups as $group) {
            $foundDomain = self::parseDomainName($group, $domainParseKeys);
            if ($foundDomain && DomainHelper::compareNames($foundDomain, $domain)) {
                return $group;
            }
        }
        return null;
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @return string
     */
    public static function parseDomainName($group, $keys = null)
    {
        $keys = !empty($keys) ? $keys : [ "domain", "domainname", "domain name" ];
        return DomainHelper::toAscii(self::firstGroupMatch($group, $keys));
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @return string[]
     */
    public static function parseNameServersAscii($group, $keys = null)
    {
        $keys = !empty($keys) ? $keys : [ "nserver", "nameserver", "name server" ];
        $nservers = [];
        $arr = self::firstGroupMatch($group, $keys);
        $arr = isset($arr) ? $arr : [];
        $arr = is_array($arr) ? $arr : [ $arr ];
        foreach ($arr as $nserv) {
            $nservers[] = DomainHelper::toAscii($nserv);
        }
        return $nservers;
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @return int
     */
    public static function parseDate($group, $keys)
    {
        return DateHelper::parseDate(self::firstGroupMatch($group, $keys));
    }
}
