<?php

function cp_get_imported_urls($filename = 'imported-links') {
  $imported_urls = [];

  $tmp_name = $_FILES[$filename]['tmp_name'];

  $handle = fopen($tmp_name, 'r');

  if ($handle !== false) {
    while (($url = fgets($handle)) !== false) {
      $url = trim($url);
      $imported_urls[] = cp_get_url_path($url);
    }

    fclose($handle);
  }

  return $imported_urls;
}
