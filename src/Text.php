<?php
/**
 * The Jstewmc\Chunker\Text class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Chunker;

/**
 * The Text chunker class
 *
 * @since  0.1.0
 */
class Text extends Chunker
{
	/* !Protected properties */
	
	/**
	 * @var  int  the (maximum) size of the chunk in characters; defaults to 2000
	 * @since  0.1.0
	 */
	protected $size = 2000;
	
	/**
	 * @var  string  the text chunker's text to chunk
	 * @since  0.1.0
	 */
	protected $text;	
	
	
	/* !Get methods */
	
	/**
	 * Returns the chunker's text
	 *
	 * @return  string|null
	 * @since   0.1.0
	 */
	public function getText()
	{
		return $this->text;
	}
	
	
	/* !Set methods */
	
	/**
	 * Sets the chunker's text
	 *
	 * @param  string  $text  the chunker's text
	 * @return  self  
	 * @since  0.1.0
	 */
	public function setText($text)
	{
		if ( ! is_string($text)) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, text, to be a string"
			);
		}
		
		$this->text = $text;
		
		return $this;
	}
	
	
	/* !Magic methods */
	
	/**
	 * Called to construct the object
	 *
	 * @param  string  $text  the text to chunk (optional; if omitted, defaults to 
	 *     null)
	 * @param  string  $encoding  the text's character encoding (optional; if
	 *      omitted, defaults to mb_internal_encoding())
	 * @return  self
	 * @throws  InvalidArgumentException  if $text is neither null nor a string
	 * @throws  InvalidArgumentException  if $encoding is neither null nor a valid
	 *     character encoding
	 * @since   0.1.0
	 */
	public function __construct($text = null, $encoding = null)
	{
		if ($text !== null) {
			$this->setText($text);
		}
		
		parent::__construct($encoding);
		
		return;
	}
	
	
	/* !Public methods */
	
	/**
	 * Returns the maximum number of chunks in the text
	 *
	 * @return  int
	 * @since  0.1.0
	 */
	public function getMaxChunks()
	{
		return ceil(mb_strlen((string) $this->text, $this->encoding) / $this->size);
	}
	 
	
	/* !Protected methods */
	
	/**
	 * Returns a string chunk or false if chunk does not exist
	 *
	 * @param  int  $offset  the chunk's offset
	 * @return  string|false
	 * @throws  InvalidArgumentException  if $offset is not a positive int or zero
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
		
		$chunk = false;
		
		$text = (string) $this->text;
		
		if ($offset < mb_strlen($text)) {
			$chunk = mb_substr($text, $offset, $this->size, $this->encoding);
		} 
		
		return $chunk;
	}
}
