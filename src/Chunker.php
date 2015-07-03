<?php
/**
 * The Jstewmc\Chunker class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Chunker;

/**
 * The Chunker class
 *
 * The Chunker class allows you to chunk the contents of very large text files and 
 * very large strings in a multi-byte safe manner.
 *
 * @since  0.1.0
 */
abstract class Chunker
{
	/* !Protected properties */
	
	/**
	 * @var  string|null  the chunker's character encoding
	 * @since  0.1.0
	 */
	protected $encoding;
	
	/**
	 * @var  int  the chunker's current chunk index; defaults to 0
	 * @since  0.1.0
	 */
	protected $index = 0;
	
	/**
	 * @var  int  the chunker's chunk size; heads up! this is the size of a chunk in
	 *    *bytes* for a file chunk and *characters* for a text chunk
	 */
	protected $size;
	
	
	/* !Get methods */

	/**
	 * Returns the chunker's character-set encoding
	 *
	 * @return  string|null
	 * @since  0.1.0
	 */
	public function getEncoding()
	{
		return $this->encoding;
	}
	
	/**
	 * Returns the chunker's current chunk index
	 *
	 * @return  int
	 * @since  0.1.0
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
	/**
	 * Returns the chunker's chunk size
	 *
	 * @return  int
	 * @since  0.1.0
	 */
	public function getSize()
	{
		return $this->size;
	}
	
	
	/* !Set methods */
	
	/**
	 * Sets the chunker's character encoding
	 *
	 * @param  string|null  $encoding  the chunker's character encoding; the string
	 *     'auto' to detect encoding; or null to use mb_internal_encoding()
	 * @returne  self
	 * @throws  InvalidArgumentException  if $encoding is not an supported charset;
	 *     the special string 'auto'; or, null
	 * @since  0.1.0
	 */
	public function setEncoding($encoding = null)
	{
		if ( 
			! (is_string($encoding) && in_array($encoding, mb_list_encodings()))
			&& ! (is_string($encoding) && $encoding === 'auto')
			&& $encoding !== null
		) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, encoding, to be a valid string "
				 . "character encoding name; the special string 'auto'; or, null"
			);
		}
		
		if ($encoding === 'auto') {
			$encoding = $this->detectEncoding();
		}
		
		$this->encoding = $encoding;
		
		return $this;
	}
	
	/**
	 * Sets the chunker's chunk index
	 *
	 * @param  int  $index  the chunker's chunk index
	 * @return  self
	 * @throws  InvalidArgumentException  if $index is not a positive int or zero
	 * @since  0.1.0
	 */
	public function setIndex($index)
	{
		if ( ! is_numeric($index) || ! is_int(+$index) || $index < 0) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, index, to be a positive "
				 ."integer or zero"
			);
		}
		
		$this->index = $index;
		
		return $this;
	}
	
	/**
	 * Sets the chunker's chunk size
	 *
	 * Heads up! This property is a size in *bytes* for file chunks and a size in 
	 * *characters* for text chunks.
	 *
	 * @param  int  $size  the chunker's chunk size
	 * @return self
	 * @throws  InvalidArgumentException  if $size is not a positive integer
	 * @since  0.1.0
	 */
	public function setSize($size)
	{
		if ( ! is_numeric($size) || ! is_int(+$size) || $size < 1) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, size, to be a positive integer"
			);	
		}
		
		$this->size = $size;
		
		return $this;
	}
	
	
	/* !Magic methods */
	
	/**
	 * Called when the object is constructed
	 *
	 * @param  string  $encoding  the chunker's character encoding (possible values
	 *     are a string character encoding name; the special string 'auto' to 
	 *     detect the string's encoding; or, null, to use mb_internal_encoding())
	 * @return  self
	 * @throws  InvalidArgumentException  if $encoding is neither null nor a valid
	 *     mb-supported character encoding
	 * @since  0.1.0
	 */
	public function __construct($encoding = null) 
	{
		if ($encoding !== null) {
			$this->setEncoding($encoding);
		}
		
		return;	
	}
	
	
	/* !Public methods */
	
	/**
	 * Alias for the getCurrentChunk() method
	 *
	 * @return  string|false
	 * @since  0.1.0
	 */
	public function current()
	{
		return $this->getCurrentChunk();
	}
	
	/**
	 * Returns the current chunk
	 *
	 * @return  string|false
	 * @since  0.1.0
	 */
	public function getCurrentChunk()
	{
		return $this->getChunk($this->index * $this->size);
	}
	
	/**
	 * Returns the maximum number of chunks in the file or text
	 *
	 * @return  int
	 * @since  0.1.0
	 */
	abstract public function getMaxChunks();
	
	/**
	 * Returns the next chunk and updates the chunker's internal pointer
	 *
	 * @return  int
	 * @since  0.1.0
	 */
	public function getNextChunk()
	{
		return $this->getChunk(++$this->index * $this->size);
	}
	
	/**
	 * Returns the previous chunk and updates the chunker's internal pointer
	 *
	 * @return  int
	 * @since  0.1.0
	 */
	public function getPreviousChunk()
	{
		$offset = --$this->index * $this->size;
		
		if ($offset >= 0) {
			$chunk = $this->getChunk($offset);
		} else {
			$chunk = false;
		}
		
		return $chunk;
	}
	
	/**
	 * Returns true if the file or text has *one* chunk
	 *
	 * @return  bool
	 * @since  0.1.0
	 */
	public function hasChunk()
	{
		return $this->getMaxChunks() == 1;
	}
	
	/**
	 * Returns true if the file or text has *one or more* chunks
	 *
	 * @return  bool
	 * @since  0.1.0
	 */
	public function hasChunks()
	{
		return $this->getMaxChunks() > 0;
	}
	
	/**
	 * Returns true if a next chunk exists
	 *
	 * @return  bool
	 * @since  0.1.0
	 */
	public function hasNextChunk()
	{
		return $this->index + 1 < $this->getMaxChunks();
	}
	
	/**
	 * Returns true if a previous chunk exists
	 *
	 * @return  bool
	 * @since  0.1.0
	 */
	public function hasPreviousChunk()
	{
		return $this->index - 1 >= 0;
	}
	
	/**
	 * Alias for the getNextChunk() method
	 *
	 * @return  string|false
	 * @since  0.1.0
	 */
	public function next()
	{
		return $this->getNextChunk();
	}
	
	/**
	 * Alias for the getPreviousChunk() method
	 *
	 * @return  string|false
	 * @since  0.1.0
	 */
	public function previous()
	{
		return $this->getPreviousChunk();
	}
	
	/**
	 * Resets the chunker
	 *
	 * @return  void
	 * @since  0.1.0
	 */
	public function reset()
	{
		$this->index = 0;
		
		return;
	}
	
	
	/* !Protected methods */
	
	/**
	 * Detects the file or string's character encoding
	 *
	 * @return  string|null
	 */
	abstract protected function detectEncoding();
	
	/**
	 * Gets a chunk starting at $offset
	 *
	 * @param  int  $offset
	 * @return  string|false
	 */
	abstract protected function getChunk($offset);
}
