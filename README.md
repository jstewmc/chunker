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

A file or string's character encoding is set *explicitly* or *implicitly* when the Chunker is constructed.

The character encoding can be set explicitly via the optional second argument in the constructor. An encoding should be a valid [character encoding](http://php.net/manual/en/function.mb-list-encodings.php) from PHP's [Multi-byte string library](http://php.net/manual/en/ref.mbstring.php).

If a character encoding is not passed to the constructer, the Chunker's encoding will be set implicitly to the encoding returned by [`mb_internal_encoding()`](http://php.net/manual/en/function.mb-internal-encoding.php).

If, after construction, you'd like to set the chunker's encoding, you can use the `setEncoding()` method:

```php
use Jstewmc\Chunker;

// set the encoding explicitly
$chunker = new Chunker\Text('foo bar baz', 'UTF-8');

// set the encoding implicitly
$chunker = new Chunker\Text('foo bar baz');

// set the string of file's after construction
$chunker->setEncoding('UTF-8');
```

## Size

The File chunker defaults to a chunk size of 8,192 *bytes*, and the Text chunker defaults to a chunk size of 2,000 *characters*. 

However, you can set the chunker's chunk size with the `setSize()` method. Just remember, the `size` property of a File chunker is in *bytes*, and the `size` property of a Text chunker is in *characters*:

```php
use Jstewmc\Chunker;

$chunker = new Chunker\File();
$chunker->setSize(8192);  // 8192 bytes (i.e., 8 kilobytes)

$chunker = new Chunker\Text();
$chunker->setSize(8192);  // yikes! that's 8192 characters (i.e, up to 32 kilobytes)
$chunker->setSize(2000);  // that's 2000 characters (i.e., up to 8 kilobytes)
```

## Usage

Once your chunker has been constructed, you can get the chunker's current, next, or previous chunk with the `getCurrentChunk()`, `getNextChunk()`, and `getPreviousChunk()` methods, respectively. For conveniece, the methods are aliased as `current()`, `next()`, and `previous()`, respectively. If a chunk does not exist, the methods will return false.

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
