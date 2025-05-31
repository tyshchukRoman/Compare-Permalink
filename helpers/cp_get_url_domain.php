<?php

function cp_get_url_domain($url) {
  $parsed = parse_url($url);
  $scheme = $parsed['scheme'] ?? 'http';
  $host = $parsed['host'] ?? '';
  return $scheme . '://' . $host;
}
