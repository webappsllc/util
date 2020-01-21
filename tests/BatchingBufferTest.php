<?php declare(strict_types=1);

namespace Webapps\Tests;

use Webapps\Tests\TestCase;

use Illuminate\Support\Collection;

use Webapps\Util\BatchingBuffer;

class BatchingBufferTest extends TestCase
{
    public function setUp() : void {
        $this->flushCount = 0;
        $this->itemsFlushed = 0;

        $this->buffer = BatchingBuffer::make([
            'onFlush' => [$this, 'onFlush'],
            'limit' => 4
        ]);
    }

    /** @test */
    public function force_flush_flushes_buffer() {
        $flushCount = 0;
        $itemsFlushed = 0;
        $this->buffer->push(1);
        $this->buffer->forceFlush();

        $this->assertEquals($this->flushCount, 1);
        $this->assertEquals($this->itemsFlushed, 1);
    }

    /** @test */
    public function attempt_flush_flushes_buffer_when_over_limit() {
        $this->buffer->push(1);
        $this->buffer->attemptFlush();

        $this->assertEquals($this->flushCount, 0);
        $this->assertEquals($this->itemsFlushed, 0);
        $this->assertEquals($this->buffer->count(), 1);

        $this->buffer->pushMany([2, 3]);

        $this->assertEquals($this->flushCount, 0);
        $this->assertEquals($this->itemsFlushed, 0);
        $this->assertEquals($this->buffer->count(), 3);

        $this->buffer->pushMany([4, 5]);

        $this->assertEquals($this->flushCount, 1);
        $this->assertEquals($this->itemsFlushed, 4);
        $this->assertEquals($this->buffer->count(), 1);
    }

    /** @test */
    public function buffer_can_handle_many_things_properly() {
        $this->buffer->push(1);
        $this->buffer->attemptFlush();

        $this->assertEquals($this->flushCount, 0);
        $this->assertEquals($this->itemsFlushed, 0);

        $this->buffer->pushMany([2, 3, 4]);

        $this->assertEquals($this->flushCount, 1);
        $this->assertEquals($this->itemsFlushed, 4);
        $this->assertEquals($this->buffer->count(), 0);

        $this->buffer->pushMany(range(1, 102));

        $this->assertEquals($this->buffer->count(), 2);
        $this->assertEquals($this->flushCount, 26);
        $this->assertEquals($this->itemsFlushed, 104);
    }

    public function onFlush(Collection $buffer) : void {
        $this->flushCount++;
        $this->itemsFlushed += $buffer->count();
    }
}
