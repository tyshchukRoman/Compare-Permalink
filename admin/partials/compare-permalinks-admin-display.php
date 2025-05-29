<?php

/**
  * Provide a admin area view for the plugin
  *
  * This file is used to markup the admin-facing aspects of the plugin.
  *
  * @link       http://example.com
  * @since      1.0.0
  *
  * @package    Compare_Permalinks
  * @subpackage Compare_Permalinks/admin/partials
  */


  $args = [
    'post_type'       => ['post', 'page'],
    'post_status'     => 'publish',
    'posts_per_page'  => -1,
    'orderby'         => 'title',
    'order'           => 'ASC',
  ];

  $posts = get_posts($args);

?>

<div class="wrap | compare-permalinks-settings-page">
  <h1>
    <?php _e('Compare Permalinks', 'compare-permalinks') ?>
  </h1>

  <div class="compare-permalinks-actions">
    <h2><?php _e('Import URLs from the Old Website', 'compare-permalinks') ?></h2>
    <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field('compare_permalinks_file_upload', 'compare_permalinks_file_upload_nonce'); ?>
      <input type="file" name="imported-links" accept=".csv" required>
      <?php submit_button(__('Upload and Process', 'compare-permalinks')); ?>
    </form>
  </div>

  <?php
    if (
      isset($_FILES['imported-links']) &&
      isset($_POST['compare_permalinks_file_upload']) &&
      wp_verify_nonce($_POST['compare_permalinks_file_upload_nonce'], 'compare_permalinks_file_upload')
    ):
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
          <?php foreach($posts as $post): ?>
            <tr>
              <td>
                <a href="<?php echo get_permalink($post) ?>">
                  <?php echo get_permalink($post) ?>
                </a>
              </td>
              <td>
                <a href="<?php echo get_permalink($post) ?>">
                  <?php echo get_permalink($post) ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
