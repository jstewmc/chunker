<?php
/**
 * The Jstewmc\Chunker\File class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */
 
namespace Jstewmc\Chunker;

/**
 * The File chunker class 
 *
 * The File chunker class allows you to chunk the contents of a very large text file
 * in a multi-byte safe manner.
 *
 * @since  0.1.0
 */
class File extends Chunker
{
	/* !Constants */
	
	/**
	 * @var  int  the maximum size in bytes of a character in *any* multi-byte 
	 *     encoding; it seems like UTF-32, at four-bytes per character and every 
	 *     character ever, is the sensible max
	 */
	const MAX_SIZE_CHARACTER = 4;
	
	
	/* !Protected properties */
	
	/**
	 * @var  int  the chunk size in bytes; defaults to 8192 bytes (i.e., 8 * 1024 
	 *     bytes)
	 * @since  0.1.0
	 */
	protected $size = 8192;
	
	/**
	 * @var  string  the file's name
	 * @since  0.1.0
	 */
	protected $name;	
	
	
	/* !Get methods */
	
	/**
	 * Returns the name of the file
	 *
	 * @return  string|null
	 * @since  0.1.0
	 */
	public function getName()
	{
		return $this->name;
	}
	
	
	/* !Set methods */
	
	/**
	 * Sets the file's name
	 *
	 * @param  string  $name  the file's name
	 * @return  self
	 * @throws  InvalidArgumentException  if $name is not a string
	 * @throws  InvalidArgumentException  if $name is not a readable file name
	 * @since  0.1.0
	 */
	public function setName($name)
	{
		if ( ! is_string($name)) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, name, to be a string"
			);	
		}
		
		if ( ! is_readable($name)) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, name, to be a readable filename"
			);
		}
		
		$this->name = $name;
		
		return $this;
	}

	
	/* !Magic methods */
	
	/**
	 * Called when the chunker is constructed
	 *
	 * @param  string  $name  the file's name (optional; if omitted, defaults to 
	 *     null)
	 * @param  string  $encoding  the text's character encoding (optional; if 
	 *     omitted, defaults to mb_internal_encoding())
	 * @return  self
	 * @throws  InvalidArgumentException  if $name is neither null nor a readable 
	 *     file name
	 * @throws  InvalidArgumentException  if $encoding is neither null nor a valid
	 *     character encoding
	 * @since  0.1.0
	 */
	public function __construct($name = null, $encoding = null) 
	{
		if ($name !== null) {
			$this->setName($name);
		}
		
		parent::__construct($encoding);
		
		return;
	}

	
	/* !Public methods */
	
	/**
	 * Returns the maximum number of chunks in the file
	 *
	 * @return  int
	 * @throws  BadMethodCallException  if $name property is null
	 * @since  0.1.0
	 */
	public function getMaxChunks()
	{
		return ceil(($this->name !== null ? filesize($this->name) : 0) / $this->size);
	}
	
	
	/* !Protected methods */
	
	/**
	 * Returns a multi-byte-safe file chunk or false
	 *
	 * The bad news is that there is no multi-byte file read function in PHP! The 
	 * good news is that even though PHP assumes each byte is a character (wrong!)
	 * it doesn't make any changes.
	 *
	 * When I chunk a string of multi-byte characters by byte instead of character, 
	 * obviously, it's possible that the current chunk will end in the middle of a 
	 * multi-byte character. If it does, the last character in the current chunk and 
	 * the first character in the next chunk will be malformed byte sequences (aka, 
	 * "?" characters).
	 *
	 * For example (if the string has three four-byte characters, the chunk's offset
	 * is 0, and the chunk's size is 10 bytes):
	 *
	 *     string: AAAABBBBCCCC
	 *     chunk:  ----------
	 * 
	 * The chunk above would result in "AB?" because the chunk ends two-bytes into 
	 * the four-byte "C" character.
	 *
	 * To prevent malformed byte sequences, I'll pad the chunk's offset and length by 
	 * the maximum number of bytes in a single character in any multi-byte encoding. 
	 * That way, no matter what the encoding is, the chunk is always padded with a 
	 * well-formed byte sequence.
	 *
	 * For example (continued from above):
	 *     
	 *     string: AAAABBBBCCCC
	 *     chunk:  --------------
	 *
	 * The chunk above would result in "ABC" because we've captured all four bytes
	 * of the four-byte "C" character, which is great. But, we've only kicked the
	 * can down the road. Our chunk could just as easily still end in two-bytes of a
	 * four-byte character. 
	 *
	 * Enter PHP's mb_strcut() function. The mb_strcut() function will cut a string 
	 * of multi-byte characters based on an offset and length in bytes. If the cut 
	 * begins in the middle of a multi-byte character, mb_strcut() will move the 
	 * cut's offset to the left, and if the cut ends in the middle of a multi-byte 
	 * character, it'll shorten the cut's length to exclude the character.
	 *
	 * For example (continued from above):
	 *
	 *     string: AAAABBBBCCCC
	 *     chunk:  --------------
	 *     strcut: ------------
	 *
	 * That's it! With the padding and the mb_strcut() function, we have a multi-byte
	 * safe file_get_contents()!
	 *
	 * @param  int  $offset  the chunk's offset
	 * @return  string|false
	 * @throws  InvalidArgumentException  if $offset is not an integer
	 * @since  0.1.0
	 */
	protected function getChunk($offset)
	{
		if ( ! is_numeric($offset) || ! is_int(+$offset) || $offset < 0) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, offset, to be a positive "
					. "integer or zero"
			);
		}
	
		if ($this->name !== null) {
			// get the single-byte chunk...
			// keep in mind, if file_get_contents() encounters an invalid offset, it 
			//     will return false AND raise an E_WARNING; we don't want the 
			//     E_WARNING, only the false
			// also, make sure you floor the offset to 0; negative values will start
			//     that many bytes from the end of the file
			//
			$sbChunk = @file_get_contents(
				$this->name, 
				false, 
				null, 
				max(0, $offset - self::MAX_SIZE_CHARACTER), 
				$this->size + self::MAX_SIZE_CHARACTER
			);
			if ($sbChunk !== false) {
				$mbChunk = mb_strcut(
					$sbChunk, 
					min(max(0, $offset), self::MAX_SIZE_CHARACTER), 
					$this->size, 
					$this->encoding
				);	
			} else {
				// otherwise, a chunk does not exist
				$mbChunk = false;
			}
		} else {
			// otherwise, name was empty
			$mbChunk = false;
		}
		
		return $mbChunk;
	}
}
