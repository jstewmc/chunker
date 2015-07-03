<?php
/**
 * The TextTest class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Chunker;

/**
 * A test suite for the Text class
 *
 * @since  0.1.0
 */
class TextTest extends \PHPUnit_Framework_TestCase
{
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
	
	
	/* !getEncoding() */
	
	/**
	 * getEncoding() should return the chunker's encoding
	 */
	public function testSetEncoding_returnsEncoding()
	{
		$chunker = new Text();
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
		$chunker = new Text();
		$chunker->setIndex(1);
		
		$this->assertEquals(1, $chunker->getIndex());
		
		return;
	}
	
	
	/* !getSize() */
	
	/**
	 * getSize() should return the chunker's size
	 */
	public function testSetSize_returnsSize()
	{
		$chunker = new Text();
		$chunker->setSize(1);
		
		$this->assertEquals(1, $chunker->getSize());
		
		return;
	}
	
	
	/* !getText() */
	
	/**
	 * getText() should return the chunker's text
	 */
	public function testGetText_returnsText()
	{
		$chunker = new Text();
		$chunker->setText('foo');
		
		$this->assertEquals('foo', $chunker->getText());
		
		return;
	}

	
	/* !__construct() */

	/**
	 * __construct() should throw an InvalidArgumentException if $text is not a string
	 */
	public function testConstruct_throwsInvalidArgumentException_ifTextIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new Text(1);
		
