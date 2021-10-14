<?php

namespace Jstewmc\Chunker;

class Text extends Chunker
{
    private $text;

    public function __construct(string $text, ?string $encoding = null, int $size = 2000)
    {
        $this->text = $text;

        parent::__construct($size, $encoding);
    }

    public function countChunks(): int
    {
        return ceil(mb_strlen($this->text, $this->encoding) / $this->size);
    }

    protected function getChunk(int $offset)
    {
        $this->validateOffset($offset);

        if ($offset < mb_strlen($this->text)) {
            return mb_substr($this->text, $offset, $this->size, $this->encoding);
        }

        return false;
    }
}
