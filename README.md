# Chunker
Chunk a large file or string with PHP (multi-byte safe).

```php
use Jstewmc\Chunker;

// create an example file with the mixed-byte string "from $ to €"
// keep in mind, every character in the string "from $ to " is one-byte in UTF-8; 
//     however, the euro symbol, "€", is a three-byte character in UTF-8
//
file_put_contents('example.txt', "from $ to €");

// create a file chunker
$chunker = new Chunker\File('example.txt');

// for this example, give the chunker a chunk size of four bytes
// obviously, this wouldn't be worth very much in the real world!
//
$chunker->setSize(4);

// loop through the file's chunks
while (false !== ($chunk = $chunker->getCurrentChunk())) {
	// echo the chunk's contents
	echo "\"$chunk\"";
	// advance to the next chunk
	$chunker->getNextChunk();	
}
```

The example above would produce the following output:

```
"from"
" $ t"
"o "
"€"
```

Note, the third chunk is only two one-byte characters. The fourth byte in the third chunk fell in the middle of the three-byte euro symbol. As a result, the third chunk was shortened. 

## About

Most of PHP's file functions like `file_get_contents()`, `fgetc()`, and `fread()` still assume that one byte is one character. In a multi-byte encoding like [UTF-8](https://en.wikipedia.org/wiki/UTF-8), that assupmtion is no longer valid. `file_get_contents()` could return a valid string from a file just as easily as it could split a multi-byte character in two and trail a malformed byte sequence.

This library was built to chunk a very large file or very large string in a multi-byte safe way.

## Files

You can use the constructor or setter to set a File chunker's filename:

```php
use Jstewmc\Chunker;

// use the constructor
$chunker = new Chunker\File('path/to/file');

// use the setter
$chunker = new Chunker\File();
$chunker->setName('path/to/file');
```

## Strings

You can use the constructor or setter to set a Text chunker's string:

```php
use Jstewmc\Chunker;

// use the constructor
$chunker = new Chunker\Text('foo bar baz');

// use the setter
$chunker = new Chunker\Text();
$chunker->setText('foo bar baz');
```

## Encoding

Both the File and Text chunkers accept an optional second argument in their constructor (and provide a setter) for character encoding. 

Encoding can be one of the following three values:

- a [character encoding](http://php.net/manual/en/function.mb-list-encodings.php) from PHP's [Multi-byte string library](http://php.net/manual/en/ref.mbstring.php),
- the special string `"auto"` (and the encoding will be detected with [`mb_detect_encoding()`](http://php.net/manual/en/function.mb-detect-encoding.php)), or 
- `null` (and the encoding will be retrieved from [`mb_internal_encoding()`](http://php.net/manual/en/function.mb-internal-encoding.php).)

For example:

```php
use Jstewmc\Chunker;

// set encoding via the constructor
$chunker = new Chunker\Text('foo bar baz', 'UTF-8');

// detect the encoding automatically
$chunker = new Chunker\Text('foo bar baz', 'auto');

// retrieve the encoding from mb_internal_encoding()
$chunker = new Chunker\Text('foo bar baz', null);
```

## Size

The File chunker defaults to chunks of 8,192 *bytes*, and the Text chunker defaults to chunks of 2,000 *characters*. But, you can set the chunker's chunk size with the `setSize()` method. Just keep in mind, the `size` property of a File chunker is in bytes, and the `size` property of a Text chunker is in characters:

```php
use Jstewmc\Chunker;

$chunker = new Chunker\File();
$chunker->setSize(8192);  // 8192 bytes (i.e., 8 kilobytes)

$chunker = new Chunker\Text();
$chunker->setSize(8192);  // 8192 characters (i.e, 8 - 32 kilobytes) !!
```

## Usage

Once your chunker has been constructed, you can get the current, next, or previous chunk with the `getCurrentChunk()`, `getNextChunk()`, and `getPreviousChunk()` methods, respectively. If a chunk does not exist, the methods will return false. 

For conveniece, the methods are aliased as `current()`, `next()`, and `previous()`, respectively.

```php
use Jstewmc\Chunker;

$chunker = Chunker\Text('foo');
$chunker->setSize(3);  // three characters

$chunker->current();   // returns "foo"
$chunker->next();      // returns false
$chunker->previous();  // returns "foo"
```

You can count the total chunks in a file or string:

```php
use Jstewmc\Chunker;

$chunker = Chunker\Text('foo bar baz');
$chunker->setSize(3);

$chunker->getMaxChunks();  // returns 4 (total is always rounded up!)
```

You can check if the file or string has *one* chunk with `hasChunk()` or has *one or more* chunks with `hasChunks()`:

```php
use Jstewmc\Chunker;

$chunker = Chunker\Text();
$chunker->setSize(3);

$chunker->setText('foo');

$chunker->hasChunk();   // returns true
$chunker->hasChunks();  // returns true

$chunker->setText('foo bar');

$chunker->hasChunk();   // returns false
$chunker->hasChunks();  // returns true
```

You can check if the file or text has a *previous* chunk with `hasPreviousChunk()` or has a *next* chunk with `hasNextChunk()`:

```php
use Jstewmc\Chunker;

$chunker = Chunker\Text('foo bar');
$chunker->setSize(3);

$chunker->hasPreviousChunk();  // returns false
$chunker->hasNextChunk();      // returns true

$chunker->next();

$chunker->hasPreviousChunk();  // returns true
$chunker->hasNextChunk();      // returns true

$chunker->next();

$chunker->hasPreviousChunk();  // returns true
$chunker->hasNextChunk();      // returns false
```

Finally, you can reset the chunk's internal pointer with `reset()`:

```php
use Jstewmc\Chunker;

$chunker = Chunker\Text('foo bar');
$chunker->setSize(3);

$chunker->current();  // returns "foo"

$chunker->next();     // returns " ba"
$chunker->current();  // returns " ba"

$chunker->reset();    

$chunker->current();  // returns "foo"
```

## Author

Jack Clayton - [clayjs0@gmail.com](mailto:clayjs0@gmail.com)

## License

This library is released under the [MIT license](https://github.com/jstewmc/chunker/blob/master/LICENSE).

## Version

0.1.0
