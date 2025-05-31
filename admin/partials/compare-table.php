<?php

/*
 * Check if user uploaded file
 */
if (!isset($_FILES['imported-links'])){
  return;
} 

/*
 * Check if form was submitted
 */
if(!isset($_POST['submit'])) {
  return;
}

/*
 * Check and verify nonce
 */
if(
  !isset($_POST['compare_permalinks_file_upload_nonce']) ||
  !wp_verify_nonce($_POST['compare_permalinks_file_upload_nonce'], 'compare_permalinks_file_upload')
){
  return;
}

/*
 * Check for file upload error
 */
if ($_FILES['imported-links']['error'] !== UPLOAD_ERR_OK) {
  return;
}

/*
 * Get URLs from imported file
 */
$imported_urls = [];
$imported_domain = null;

$tmp_name = $_FILES['imported-links']['tmp_name'];

$handle = fopen($tmp_name, 'r');

if ($handle !== false) {
  while (($url = fgets($handle)) !== false) {
    $url = trim($url);
    $imported_urls[] = cp_get_url_path($url);
    $imported_domain = cp_get_url_domain($url);
  }

  fclose($handle);
}

/*
 * Fetch all urls on current website
 */
$current_urls = array_map(fn($url) => cp_get_url_path($url), cp_get_current_urls());
$current_domain = cp_get_url_domain(get_site_url());

/*
 * Find all new urls on current website
 */
$new_urls = [];

foreach ($current_urls as $url) {
  if(!in_array($url, $imported_urls)) {
    $new_urls[] = $current_domain . $url;
  }
}

/*
 * Find missing urls in imported
 */
$missed_urls = [];

foreach ($imported_urls as $url) {
  if(!in_array($url, $current_urls)) {
    $missed_urls[] = $imported_domain . $url;
  }
}

?>

<div class="compare-permalinks-table">
  <table>
    <thead>
      <tr>
        <th><?php esc_html_e('Imported File: These URLs are missing from the current website', 'compare-permalinks') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($missed_urls as $url): ?>
        <tr>
          <td>
            <a target="_blank" href="<?php echo $url ?>">
              <?php echo cp_get_inline_svg('warning-icon.svg') ?>
              <?php echo $url ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="compare-permalinks-table">
  <table>
    <thead>
      <tr>
        <th><?php esc_html_e('Current Website: These URLs are missing from the imported file', 'compare-permalinks') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($new_urls as $url): ?>
        <tr>
          <td>
            <a target="_blank" href="<?php echo $url ?>">
              <?php echo cp_get_inline_svg('warning-icon.svg') ?>
              <?php echo $url ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
