<?php

function cp_get_inline_svg($name) {
  return file_get_contents(COMPARE_PERMALINKS_URI . "assets/icons/$name");
}
