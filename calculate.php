<?php

/**
 * Calculate lines of files
 *
 * @author Ondřej Kubíček (http://www.kubon.cz)
 */

$args = $_SERVER['argv'];

if (isset($args[1])) {
	$count = 0;
	$path = $args[1];

	if (is_file($path)) {
		$iterator = [$path];
	} elseif (is_dir($path)) {
		$iterator = new CallbackFilterIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)), function($file, $key, $iterator) {
			return !$file->isDir() && !$iterator->isDot();
		});
	} else {
		echo "Path $path not found.\n";
		die(1);
	}

	foreach ($iterator as $file) {
		echo $file;
		$count += $lines = iterator_count(getLines($file));
		echo " $lines\n";
	}

	echo "Total lines of files: $count";

} else {
	echo "
		Code lines
		----------------------------
		Usage: {$args[0]} [<directory> | <file>]
		";
	die(1);
}

function getLines($file)
{
    $f = fopen($file, 'r');

    // read each line of the file without loading the whole file to memory
    try {
	    while ($line = fgets($f)) {
	        yield $line;
	    }
    } finally {
    	fclose($f);
    }
}
