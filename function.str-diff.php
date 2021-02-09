<?php

/**
 * For two strings, return array of the common prefix and the two
 * differing suffixes, if any.
 *
 * For example, str_diff('/a/b/c/d/e', '/a/b/d/e/f') would return
 * array('/a/b/', 'c/d/e', 'd/e/f')
 */
function str_diff($str1, $str2) {
  $common = '';
  $diff1 = '';
  $diff2 = '';
  $i = 0;
  for ($i = 0; $i < min(strlen($str1), strlen($str2)); $i++) {
    if ($str1[$i] == $str2[$i]) {
      $common .= $str1[$i];
    } else {
      break;
    }
  }
  $diff1 = substr($str1, $i);
  $diff2 = substr($str2, $i);
  return array($common, $diff1, $diff2);
}

