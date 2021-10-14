<?php

namespace Jstewmc\Chunker;

use org\bovigo\vfs\{vfsStream, vfsStreamDirectory, vfsStreamFile};

class FileTest extends TestCase
{
    /**
     * Use a chunk size of eight bytes, because chunk size must be greater than
     * the `File::MAX_SIZE_CHARACTER` constant.
     */
    private const SIZE = 8;

    private vfsStreamDirectory $root;

    public function setUp(): void
    {
        $this->root = vfsStream::setup('root');
    }

    public function testConstructThrowsInvalidArgumentExceptionWhenFileIsNotReadable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new File("{$this->root->url()}/example.txt");
    }

    public function testConstructThrowsInvalidArgumentExceptionWhenEncodingIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new File($this->emptyFile()->url(), 'foo');
    }

    public function testConstructThrowsInvalidArgumentExceptionWhenSizeIsTooSmall(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new File($this->emptyFile()->url(), self::ENCODING, 1);
    }

    public function testGetEncodingReturnsString(): void
    {
        $this->assertEquals(self::ENCODING, $this->singleByteChunker()->getEncoding());
    }

    public function testGetIndexReturnsInt(): void
    {
        $this->assertEquals(0, $this->singleByteChunker()->getIndex());
    }

    public function testGetSizeReturnsInt(): void
    {
        $this->assertEquals(self::SIZE, $this->singleByteChunker()->getSize());
    }

    public function testGetCurrentChunkReturnsFalseWhenContentIsEmpty(): void
    {
        $this->assertFalse($this->emptyChunker()->getCurrentChunk());
    }

    public function testGetCurrentChunkReturnsStringWhenContentIsSingleByte(): void
    {
        $this->assertEquals(
            $this->singleByteChunk1(),
            $this->singleByteChunker()->getCurrentChunk()
        );
    }

    public function testGetCurrentChunkReturnsStringWhenContentIsMultiByte(): void
    {
        $this->assertEquals(
            $this->multiByteChunk1(),
            $this->multiByteChunker()->getCurrentChunk()
        );
    }

    public function testCurrentReturnsFalseWhenContentIsEmpty(): void
    {
        $this->assertFalse($this->emptyChunker()->current());
    }

    public function testCurrentReturnsStringWhenContentIsSingleByte(): void
    {
        $this->assertEquals(
            $this->singleByteChunk1(),
            $this->singleByteChunker()->current()
        );
    }

    public function testCurrentReturnsStringWhenContentIsMultiByte(): void
    {
        $this->assertEquals(
            $this->multiByteChunk1(),
            $this->multiByteChunker()->current()
        );
    }

    public function testCountChunksReturnsIntWhenFileIsEmpty(): void
    {
        $this->assertEquals(0, $this->emptyChunker()->countChunks());
    }

    public function testCountChunksReturnsIntWhenFileIsSingleByte(): void
    {
        $this->assertEquals(4, $this->singleByteChunker()->countChunks());
    }

    public function testCountChunksReturnsIntWhenFileIsMultiByte(): void
    {
        $this->assertEquals(4, $this->multiByteChunker()->countChunks());
    }

    public function testGetNextChunkReturnsFalseWhenNextChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->getNextChunk());
    }

    public function testGetNextChunkReturnsStringWhenNextChunkIsSingleByte(): void
    {
        $this->assertEquals(
            $this->singleByteChunk2(),
            $this->singleByteChunker()->getNextChunk()
        );
    }

    public function testGetNextChunkReturnsStringWhenNextChunkIsMultiByte(): void
    {
        $this->assertEquals(
            $this->multiByteChunk2(),
            $this->multiByteChunker()->getNextChunk()
        );
    }

    public function testNextReturnsFalseWhenNextChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->next());
    }

    public function testNextReturnsStringWhenNextChunkIsSingleByte(): void
    {
        $this->assertEquals(
            $this->singleByteChunk2(),
            $this->singleByteChunker()->next()
        );
    }

    public function testNextReturnsStringWhenNextChunkIsMultiByte(): void
    {
        $this->assertEquals(
            $this->multiByteChunk2(),
            $this->multiByteChunker()->next()
        );
    }

    public function testGetPreviousChunkReturnsFalseWhenPreviousChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->getPreviousChunk());
    }

    public function testGetPreviousChunkReturnsStringWhenPreviousChunkIsSingleByte(): void
    {
        $chunker = $this->singleByteChunker();

        $chunker->next();

        $this->assertEquals(
            $this->singleByteChunk1(),
            $chunker->getPreviousChunk()
        );
    }

    public function testGetPreviousChunkReturnsFalseWhenPreviousChunkIsMultiByte(): void
    {
        $chunker = $this->multiByteChunker();

        $chunker->next();

        $this->assertEquals(
            $this->multiByteChunk1(),
            $chunker->getPreviousChunk()
        );
    }

    public function testPreviousReturnsFalseWhenPreviousChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->previous());
    }

    public function testPreviousReturnsStringWhenPreviousChunkIsSingleByte(): void
    {
        $chunker = $this->singleByteChunker();

        $chunker->next();

        $this->assertEquals($this->singleByteChunk1(), $chunker->previous());
    }

    public function testPreviousReturnsFalseWhenPreviousChunkIsMultiByte(): void
    {
        $chunker = $this->multiByteChunker();

        $chunker->next();

        $this->assertEquals($this->multiByteChunk1(), $chunker->previous());
    }

    public function testHasChunkReturnsFalseWhenFileIsEmpty(): void
    {
        $this->assertFalse($this->emptyChunker()->hasChunk());
    }

    public function testHasChunkReturnsTrueWhenFileHasOneChunk(): void
    {
        $this->assertTrue($this->oneChunkChunker()->hasChunk());
    }

    public function testHasChunkReturnsFalseWhenFileHasManyChunks(): void
    {
        $this->assertFalse($this->manyChunkChunker()->hasChunk());
    }

    public function testHasChunksReturnsFalseWhenFileIsEmpty(): void
    {
        $this->assertFalse($this->emptyChunker()->hasChunks());
    }

    public function testHasChunksReturnsTrueWhenFileHasOneChunk(): void
    {
        $this->assertTrue($this->oneChunkChunker()->hasChunks());
    }

    public function testHasChunksReturnsTrueWhenFileHasManyChunks(): void
    {
        $this->assertTrue($this->manyChunkChunker()->hasChunks());
    }

    public function testHasNextChunkReturnsFalseWhenNextChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->hasNextChunk());
    }

    public function testHasNextChunkReturnsTrueWhenNextChunkDoesExist(): void
    {
        $this->assertTrue($this->manyChunkChunker()->hasNextChunk());
    }

    public function testHasPreviousChunkReturnsFalseWhenPreviousChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->hasPreviousChunk());
    }

    public function testHasPreviousChunkReturnsTrueWhenPreviousChunkDoesExist(): void
    {
        $chunker = $this->manyChunkChunker();

        $chunker->next();

        $this->assertTrue($chunker->hasPreviousChunk());
    }


    public function testResetResetsInternalPointer(): void
    {
        $chunker = $this->manyChunkChunker();

        $chunker->next();

        $this->assertEquals(1, $chunker->getIndex());

        $chunker->reset();

        $this->assertEquals(0, $chunker->getIndex());
    }

    protected function emptyChunker(): File
    {
        return new File($this->emptyFile()->url());
    }

    protected function oneChunkChunker(): File
    {
        return new File(
            $this->singleByteFile()->url(),
            self::ENCODING,
            strlen($this->singleByteString())
        );
    }

    protected function singleByteChunker(): File
    {
        return new File($this->singleByteFile()->url(), self::ENCODING, self::SIZE);
    }

    protected function multiByteChunker(): File
    {
        return new File($this->multiByteFile()->url(), self::ENCODING, self::SIZE);
    }

    protected function singleByteString(): string
    {
        return 'foo bar baz qux quux corge';
    }

    protected function singleByteChunk1(): string
    {
        return 'foo bar ';
    }

    protected function singleByteChunk2(): string
    {
        return 'baz qux ';
    }

    protected function singleByteChunk3(): string
    {
        return 'quux cor';
    }

    protected function multiByteString(): string
    {
        return "foo {$this->twoByteCharacter()} bar " .
            "{$this->threeByteCharacter()} baz {$this->twoByteCharacter()} " .
            "{$this->threeByteCharacter()}";
    }

    protected function multiByteChunk1(): string
    {
        return "foo {$this->twoByteCharacter()} b";
    }

    protected function multiByteChunk2(): string
    {
        return "ar {$this->threeByteCharacter()} b";
    }

    protected function multiByteChunk3(): string
    {
        return "az {$this->twoByteCharacter()} ";
    }

    private function emptyFile(): vfsStreamFile
    {
        return vfsStream::newFile('example.txt')->at($this->root);
    }

    private function singleByteFile(): vfsStreamFile
    {
        return vfsStream::newFile('example.txt')
            ->withContent($this->singleByteString())
            ->at($this->root);
    }

    private function multiByteFile(): vfsStreamFile
    {
        return vfsStream::newFile('example.txt')
            ->withContent($this->multiByteString())
            ->at($this->root);
    }
}
