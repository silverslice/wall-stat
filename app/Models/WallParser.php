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

        return new PostIterator($this, $params);
    }

    public function request($method, $params)
    {
        $params['v'] = $this->version;

        $res = file_get_contents($this->apiUrl . $method . '?' . http_build_query($params));

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