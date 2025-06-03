<?php

function cp_get_url_path($url) {
  return trim(wp_parse_url($url, PHP_URL_PATH), '/');
}
