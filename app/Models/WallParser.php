<?php

namespace App\Models;

class WallParser
{
    protected $apiUrl = 'https://api.vk.com/method/';

    protected $version;

    protected $defaultCount = 100;

    public function __construct($version = '5.40')
    {
        $this->version = $version;
    }

    public function getPosts($params)
    {
        $params['count'] = isset($params['count']) ? $params['count'] : $this->defaultCount;
        $params['offset'] = 0;
        $totalCount = $this->defaultCount;
        $fetchedCount = 0;

        while ($fetchedCount < $totalCount) {
            $res = $this->request('wall.get', $params);
            $totalCount = $res['count'];
            $fetchedCount += $params['count'];
            $params['offset'] = $fetchedCount;

            yield $res;
        }
    }

    protected function request($method, $params)
    {
        $params['v'] = $this->version;

        $cacheFile = 'cache/' . $method . '?' . http_build_query($params);
        if (file_exists($cacheFile)) {
            $res = unserialize(file_get_contents($cacheFile));
        } else {
            $res = file_get_contents($this->apiUrl . $method . '?' . http_build_query($params));
            file_put_contents($cacheFile, serialize($res));
        }

        if ($res === false) {
            throw new \Exception('Unable to get data from vk.com');
        }

        $res = json_decode($res, true);
        if ($res === false) {
            throw new \Exception('Wrong json');
        }

        if (isset($res['error'])) {
            throw new \Exception($res['error']['error_msg'], $res['error']['error_code']);
        }

        if (!isset($res['response'])) {
            throw new \Exception('Response key not found in json');
        }

        return $res['response'];
    }
}