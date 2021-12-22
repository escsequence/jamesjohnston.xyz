<?php
  //Download.php
  include('db.php');

  $obfr = $_GET['obfr'];
  $obf = md5($obfr);
  $fv = $_GET['fv'];
  $fn = $_GET['fn'];
  $file = 'cabinet/' . $obf . '/' . $fv . '/' . $fn;
  echo $file;
?>
