<?php

function compressPNG($png) {
	if (!file_exists($png)) {
		throw new Exception('File does not exist: ' . $png);
	}
	$cmd = 'pngquant.exe --force --verbose --ordered --speed=1 --ext .png --quality=80-100 ' . escapeshellarg($png);
	shell_exec($cmd);
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
					if (pathinfo($path, PATHINFO_EXTENSION) == 'png') {
						compressPNG($path);
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
