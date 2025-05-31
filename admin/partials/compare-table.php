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

$tmp_name = $_FILES['imported-links']['tmp_name'];

$handle = fopen($tmp_name, 'r');

if ($handle !== false) {
  while (($url = fgets($handle)) !== false) {
    $imported_urls[] = trim($url);
  }

  fclose($handle);
}

/*
 * Fetch all posts and pages on current website
 */
$args = [
  'post_type'       => ['post', 'page'],
  'post_status'     => 'publish',
  'posts_per_page'  => -1,
  'orderby'         => 'title',
  'order'           => 'ASC',
];

$posts = get_posts($args);

$current_urls = array_map(fn($post) => get_permalink($post), $posts);

/*
 * Find all new urls on current website
 */
$new_urls = [];

foreach ($current_urls as $url) {
  if(!in_array($url, $imported_urls)) {
    $new_urls[] = $url;
  }
}

/*
 * Find missing urls in imported
 */
$missed_urls = [];

foreach ($imported_urls as $url) {
  if(!in_array($url, $current_urls)) {
    $missed_urls[] = $url;
  }
}

?>

<div class="compare-permalinks-table">
  <table>
    <thead>
      <tr>
        <th><?php _e('New URLs on current website:', 'compare-permalinks') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($new_urls as $url): ?>
        <tr>
          <td>
            <a target="_blank" href="<?php echo $url ?>">
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
        <th><?php _e('Missed URLs from the imported file:', 'compare-permalinks') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($missed_urls as $url): ?>
        <tr>
          <td>
            <a target="_blank" href="<?php echo $url ?>">
              <?php echo file_get_contents(COMPARE_PERMALINKS_URI . 'assets/icons/warning-icon.svg') ?>
              <?php echo $url ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
