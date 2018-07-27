<?php

function checkBOM($filename) {
	$content = file_get_contents($filename);
	$charset[1] = substr($content, 0, 1);
	$charset[2] = substr($content, 1, 1);
	$charset[3] = substr($content, 2, 1);
	if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
		return true;
	} else {
		return false;
	}
}

function removeBOM($filename) {
	if (checkBOM($filename)) {
		$content = file_get_contents($filename);
		$rest = substr($content, 3);
		$f = fopen($filename, 'w');
		flock($f, LOCK_EX);
		fwrite($f, $rest);
		fclose($f);
	}
}

function checkDir($dir) {
	$dh = opendir($dir);
	if ($dh) {
		while (($file = readdir($dh)) !== false) {
			if ($file != '.' && $file != '..' && $file != '.svn') {
				$path = $dir . DIRECTORY_SEPARATOR . $file;
				if (is_dir($path)) {
					checkDir($path);
				} else {
					if (pathinfo($path, PATHINFO_EXTENSION) == 'php' && checkBOM($path)) {
						echo 'file: ', $path, " has BOM\n";
					}
				}
			}
		}
		closedir($dh);
	}
}

if ($argc < 2) {
	die('Please input target-directory');
}

$dir = $argv[1];
if (!is_dir($dir)) {
	die('Please input valid directory');
}

checkDir($dir);
