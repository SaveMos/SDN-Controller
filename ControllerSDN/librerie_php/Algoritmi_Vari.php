<?php

function fixObject(&$object)
{
  if (!is_object($object) && gettype($object) == 'object')
    return ($object = unserialize(serialize($object)));
  return $object;
}

function PrintMatrix($matr)
{
  echo "<br>";
  $dim = count($matr[0]);
  for ($i = 0; $i < $dim; $i++) {
    for ($j = 0; $j < $dim; $j++) {
      if ($matr[$i][$j] == 99999) {
        echo "- \t";
      } else {
        echo $matr[$i][$j] . "\t";
      }
    }
    echo "<br>";
  }
}

