<?php

namespace App\Models;

class PostIterator implements \Iterator
{
    protected $parser;

    protected $params;

    protected $totalCount;

    protected $fetchedCount;

    protected $key;

    public function __construct(WallParser $parser, $params)
    {
        $this->parser = $parser;
        $this->params = $params;

        $this->totalCount = $params['count'];
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        $res = $this->parser->request('wall.get', $this->params);
        $this->totalCount = $res['count'];

        return $res;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->fetchedCount += $this->params['count'];
        $this->params['offset'] = $this->fetchedCount;
        $this->key++;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->fetchedCount < $this->totalCount;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->fetchedCount = 0;
        $this->params['offset'] = 0;
        $this->key = 0;
    }
}
