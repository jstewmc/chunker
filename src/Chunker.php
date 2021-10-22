<?php

namespace Jstewmc\Chunker;

abstract class Chunker
{
    /**
     * The file or string's character encoding
     */
    protected string $encoding;

    /**
     * The current chunk index (defaults to 0)
     */
    protected int $index = 0;

    /**
     * The chunk size in *bytes* for a file chunk and *characters* for text
     */
    protected int $size;

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function __construct(int $size, ?string $encoding = null)
    {
        $this->setSize($size);

        $this->setEncoding($encoding ?: mb_internal_encoding());
    }

    /**
     * Alias for the getCurrentChunk() method
     *
     * @return  string|false
     */
    public function current()
    {
        return $this->getCurrentChunk();
    }

    /**
     * Returns the current chunk
     *
     * @return  string|false
     */
    public function getCurrentChunk()
    {
        return $this->getChunk($this->index * $this->size);
    }

    /**
     * Returns the number of chunks in the file or text
     */
    abstract public function countChunks(): int;

    /**
     * Returns the next chunk and updates the internal pointer
     *
     * @return  string|false
     */
    public function getNextChunk()
    {
        if (!$this->hasNextChunk()) {
            return false;
        }

        return $this->getChunk(++$this->index * $this->size);
    }

    /**
     * Returns the previous chunk and updates the internal pointer
     *
     * @return  string|false
     */
    public function getPreviousChunk()
    {
        if (!$this->hasPreviousChunk()) {
            return false;
        }

        return $this->getChunk(--$this->index * $this->size);
    }

    public function hasChunk(): bool
    {
        return $this->countChunks() === 1;
    }

    public function hasChunks(): bool
    {
        return $this->countChunks() > 0;
    }

    public function hasNextChunk(): bool
    {
        return ($this->index + 1) < $this->countChunks();
    }

    public function hasPreviousChunk(): bool
    {
        return ($this->index - 1) >= 0;
    }

    /**
     * Alias for the getNextChunk() method
     *
     * @return  string|false
     */
    public function next()
    {
        return $this->getNextChunk();
    }

    /**
     * Alias for the getPreviousChunk() method
     *
     * @return  string|false
     */
    public function previous()
    {
        return $this->getPreviousChunk();
    }

    public function reset(): void
    {
        $this->index = 0;
    }

    /**
     * Returns a chunk starting at $offset (or returns false)
     *
     * @return  string|false
     */
    abstract protected function getChunk(int $offset);

    protected function validateOffset(int $offset): void
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException(
                "offset must be a positive int or zero"
            );
        }
    }

    protected function setSize(int $size): self
    {
        if ($size < 1) {
            throw new \InvalidArgumentException(
                "size must be a positive integer"
            );
        }

        $this->size = $size;

        return $this;
    }

    private function setEncoding(string $encoding): self
    {
        if (!in_array($encoding, mb_list_encodings())) {
            throw new \InvalidArgumentException(
                "encoding must be valid multi-byte character encoding"
            );
        }

        $this->encoding = $encoding;

        return $this;
    }
}
