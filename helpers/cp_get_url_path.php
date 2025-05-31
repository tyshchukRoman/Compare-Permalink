<?php

function cp_get_url_path($url) {
  $parsed = parse_url($url);
  return $parsed['path'] ?? '/';
}
