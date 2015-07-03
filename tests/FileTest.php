<?php
/**
 * The FileTest class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Chunker;

/**
 * A test suite for the File class
 *
 * @since  0.1.0
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	/* !Protected properties */
	
	/**
	 * @var  string  the name of a file that does not exst
	 */
	protected $fileDoesNotExist;
	
	/**
	 * @var  string  the name of a file that does exist but is empty
	 */
	protected $fileIsEmpty;
	
	/**
	 * @var  string  the name of a file with single-byte characters
	 */
	protected $fileHasSbCharacters;
	
	/**
	 * @var  string  the name of a file with multi-byte characters
	 */
	protected $fileHasMbCharacters;
	

	/* !Magic methods */
	
	/**
	 * Called before each test
	 *
	 * I'll (drop) create a fresh test file.
	 *
	 * @return  void
	 */
	public function setUp()
	{	
		$this->fileDoesNotExist    = dirname(__FILE__).DIRECTORY_SEPARATOR.'foo.txt';
		$this->fileIsEmpty         = dirname(__FILE__).DIRECTORY_SEPARATOR.'bar.txt';
		$this->fileHasSbCharacters = dirname(__FILE__).DIRECTORY_SEPARATOR.'baz.txt';
		$this->fileHasMbCharacters = dirname(__FILE__).DIRECTORY_SEPARATOR.'qux.txt';
		
		// delete the empty file if it exists
		if (is_file($this->fileIsEmpty)) {
			unlink($this->fileIsEmpty);	
		}
		
		// create a new empty file
		file_put_contents($this->fileIsEmpty, null);
		
		// delete the single-byte file if it exists
		if (is_file($this->fileHasSbCharacters)) {
			unlink($this->fileHasSbCharacters);
		}
		
		// create a new single-byte file
		file_put_contents($this->fileHasSbCharacters, 'foo bar baz');
		
		// delete the multi-byte file if it exists
		if (is_file($this->fileHasMbCharacters)) {
			unlink($this->fileHasMbCharacters);
		}
		
		// create a new multi-byte file
		file_put_contents($this->fileHasMbCharacters, self::cent().self::euro());
		
		return;
	}
	
	/**
	 * Returns the UTF-8 two-byte cent character
	 *
	 * @return  string
	 */
	public static function cent()
	{
		return mb_convert_encoding('&#xA2;', 'UTF-8', 'HTML-ENTITIES');
	}
	
	/**
	 * Returns the UTF-8 three-byte euro character
	 *
	 * @return  string
	 */
	public static function euro()
	{
		return mb_convert_encoding('&#x20AC;', 'UTF-8', 'HTML-ENTITIES');
	}
		
	
	/**
	 * Called after each test
	 *
	 * I'll delete the test files.
	 *
	 * @return  void
	 */
	public function tearDown()
	{
		// delete the empty file if it exists
		if (is_file($this->fileIsEmpty)) {
			unlink($this->fileIsEmpty);	
		}
		
		// delete the single-byte file if it exists
		if (is_file($this->fileHasSbCharacters)) {
			unlink($this->fileHasSbCharacters);
		}
		
		// delete the multi-byte file if it exists
		if (is_file($this->fileHasMbCharacters)) {
			unlink($this->fileHasMbCharacters);
		}

		return;
	}
	
	
	/* !getEncoding() */
	
	/**
	 * getEncoding() should return the chunker's encoding
	 */
	public function testSetEncoding_returnsEncoding()
	{
		$chunker = new File();
		$chunker->setEncoding('UTF-8');
		
		$this->assertEquals('UTF-8', $chunker->getEncoding());
		
		return;
	}
	
	/* !getIndex() */
	
	/**
	 * getIndex() should return the chunker's index
	 */
	public function testSetIndex_returnsIndex()
	{
		$chunker = new File();
		$chunker->setIndex(1);
		
		$this->assertEquals(1, $chunker->getIndex());
		
		return;
	}
	
	/* !getName() */
	
	/**
	 * getName() should return the chunker's file name
	 */
	public function testGetFile_returnsFile()
	{
		$chunker = new File();
		$chunker->setName($this->fileIsEmpty);
		
		$this->assertEquals($this->fileIsEmpty, $chunker->getName());
		
		return;
	}
	
	
	/* !getSize() */
	
	/**
	 * getSize() should return the chunker's size
	 */
	public function testSetSize_returnsSize()
	{
		$chunker = new File();
		$chunker->setSize(1);
		
		$this->assertEquals(1, $chunker->getSize());
		
		return;
	}
	

	
	/* !__construct() */

	/**
	 * __construct() should throw an InvalidArgumentException if $name is not a string
	 */
	public function testConstruct_throwsInvalidArgumentException_ifNameIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File(1);
		
		return;
	}
	
	/**
	 * __construct() should throw an InvalidArgumentException if $encoding is not a string
	 */
	public function testConstruct_throwsInvalidArgumentException_ifEncodingIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File($this->fileIsEmpty, 1);
		
		return;
	}
	
	/**
	 * __construct() should throw an InvalidArgumentException if $encoding is not a valid encoding
	 */
	public function testConstruct_throwsInvalidArgumentException_ifEncodingIsNotSupported()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File($this->fileIsEmpty, 'foo');
		
		return;
	}
	
	/**
	 * __construct() should return file chunker if $text exists
	 */
	public function testConstruct_returnsObject_ifFileDoesExist()
	{
		$chunker = new File($this->fileIsEmpty);
		
		$this->assertEquals($this->fileIsEmpty, $chunker->getName());
		
		return;
	}
	
	/**
	 * __construct() should return chunker if $encoding exists
	 */
	public function testConstruct_returnsObject_ifFileAndEncodingDoesExist()
	{
		$chunker = new File($this->fileIsEmpty, 'UTF-8');
		
		$this->assertEquals($this->fileIsEmpty, $chunker->getName());
		$this->assertEquals('UTF-8', $chunker->getEncoding());
		
		return;
	}
	
	/**
	 * __construct() should return object if $name does not exist
	 */
	public function testConstruct_returnsObject_ifFileDoesNotExist()
	{
		$chunker = new File();
		
		$this->assertNull($chunker->getName());
		
		return;
	}
	
	
	/* !getCurrentChunk() */
	
	/**
	 * getCurrentChunk() should return false if contents do not exist
	 */
	public function testGetCurrentChunk_returnsFalse_ifContentDoesNotExist()
	{
		$chunker = new File();
		
		$this->assertFalse($chunker->getCurrentChunk());
		
		return;
	}
	
	/**
	 * getCurrentChunk() should return string if single-byte content does exist
	 */
	public function testGetCurrentChunk_returnsString_ifSbContentDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(4);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertEquals('foo ', $chunker->getCurrentChunk());
		
		return;
	}
	
	/**
	 * getCurrentChunk() should return string if multi-byte content does exist
	 */
	public function testGetCurrentChunk_returnsString_ifMbContentDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(4);
		$chunker->setName($this->fileHasMbCharacters);
		
		// the file has two-byte + three-byte characters
		$this->assertEquals(self::cent(), $chunker->getCurrentChunk());
		
		return;
	}
	
	
	/* !getMaxChunks() */
	
	/**
	 * getMaxChunks() should return int if content does not exist
	 */
	public function testGetMaxChunks_returnsInt_ifFileDoesNotExist()
	{
		$chunker = new File();
		
		$this->assertEquals(0, $chunker->getMaxChunks());
		
		return;
	}
	
	/**
	 * getMaxChunks() should return int if single-byte text does exist
	 */
	public function testGetMaxChunks_returnsInt_ifSbFileDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertEquals(11, $chunker->getMaxChunks());
		
		return;
	}
	
	/**
	 * getMaxChunks() should return int if multi-byte text does exist
	 */
	public function testGetMaxChunks_returnsInt_ifMbFileDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(4);
		$chunker->setName($this->fileHasMbCharacters);
		
		$this->assertEquals(2, $chunker->getMaxChunks());
		
		return;
	}

	/* !getNextChunk() */
	
	/**
	 * getNextChunk() should return false if next chunk does not exist
	 */
	public function testGetNextChunk_returnsFalse_ifNextChunkDoesNotExist()
	{
		$chunker = new File();
		
		$this->assertFalse($chunker->getNextChunk());
		
		return;
	}
	
	/**
	 * getNextChunk() should return string if single-byte next chunk does exist
	 */
	public function testGetNextChunk_returnsString_ifNextChunkSbDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertEquals('o', $chunker->getNextChunk());
		
		return;
	}
	
	/**
	 * getNextChunk() should return string if multi-byte next chunk does exist
	 */
	public function testGetNextChunk_returnsString_ifNextChunkMbDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(4);
		$chunker->setName($this->fileHasMbCharacters);
		
		$this->assertEquals(self::euro(), $chunker->getNextChunk());
		
		return;
	}
	
	
	/* !getPreviousChunk() */
	
	/**
	 * getPreviousChunk() should return false if previous chunk does not exist
	 */
	public function testGetPreviousChunk_returnsFalse_ifPreviousChunkDoesNotExist()
	{
		$chunker = new File();
		
		$this->assertFalse($chunker->getPreviousChunk());
		
		return;
	}
	
	/**
	 * getPreviousChunk() should return false if previous single-byte chunk does exist
	 */
	public function testGetPreviousChunk_returnsFalse_ifPreviousChunkSbDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertEquals('f', $chunker->getPreviousChunk());
		
		return;
	}
	
	/**
	 * getPreviousChunk() should return false if previous multi-byte chunk does exist
	 */
	public function testGetPreviousChunk_returnsFalse_ifPreviousChunkMbDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(4);
		$chunker->setIndex(1);
		$chunker->setName($this->fileHasMbCharacters);
		
		$this->assertEquals(self::cent(), $chunker->getPreviousChunk());
		
		return;
	}
	
	/* !hasChunk() */
	
	/**
	 * hasChunk() should return false if the content does not exist
	 */
	public function testHasChunk_returnsFalse_ifFileHasZeroChunks()
	{
		$chunker = new File();
		
		$this->assertFalse($chunker->hasChunk());
		
		return;
	}
	
	/**
	 * hasChunk() should return true if the content has one chunk
	 */
	public function testHasChunk_returnsTrue_ifFileHasOneChunk()
	{
		$chunker = new File();
		
		$chunker->setSize(11);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertTrue($chunker->hasChunk());
		
		return;
	}
	
	/**
	 * hasChunk() should return false if the content has many chunks
	 */
	public function testHasChunk_returnsFalse_ifFileHasManyChunks()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertFalse($chunker->hasChunk());
		
		return;
	}
	
	
	/* !hasChunks() */
	
	/**
	 * hasChunks() should return false if the content has zero chunks
	 */
	public function testHasChunks_returnsFalse_ifFileHasZeroChunks()
	{
		$chunker = new File();
		
		$this->assertFalse($chunker->hasChunks());
		
		return;
	}
	
	/**
	 * hasChunks() should return true if the content has one chunk
	 */
	public function testHasChunks_returnsTrue_ifFileHasOneChunk()
	{
		$chunker = new File();
		
		$chunker->setSize(11);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertTrue($chunker->hasChunks());
		
		return;
	}
	
	/**
	 * hasChunks() should return true if the content has many chunks
	 */
	public function testHasChunks_returnsTrue_ifFileHasManyChunks()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertTrue($chunker->hasChunks());
		
		return;
	}
	
	
	/* !hasNextChunk() */
	
	/**
	 * hasNextChunk() should return false if a next chunk does not exist
	 */
	public function testHasNextChunk_returnsFalse_ifNextChunkDoesNotExist()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setIndex(10);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertFalse($chunker->hasNextChunk());
		
		return;
	}
	
	/**
	 * hasNextChunk() should return true if a next chunk does exist
	 */
	public function testHasNextChunk_returnsTrue_ifNextChunkDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertTrue($chunker->hasNextChunk());
		
		return;
	}
	
	
	/* !hasPreviousChunk() */
	
	/**
	 * hasPreviousChunk() should return false if a previous chunk does not exist
	 */
	public function testHasPreviousChunk_returnsFalse_ifPreviousChunkDoesNotExist()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setIndex(0);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertFalse($chunker->hasPreviousChunk());
		
		return;
	}
	
	/**
	 * hasPreviousChunk() should return true if a previous chunk does exist
	 */
	public function testHasPreviousChunk_returnsTrue_ifPreviousChunkDoesExist()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setName($this->fileHasSbCharacters);
		
		$this->assertTrue($chunker->hasPreviousChunk());
		
		return;
	}
	
	
	/* !reset() */
	
	/**
	 * reset() should reset the chunker's index
	 */
	public function testReset()
	{
		$chunker = new File();
		
		$chunker->setSize(1);
		$chunker->setName($this->fileHasSbCharacters);
		
		$chunker->getNextChunk();
		
		$this->assertEquals(1, $chunker->getIndex());
		
		$chunker->reset();
		
		$this->assertEquals(0, $chunker->getIndex());
		
		return;
	}
	
	
	/* !setIndex() */
	
	/**
	 * setIndex() throws InvalidArgumentException if $index is not an integer
	 */
	public function testSetIndex_throwsInvalidArgumentException_ifIndexIsNotAnInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File();
		$chunker->setIndex('foo');
		
		return;
	}
	
	/**
	 * setIndex() should throw an InvalidArgumentException if $index is a negative
	 *     integer
	 */
	public function testSetIndex_throwsInvalidArgumentException_ifIndexIsNegativeInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File();
		$chunker->setIndex(-1);
		
		return;
	}
	
	/**
	 * setIndex() should return self if $index is a positive integer or zero
	 */
	public function testSetIndex_returnsSelf_ifIndexIsPositiveIntegerOrZero()
	{
		$chunker = new File();
		
		$this->assertSame($chunker, $chunker->setIndex(1));
		
		return;
	}
	
	
	/* !setSize() */
	
	/**
	 * setSize() should throw InvalidArgumentException if $size is not an integer
	 */
	public function testSetSize_throwsInvalidArgumentException_ifSizeIsNotAnInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File();
		$chunker->setSize('foo');
		
		return;
	}
	
	/**
	 * setSize() should throw InvalidArgumentException if $size is not a positive integer
	 */
	public function testSetSize_throwsInvalidArgumentException_ifSizeIsNotAPositiveInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File();
		$chunker->setSize(-1);
		
		return;
	}
	
	/**
	 * setSize() should return self if $size is a positive integer
	 */
	public function testSetSize_returnsSelf_ifSizeIsAPositiveInteger()
	{
		$chunker = new File();
		
		$this->assertSame($chunker, $chunker->setSize(1));
		
		return;
	}
	
	
	/* !setName() */
	
	/**
	 * setName() should throw InvalidArgumentException if $name is not a string
	 */
	public function testSetName_throwsInvalidArgumentException_ifNameIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File();
		$chunker->setName(1);
		
		return;
	}
	
	/**
	 * setName() should throw InvalidArgumentException if $name is not readable
	 */
	public function testSetName_throwsInvalidArgumentException_ifNameIsNotReadable()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new File();
		$chunker->setName('foo');
		
		return;
	}
	
	/**
	 * setName() should return self if $name is a readable file name
	 */
	public function testSetName_returnsSelf_ifNameIsReadable()
	{
		$chunker = new File();
		
		$this->assertSame($chunker, $chunker->setName($this->fileIsEmpty));
		
		return;
	}
}
