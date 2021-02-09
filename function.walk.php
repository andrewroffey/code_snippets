<?php

/**
 * Generate the file names in a directory tree by walking the tree
 * top-down. For each directory in the tree rooted at directory
 * $parent_dir (including $parent_dir itself), it returns a 3-item
 * array($dirpath, $dirnames, $filenames).
 *
 * $dirpath is a string, the path to the directory. $dirnames is a list
 * of the names of the subdirectories in dirpath (excluding '.' and
 * '..'). $filenames is a list of the names of the non-directory files in
 * $dirpath.
 *
 * The interface is somewhat adapted from Python os.walk; see
 * https://docs.python.org/3/library/os.html#os.walk
 *
 * Main limitation compared to Python is that the whole graph is loaded
 * into memory all at once.  Also see class.walker.php for a similar PHP
 * Iterator which walks the tree iteratively.
 *
 * @return array
 */
function walk($parent_dir, $depth = 0) {
  // array(string $dirname, array $dirnames, array $filenames)
  $nodes = array();
  $dirnames = array();
  $filenames = array();
  if ($dh = opendir($parent_dir)) {
    while (false !== ($fn = readdir($dh))) {
      if ($fn != '.' && $fn !== '..') {
        $fullfn = $parent_dir . '/' . $fn;
        if (is_dir($fullfn)) {
          $dirnames[] = $fn;
	} else {
	  $filenames[] = $fn;
	}
      }
    }
    closedir($dh);
    $nodes[] = array($parent_dir, $dirnames, $filenames);
  }
  foreach ($dirnames as $dirname) {
    $fulldir = $parent_dir . '/' . $dirname;
    // recurse subdirectories
    $nodes = array_merge($nodes, walk($fulldir, $depth + 1));
  }
  return $nodes;
}

