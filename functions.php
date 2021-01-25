<?php
function getImageString($image) {
  ob_start();
  imagebmp($image);
  $image_data = ob_get_contents();
  ob_end_clean();

  return 'data:image/bmp;base64,' . base64_encode($image_data);
}

function setupPartImage($width, $height, &$black, &$white = null) {
  $part = imagecreate($width * 2, $height);
  $white = imagecolorallocate($part, 255,255,255);
  $black = imagecolorallocate($part, 0,0,0);
  return $part;
}

function setupPartImage4($width, $height, &$black, &$white = null) {
  $part = imagecreate($width * 2, $height * 2);
  $white = imagecolorallocate($part, 255,255,255);
  $black = imagecolorallocate($part, 0,0,0);
  return $part;
}