		return;
	}
	
	/**
	 * __construct() should throw an InvalidArgumentException if $encoding is not a string
	 */
	public function testConstruct_throwsInvalidArgumentException_ifEncodingIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new Text('foo', 1);
		
		return;
	}
	
	/**
	 * __construct() should throw an InvalidArgumentException if $encoding is not a valid encoding
	 */
	public function testConstruct_throwsInvalidArgumentException_ifEncodingIsNotSupported()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new Text('foo', 'foo');
		
		return;
	}
	
	/**
	 * __construct() should return text if $text exists
	 */
	public function testConstruct_returnsObject_ifTextDoesExist()
	{
		$chunker = new Text('foo');
		
		$this->assertEquals('foo', $chunker->getText());
		
		return;
	}
	
	/**
	 * __construct() should return text if $encoding exists
	 */
	public function testConstruct_returnsObject_ifTextAndEncodingDoesExist()
	{
		$chunker = new Text('foo', 'UTF-8');
		
		$this->assertEquals('foo', $chunker->getText());
		$this->assertEquals('UTF-8', $chunker->getEncoding());
		
		return;
	}
	
	/**
	 * __construct() should return object if $text does not exist
	 */
	public function testConstruct_returnsObject_ifTextDoesNotExist()
	{
		$chunker = new Text();
		
		$this->assertNull($chunker->getText());
		
		return;
	}
	
	
	/* !getCurrentChunk() */
	
	/**
	 * getCurrentChunk() should return false if text does not exist
	 */
	public function testGetCurrentChunk_returnsFalse_ifTextDoesNotExist()
	{
		$chunker = new Text();
		
		$this->assertFalse($chunker->getCurrentChunk());
		
		return;
	}
	
	/**
	 * getCurrentChunk() should return string if single-byte text does exist
	 */
	public function testGetCurrentChunk_returnsString_ifSbTextDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setText('foo');
		
		$this->assertEquals('f', $chunker->getCurrentChunk());
		
		return;
	}
	
	/**
	 * getCurrentChunk() should return string if multi-byte text does exist
	 */
	public function testGetCurrentChunk_returnsString_ifMbTextDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setText(self::cent());
		
		$this->assertEquals(self::cent(), $chunker->getCurrentChunk());
		
		return;
	}
	
	
	/* !getMaxChunks() */
	
	/**
	 * getMaxChunks() should return int if text does not exist
	 */
	public function testGetMaxChunks_returnsInt_ifTextDoesNotExist()
	{
		$chunker = new Text();
		
		$this->assertEquals(0, $chunker->getMaxChunks());
		
		return;
	}
	
	/**
	 * getMaxChunks() should return int if single-byte text does exist
	 */
	public function testGetMaxChunks_returnsInt_ifSbTextDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setText('foo');
		
		$this->assertEquals(3, $chunker->getMaxChunks());
		
		return;
	}
	
	/**
	 * getMaxChunks() should return int if multi-byte text does exist
	 */
	public function testGetMaxChunks_returnsInt_ifMbTextDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setText(self::cent().' '.self::euro());
		
		$this->assertEquals(3, $chunker->getMaxChunks());
		
		return;
	}

	/* !getNextChunk() */
	
	/**
	 * getNextChunk() should return false if next chunk does not exist
	 */
	public function testGetNextChunk_returnsFalse_ifNextChunkDoesNotExist()
	{
		$chunker = new Text();
		
		$this->assertFalse($chunker->getNextChunk());
		
		return;
	}
	
	/**
	 * getNextChunk() should return string if single-byte next chunk does exist
	 */
	public function testGetNextChunk_returnsString_ifNextChunkSbDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setText('foo');
		
		$this->assertEquals('o', $chunker->getNextChunk());
		
		return;
	}
	
	/**
	 * getNextChunk() should return string if multi-byte next chunk does exist
	 */
	public function testGetNextChunk_returnsString_ifNextChunkMbDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setText(self::cent().self::euro());
		
		$this->assertEquals(self::euro(), $chunker->getNextChunk());
		
		return;
	}
	
	
	/* !getPreviousChunk() */
	
	/**
	 * getPreviousChunk() should return false if previous chunk does not exist
	 */
	public function testGetPreviousChunk_returnsFalse_ifPreviousChunkDoesNotExist()
	{
		$chunker = new Text();
		
		$this->assertFalse($chunker->getPreviousChunk());
		
		return;
	}
	
	/**
	 * getPreviousChunk() should return false if previous single-byte chunk does exist
	 */
	public function testGetPreviousChunk_returnsFalse_ifPreviousChunkSbDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setText('foo');
		
		$this->assertEquals('f', $chunker->getPreviousChunk());
		
		return;
	}
	
	/**
	 * getPreviousChunk() should return false if previous multi-byte chunk does exist
	 */
	public function testGetPreviousChunk_returnsFalse_ifPreviousChunkMbDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setText(self::cent().self::euro());
		
		$this->assertEquals(self::cent(), $chunker->getPreviousChunk());
		
		return;
	}
	
	/* !hasChunk() */
	
	/**
	 * hasChunk() should return false if the text does not exist
	 */
	public function testHasChunk_returnsFalse_ifTextHasZeroChunks()
	{
		$chunker = new Text();
		
		$this->assertFalse($chunker->hasChunk());
		
		return;
	}
	
	/**
	 * hasChunk() should return true if the text has one chunk
	 */
	public function testHasChunk_returnsTrue_ifTextHasOneChunk()
	{
		$chunker = new Text();
		
		$chunker->setSize(3);
		$chunker->setText('foo');
		
		$this->assertTrue($chunker->hasChunk());
		
		return;
	}
	
	/**
	 * hasChunk() should return false if the text has many chunks
	 */
	public function testHasChunk_returnsFalse_ifTextHasManyChunks()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setText('foo');
		
		$this->assertFalse($chunker->hasChunk());
		
		return;
	}
	
	
	/* !hasChunks() */
	
	/**
	 * hasChunks() should return false if the text has zero chunks
	 */
	public function testHasChunks_returnsFalse_ifTextHasZeroChunks()
	{
		$chunker = new Text();
		
		$this->assertFalse($chunker->hasChunks());
		
		return;
	}
	
	/**
	 * hasChunks() should return true if the text has one chunk
	 */
	public function testHasChunks_returnsTrue_ifTextHasOneChunk()
	{
		$chunker = new Text();
		
		$chunker->setSize(3);
		$chunker->setText('foo');
		
		$this->assertTrue($chunker->hasChunks());
		
		return;
	}
	
	/**
	 * hasChunks() should return true if the text has many chunks
	 */
	public function testHasChunks_returnsTrue_ifTextHasManyChunks()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setText('foo');
		
		$this->assertTrue($chunker->hasChunks());
		
		return;
	}
	
	
	/* !hasNextChunk() */
	
	/**
	 * hasNextChunk() should return false if a next chunk does not exist
	 */
	public function testHasNextChunk_returnsFalse_ifNextChunkDoesNotExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setIndex(2);
		$chunker->setText('foo');
		
		$this->assertFalse($chunker->hasNextChunk());
		
		return;
	}
	
	/**
	 * hasNextChunk() should return true if a next chunk does exist
	 */
	public function testHasNextChunk_returnsTrue_ifNextChunkDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setText('foo');
		
		$this->assertTrue($chunker->hasNextChunk());
		
		return;
	}
	
	
	/* !hasPreviousChunk() */
	
	/**
	 * hasPreviousChunk() should return false if a previous chunk does not exist
	 */
	public function testHasPreviousChunk_returnsFalse_ifPreviousChunkDoesNotExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setIndex(0);
		$chunker->setText('foo');
		
		$this->assertFalse($chunker->hasPreviousChunk());
		
		return;
	}
	
	/**
	 * hasPreviousChunk() should return true if a previous chunk does exist
	 */
	public function testHasPreviousChunk_returnsTrue_ifPreviousChunkDoesExist()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		$chunker->setText('foo');
		
		$this->assertTrue($chunker->hasPreviousChunk());
		
		return;
	}
	
	
	/* !reset() */
	
	/**
	 * reset() should reset the chunker's index
	 */
	public function testReset()
	{
		$chunker = new Text();
		
		$chunker->setSize(1);
		$chunker->setText('foo');
		
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
		
		$chunker = new Text();
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
		
		$chunker = new Text();
		$chunker->setIndex(-1);
		
		return;
	}
	
	/**
	 * setIndex() should return self if $index is a positive integer or zero
	 */
	public function testSetIndex_returnsSelf_ifIndexIsPositiveIntegerOrZero()
	{
		$chunker = new Text();
		
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
		
		$chunker = new Text();
		$chunker->setSize('foo');
		
		return;
	}
	
	/**
	 * setSize() should throw InvalidArgumentException if $size is not a positive integer
	 */
	public function testSetSize_throwsInvalidArgumentException_ifSizeIsNotAPositiveInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new Text();
		$chunker->setSize(-1);
		
		return;
	}
	
	/**
	 * setSize() should return self if $size is a positive integer
	 */
	public function testSetSize_returnsSelf_ifSizeIsAPositiveInteger()
	{
		$chunker = new Text();
		
		$this->assertSame($chunker, $chunker->setSize(1));
		
		return;
	}
	
	
	/* !setText() */
	
	/**
	 * setText() should throw InvalidArgumentException if $text is not a string
	 */
	public function testSetText_throwsInvalidArgumentException_ifTextIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$chunker = new Text();
		$chunker->setText(1);
		
		return;
	}
	
	/**
	 * setText() should return self if text is a string
	 */
	public function testSetText_returnsSelf_ifTextIsAString()
	{
		$chunker = new Text();
		
		$this->assertSame($chunker, $chunker->setText('foo'));
		$this->assertEquals('foo', $chunker->getText());
		
		return;
	}
}
