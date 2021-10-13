<?php

namespace Jstewmc\Chunker;

/**
 * Provides helper methods for easier testing
 *
 * I provide a number of helper functions to make the tests easier to read and
 * to avoid any system-level, character-encoding issues:
 *
 *   - The `setUp()` and `tearDown()` methods ensure the system's internal
 *     encoding doesn't impact tests.
 *   - The `*Character()` methods provide multi-byte characters programmatically
 *     to avoid any system-level issues parsing strings.
 *   - The `*ByteString()` methods provide known single- and multi-byte strings
 *     for easier assertiions.
 *   - The `*Chunker()` methods return chunkers with various initial conditions
 *     (e.g., zero chunks, one chunk, many chunks, etc).
 *   - The `*Chunk*()` methods return expected chunks.
 *
 * NOTE! One of the biggest differences between testing strings and testing
 * is the chunk size. When testing files, the chunk size must be greater than
 * the `File::MAX_SIZE_CHARACTER` value.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected const ENCODING = 'UTF-8';

    // Don't include type here, or it'll throw an exception: `Error: Typed
    // property must not be accessed before initialization`
    protected $systemEncoding;


    protected function setUp(): void
    {
        $this->setSystemEncoding();
    }

    protected function tearDown(): void
    {
        $this->unsetSystemEncoding();
    }


    abstract protected function emptyChunker(): Chunker;

    abstract protected function oneChunkChunker(): Chunker;

    protected function manyChunkChunker(): Chunker
    {
        // Either the single- or multi-byte chunker will do.
        return $this->singleByteChunker();
    }

    abstract protected function singleByteChunker(): Chunker;

    abstract protected function multiByteChunker(): Chunker;


    abstract protected function multiByteString(): string;

    abstract protected function multiByteChunk1(): string;

    abstract protected function multiByteChunk2(): string;

    abstract protected function multiByteChunk3(): string;


    abstract protected function singleByteString(): string;

    abstract protected function singleByteChunk1(): string;

    abstract protected function singleByteChunk2(): string;

    abstract protected function singleByteChunk3(): string;


    protected function twoByteCharacter(): string
    {
        return mb_convert_encoding('&#xA2;', self::ENCODING, 'HTML-ENTITIES');
    }

    protected function threeByteCharacter(): string
    {
        return mb_convert_encoding('&#x20AC;', self::ENCODING, 'HTML-ENTITIES');
    }


    private function setSystemEncoding(): void
    {
        $this->systemEncoding = mb_internal_encoding();

        mb_internal_encoding(self::ENCODING);
    }

    private function unsetSystemEncoding(): void
    {
        // If the encoding is empty (as has happened on CircleCI's
        // 7.4-node-browbsers image), short-circuit.
        if (!$this->systemEncoding) {
            return;
        }

        mb_internal_encoding($this->systemEncoding);
    }
}
