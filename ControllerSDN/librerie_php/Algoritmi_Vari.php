<?php

function fixObject(&$object)
{
  if (
    (!is_object($object))
    && (gettype($object) == 'object')
  ) {
    return ($object = unserialize(serialize($object)));
  }
  return $object;
}

function SecureTextInput($text)
{
  if (count($text) == 0) {
    return $text;
  }
  $text = trim($text);
  $text = str_replace(' ', '-', $text);
  $text = htmlspecialchars($text);
  $text = strip_tags($text);
  $text = addslashes($text);
  return $text;
}

function SecureSubnetMask($mask)
{
  if (strlen($mask) > 2 || strlen($mask) == 0) {
    return false;
  }

  $mask = intval($mask);
  if ($mask < 0 || $mask > 32) {
    return false;
  } else {
    return true;
  }
}

function SecureMACAddress($mac)
{
  return filter_var($mac, FILTER_VALIDATE_MAC);
}

function SecureNumber($number)
{
  return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
}

function SecureIPAddress($ip)
{
  return filter_var($ip, FILTER_VALIDATE_IP);
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
