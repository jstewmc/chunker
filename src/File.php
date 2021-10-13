<?php

namespace Jstewmc\Chunker;

class File extends Chunker
{
    /**
     * @var  int  the maximum size in bytes of a character in *any* multi-byte
     *     encoding; it seems like UTF-32, at four-bytes per character and every
     *     character ever, is the sensible max
     */
    private const MAX_SIZE_CHARACTER = 4;

    private string $name;

    public function __construct(string $name, ?string $encoding = null, int $size = 8192)
    {
        $this->setName($name);

        parent::__construct($size, $encoding);
    }

    public function countChunks(): int
    {
        return ceil(filesize($this->name) / $this->size);
    }

    /**
     * Returns a multi-byte-safe file chunk (or false)
     *
     * @return  string|false
     */
    protected function getChunk(int $offset)
    {
        $this->validateOffset($offset);

        $singleByteChunk = $this->getSingleByteChunk($offset);

        // if reading the file failed or it was empty, short-circuit
        if ($singleByteChunk === false || $singleByteChunk === '') {
            return false;
        }

        return $this->getMultiByteChunk($singleByteChunk);
    }

    /**
     * When reading from multi-byte files, the chunk needs to contain at least
     * one multi-byte character.
     *
     * For very low chunk sizes around one or two bytes - like those initially
     * considered for testing - this library will return an empty string (the
     * native PHP method it uses will adjust the chunk to begin and end at the
     * same byte).
     */
    protected function setSize(int $size): self
    {
        if ($size < self::MAX_SIZE_CHARACTER) {
            throw new \InvalidArgumentException(
                "size must be greater than {self::MAX_SIZE_CHARACTER}"
            );
        }

        $this->size = $size;

        return $this;
    }

    /**
     * Returns a "single-byte" (aka, "sb") chunk (or false)
     *
     * PHP's `file_get_contents()` method uses bytes to read data. However, when
     * the contents are multi-byte characters, using bytes can result in
     * malformed byte sequences if a multi-byte character is partially read.
     *
     * For example:
     *
     *   string: AAAABBBBCCCC  (a string with three four-byte characters)
     *   chunk:  ----------    (a 10-byte chunk, produces "AB?")
     *
     * I'll pad a chunk in both directions by the maximum length of a multi-byte
     * character in any encoding. That way, no matter what the encoding is, the
     * chunk is always padded with a well-formed multi-byte sequence.
     *
     * For example:
     *
     *     string: AAAABBBBCCCC    (a string with three four-byte characters)
     *     chunk:  ----------pppp  (a 10-byte chunk, with four-bytes padding)
     *
     * Keep in mind, if file_get_contents() encounters an invalid offset, it
     * will return false AND raise an E_WARNING, which we don't want.
     *
     * Also, be sure you floor the offset to 0. Negative values will start that
     * many bytes from the end of the file.
     *
     * @param  int  $offset
     * @return  string|false
     */
    private function getSingleByteChunk(int $offset)
    {
		// phpcs:ignore Generic.PHP.NoSilencedErrors.Forbidden -- supress invalid offset errors
        return @file_get_contents(
            $this->name,
            false,
            null,
            max(0, $offset - self::MAX_SIZE_CHARACTER),
            $this->size + self::MAX_SIZE_CHARACTER
        );
    }

    /**
     * Returns the multi-byte string within the single-byte string
     *
     * Given a padded single-byte chunk, I'll use PHP's multi-byte safe
     * `mb_strcut()` function to return the well-formed multi-byte chunk within.
     *
     * If the intended chunk begins in the middle of a multi-byte character,
     * `mb_strcut()` will move the cut to the left to include the character.
     *
     * For example:
     *
     *     string: ZZZZAAAABBBBCCCC  (a string with four four-byte characters)
     *     chunk:    pppp----------  (a 10-byte chunk with four-byte padding)
     *     strcut:     ------------  (the adjusted multi-byte chunk)
     *     output: "ABC"
     *
     * If the intended chunk ends in the middle of a multi-byte character,
     * `mb_strcut()` will move the cut to the right to exclude the character.
     *
     * For example:
     *
     *     string: AAAABBBBCCCCDDDD  (a string with four four-byte characters)
     *     chunk:  ----------pppp    (a 10-byte chunk with four-byte padding)
     *     strcut: ------------      (the adjusted multi-byte chunk)
     */
    private function getMultiByteChunk(string $singleByteChunk): string
    {
        return mb_strcut(
            $singleByteChunk,
            // The first chunk has no leading padding.
            $this->index === 0 ? 0 : self::MAX_SIZE_CHARACTER,
            $this->size,
            $this->encoding
        );
    }

    private function setName(string $name): self
    {
        if (!is_readable($name)) {
            throw new \InvalidArgumentException(
                "file, $name, must exist and be readable"
            );
        }

        $this->name = $name;

        return $this;
    }
}
