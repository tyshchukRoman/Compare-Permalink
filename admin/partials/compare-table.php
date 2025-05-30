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

/*
 * Construct array
 */
$urls = [];

$i = 0;

while($i < count($posts) && $i < count($imported_urls)) {

  if(isset($posts[$i])) {
    $urls[$i][0] = get_permalink($posts[$i]);
  }

  if(isset($imported_urls[$i])) {
    $urls[$i][1] = $imported_urls[$i];
  }

  $i++;
}

?>

<div class="compare-permalinks-table">
  <table>
    <thead>
      <tr>
        <th><?php _e('Current website URLs', 'compare-permalinks') ?></th>
        <th><?php _e('Imported URLs', 'compare-permalinks') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($urls as $url): 
        $current_url = isset($url[0]) ? $url[0] : '';
        $imported_url = isset($url[1]) ? $url[1] : '';
      ?>
        <tr>
          <td>
            <a target="_blank" href="<?php echo $current_url ?>">
              <?php echo $current_url ?>
            </a>
          </td>
          <td>
            <a target="_blank" href="<?php echo $imported_url ?>">
              <?php echo $imported_url ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
