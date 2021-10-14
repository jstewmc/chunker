[![CircleCI](https://circleci.com/gh/jstewmc/chunker.svg?style=svg)](https://circleci.com/gh/jstewmc/chunker) [![codecov](https://codecov.io/gh/jstewmc/chunker/branch/master/graph/badge.svg?token=6XDlEBPQ2i)](https://codecov.io/gh/jstewmc/chunker)

# Chunker

_A multi-byte-safe stream for reading very large files (or strings) as sequential chunks with PHP._

Breaking very large files or string into chunks and reading them one-at-a-time (aka, "chunking") can reduce memory consumption.

Unfortunately, chunking multi-byte-encoded files, like those using `UTF-8`, can result in broken multi-byte characters. PHP's file functions, like `file_get_contents()`, use limits in bytes. When the limit falls in the middle of a multi-type character, a malformed byte sequence, represented by the `"?"` character, will result:

```php
// Create an example file with multi-byte characters (characters in the string
// "from $ to " are one-byte in UTF-8, while the euro symbol, "€", is a three-
// byte character in UTF-8)
file_put_contents('example.txt', 'from $ to €');

// Read 12 bytes of the file.
$length = 12;

echo file_get_contents('example.txt', false, null, 0, $length);
```

The example above would produce the following output:

```
from $ to ?
```

The `"?"` appears, because 12-bytes worth of the example file's content lands in the middle of the three-byte euro symbol. This results in a malformed byte sequence, which PHP represents with the `"?"` character.

This library chunks very large files (and strings) in a multi-byte-safe way. It adjusts the chunk size slightly to ensure a well-formed byte sequence each time:

```php
use Jstewmc\Chunker\Text;

// Instantiate a new text chunker with UTF-8 encoding and a chunk size of four
// bytes (these constructor arguments are covered in more detail below).
$chunker = new Text('from $ to €', 'UTF-8', 4);

// Loop through the file's chunks, echo the chunk, and advance to the next one.
while (false !== ($chunk = $chunker->current())) {
	echo "'$chunk'";
	$chunker->next();
}
```

The example above would produce the following output:

```
'from'
' $ t'
'o '
'€'
```

Notice, the third chunk is only two one-byte characters, and the last chunk is a single three-byte character.

## Installation

This library requires [PHP 7.4+](https://secure.php.net).

It is multi-platform, and we strive to make it run equally well on Windows, Linux, and OSX.

It should be installed via [Composer](https://getcomposer.org). To do so, add the following line to the `require` section of your `composer.json` file, and run `composer update`:

```javascript
{
   "require": {
       "jstewmc/chunker": "^0.2"
   }
}
```

## Instantiating a chunker

There are two types of chunkers: _file_ and _text_.

### Instantiating a _file_ chunker

You can instantiate a `File` chunker using a file pathname:

```php
use Jstewmc\Chunker\File;

$chunker = new File('path/to/file.ext');
```

### Instantiating a _text_ chunker

You can instantiate a `Text` chunker using a string:

```php
use Jstewmc\Chunker\Text;

$chunker = new Chunker\Text('foo bar baz');
```

## Setting the character encoding

A file or string's character encoding lets PHP know how to understand its contents.

You can set the file (or string's) character encoding *explicitly* - using the chunker's constructor argument - or you can let this library set it *implicitly* - using your application's internal character encoding.

### Setting character encoding _explicitly_

To set the file or string's character encoding explicitly, use the constructor's second argument, `$encoding`. This is useful if you know the file or string differs from your application's encoding. An encoding must be a valid [character encoding](http://php.net/manual/en/function.mb-list-encodings.php) from PHP's [Multi-byte string library](http://php.net/manual/en/ref.mbstring.php):

```php
use Jstewmc\Chunker\Text;

$chunker = new Text('foo', 'UTF-8');
```

### Setting character encoding _implicitly_

To let this library set the file or string's character coding implicitly, don't pass a character encoding to the constructer. The chunk's encoding is assumed to be your application's internal character encoding, the value returned by PHP's [`mb_internal_encoding()`](http://php.net/manual/en/function.mb-internal-encoding.php):

```php
use Jstewmc\Chunker\Text;

$chunker = new Text('foo');
```

## Setting the chunk size

The chunker's "chunk size" setting determines the memory consumption of each chunk. This library attempts to provide sensible defaults: 8,192 *bytes* for files and 2,000 *characters* for strings (a maximum of around 8,000 bytes).

To change the chunk size, use the constructor's third argument, `$size` (remember, it's *bytes* for files and *characters* for text):

```php
use Jstewmc\Chunker\{File, Text};

// Use chunks of 8,192 bytes.
$chunker1 = new File('/path/to/file.ext', null, 8192);

// Use chunks of 2,000 characters.
$chunker2 = new Text('foo bar baz', null, 2000);
```

## Consuming the chunks

Chunkers are designed to mimic a stream or array of chunks.

You can use the `getCurrentChunk()` (alias, `current()`), `getNextChunk()` (alias, `next()`), and `getPreviousChunk()` (alias, `previous()`) to navigate between chunks (if a chunk does not exist, the methods will return false):

```php
use Jstewmc\Chunker\Text;

$chunker = new Text('foo');

$chunker->getCurrentChunk();   
// returns "foo" (the first chunk is immediately available upon instantiation)

$chunker->getNextChunk();      
// returns false (advances the internal pointer and returns next chunk)

$chunker->getPreviousChunk();  
// returns "foo" (rewinds the internal pointer and returns previous chunk)

$chunker->getCurrentChunk();   
// returns "foo"

$chunker->getCurrentChunk();   
// returns "foo" (because the internal pointer hasn't moved)
```

These methods will usually be combined in a `while` loop (keep in mind, it's important to strictly compare a chunk's value to `false`; it may return Boolean `false`, and it may also return a non-Boolean value which evaluates to `false`):

```php
while (false !== ($chunk = $chunker->current())) {
	//
	// do something with the $chunk...
	//
	// advance the pointer for the next iteration
	$chunker->next();
}
```

You can use the `countChunks()` method to count the chunks in a file or string:

```php
use Jstewmc\Chunker\Text;

$chunker = new Text('foo bar baz', null, 3);

$chunker->countChunks();  // returns 4 (the count is always rounded up)
```

You can use the `hasChunk()` or `hasChunks()` to see whether or not the file or string has *exactly one* chunk or has *one or more chunks*, respectively:

```php
use Jstewmc\Chunker\Text;

$chunker1 = new Text('foo', null, 3);

$chunker1->hasChunk();   // returns true
$chunker1->hasChunks();  // returns true

$chunker2 = new Text('foo bar', null, 3);

$chunker->hasChunk();   // returns false (there are three, three-char chunks)
$chunker->hasChunks();  // returns true
```

You can use the `hasPreviousChunk()` or `hasNextChunk()` to see whether or not the file or string has a *previous* chunk or a *next* chunk, respectively:

```php
use Jstewmc\Chunker\Text;

$chunker = new Text('foo bar', null, 3);

$chunker->countChunks();  // returns 3 (there are three, three-character chunks)

$chunker->hasPreviousChunk();  // returns false (the pointer is at zero)
$chunker->hasNextChunk();      // returns true

$chunker->next(); // advance to the next chunk

$chunker->hasPreviousChunk();  // returns true
$chunker->hasNextChunk();      // returns true

$chunker->next();

$chunker->hasPreviousChunk();  // returns true
$chunker->hasNextChunk();      // returns false (the pointer it at the end)
```

Finally, you can use `reset()` to reset the chunker's internal pointer to zero:

```php
use Jstewmc\Chunker\Text;

$chunker = new Text('foo bar', null, 3);

$chunker->current();  // returns "foo"

$chunker->next();     // returns " ba"
$chunker->current();  // returns " ba"

$chunker->reset();    

$chunker->current();  // returns "foo"
```

## Contributing

Contributions are welcome! Here are the steps to get started:

```bash
# Clone the repository (assuming you have Git installed).
~/path/to $ git clone git@github.com:jstewmc/chunker.git

# Install dependencies (assuming you are using Composer locally).
~/path/to/chunker $ php composer.phar install

# Run the tests.
~/path/to/chunker $ ./vendor/bin/phpunit

# Create and checkout a new branch.
~/path/to/chunker $ git branch -c YOUR_BRANCH_NAME

# Make your changes...

# Run the tests again.
~/path/to/chunker $ ./vendor/bin/phpunit

# Lint your changes.
~/path/to/chunker $ ./vendor/bin/phpcs .

# Fix the issues you can automatically.
~/path/to/usps-address $ ./vendor/bin/phpcbf .

# Push your changes to Github and create a pull request.
~/path/to/chunker $ git push origin YOUR_BRANCH_NAME
```

## License

This library is released under the [MIT license](https://github.com/jstewmc/chunker/blob/master/LICENSE).
