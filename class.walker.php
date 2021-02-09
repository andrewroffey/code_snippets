<?php

/**
 * Iterate  a directory tree by walking the tree. For each directory in
 * the tree rooted at directory $parent_dir (including $parent_dir
 * itself), it returns $dirpath => array($dirnames, $filenames).
 *
 * $dirpath is a string, the path to the directory. $dirnames is a list
 * of the names of the subdirectories in dirpath (excluding '.' and
 * '..'). $filenames is a list of the names of the non-directory files in
 * $dirpath.
 *
 * Inspired by Python os.walk; see
 * https://docs.python.org/3/library/os.html#os.walk
 * Implemented as an iterator rather than a generator.
 *
 * @return array
 */

class walker implements Iterator {
  private $parent_dir = '';
  private $d = 0; // iterator
  private $dirs = array(); // indexed by iterator
  private $dirstack;
  private $discovered = array(); // indexed by directory
  private $v; // current directory
  private $cur_dirnames = array(); // current subdirs
  private $cur_filenames = array(); // current files in directory

  public function __construct($parent_dir) {
    $this->parent_dir = $parent_dir;
    $this->d = 0;
    $this->dirs[$this->d] = $parent_dir;
    $this->dirstack = new SplStack();
    $this->dirstack->push($parent_dir);
    $this->discovered = array($parent_dir => true);
    $this->v = $parent_dir;
    $this->cur_dirnames = array();
    $this->cur_filenames = array();
    $this->next();
  }

  public function rewind() {
    $this->__construct($this->parent_dir);
  }

  public function current() {
    return array($this->cur_dirnames, $this->cur_filenames);
  }

  public function key() {
    return $this->v;
  }

  public function next() {
    ++$this->d;
    $this->v = $this->dirstack->pop();
    $this->dirs[$this->d] = $this->v;
    $this->cur_dirnames = array();
    $this->cur_filenames = array();
    if (!$dh = opendir($this->v)) {
      // opendir emits E_WARNING if unable to open directory, likely due
      // to a permissions issue or directory removed before we could get
      // there
      return;
    }
    // discover the directories, return directories and files
    while (false !== ($fn = readdir($dh))) {
      if ($fn != '.' && $fn !== '..') {
        $fullfn = $this->v . '/' . $fn;
        if (is_dir($fullfn)) {
	  $this->cur_dirnames[] = $fn;
          if (!array_key_exists($fullfn, $this->discovered)) {
            $this->discovered[$fullfn] = true;
	    $this->dirstack->push($fullfn);
	  }
        } else {
          $this->cur_filenames[] = $fn;
        }
      }
    }
    closedir($dh);
  }

  public function valid() {
    return $this->dirstack->count();
  }
}

