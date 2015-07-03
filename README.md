# Chunker
Chunk a large file or string with PHP (multi-byte safe).

```php
use Jstewmc\Chunker;

// create an example file with the mixed-byte string of "from $ to €"
// keep in mind, every character in "from $ to " is one-byte in UTF-8; however, the
//     euro symbol, "€", is three-bytes in UTF-8
//
file_put_contents('example.txt', "from $ to €");

// create a file chunker
$chunker = new Chunker\File('example.txt');

// for this example, give the chunker a chunk size of four bytes
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


## Encoding

TODO

## Files

TODO

## Strings

TODO

## Usage

TODO

## Author

Jack Clayton - [clayjs0@gmail.com](mailto:clayjs0@gmail.com)

## License

This library is released under the [MIT license](https://github.com/jstewmc/chunker/blob/master/LICENSE).

## Version

0.1.0
