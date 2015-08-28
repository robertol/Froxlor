<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2003-2009 the SysCP Team (see authors).
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  (c) the authors
 * @author     Florian Lippert <flo@syscp.org> (2003-2009)
 * @author     Froxlor team <team@froxlor.org> (2010-)
 * @license    GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package    Functions
 *
 */

/**
 * Returns an array of found directories
 *
 * This function checks every found directory if they match either $uid or $gid, if they do
 * the found directory is valid. It uses recursive-iterators to find subdirectories.
 *
 * @param  string $path the path to start searching in
 * @param  int $uid the uid which must match the found directories
 * @param  int $gid the gid which must match the found direcotries
 *
 * @return array Array of found valid paths
 */
function findDirs($path, $uid, $gid) {

	$_fileList = array ();
	$path = makeCorrectDir($path);

	// valid directory?
	if (is_dir($path)) {
		try {
			// create RecursiveIteratorIterator
			$its = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
			// we can limit the recursion-depth, but will it be helpful or
			// will people start asking "why do I only see 2 subdirectories, i want to use /a/b/c"
			// let's keep this in mind and see whether it will be useful
			// @TODO
			// $its->setMaxDepth(2);

			// check every file
			foreach ($its as $fullFileName => $it) {
				if ($it->isDir() && (fileowner($fullFileName) == $uid || filegroup($fullFileName) == $gid)) {
					$_fileList[] = makeCorrectDir(dirname($fullFileName));
				}
			}
		} catch (UnexpectedValueException $e) {
			// this is thrown if the directory is not found or not readble etc.
			// just ignore and keep going
		}
	}

	return array_unique($_fileList);

}
