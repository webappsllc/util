<?php declare(strict_types=1);

namespace Webapps\Util;

use Countable;

use with;
use Illuminate\Support\Collection;

use Webapps\Util\Traits\MakeWithKeywords;

class BatchingBuffer implements Countable
{
    use MakeWithKeywords;

    protected $buffer;
    protected $limit;
    protected $onFlush;

    public function __construct(int $limit = 1000, ?callable $onFlush = null)
    {
        $this->limit = $limit;
        $this->onFlush = $onFlush;
        $this->buffer = new Collection;
    }

    public function count() : int {
        return $this->buffer->count();
    }

    public function isEmpty() : bool {
        return $this->buffer->isEmpty();
    }

    public function atLimit() : bool {
        return $this->buffer->count() >= $this->limit;
    }

    public function push($item, ?callable $onFlush = null) {
        $this->buffer->push($item);
        $this->attemptFlush($onFlush);
    }

    public function pushMany(iterable $itemList, ?callable $onFlush = null)
    {
        foreach ($itemList as $item) {
            $this->buffer->push($item);
        }
        $this->attemptFlush($onFlush);
    }

    public function attemptFlush(?callable $onFlush = null)
    {
        while ($this->atLimit()) {
            $this->forceFlush($onFlush);
        }
    }

    public function forceFlush(?callable $onFlush = null)
    {
        if($this->isEmpty()) { return; }
        $rest = $this->buffer->splice($this->limit);
        with($this->buffer, $this->onFlush);
        with($this->buffer, $onFlush);
        $this->buffer = $rest;
    }
}
