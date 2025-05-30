<div class="compare-permalinks-actions">
  <div class="compare-permalinks-action">
    <h2>
      <?php _e('Export URLs from the Current Website', 'compare-permalinks') ?>
    </h2>
    <p>
      <?php _e('You can export all URLs from the current website and import the file into your new website to verify that all links match correctly.', 'compare-permalinks') ?>
    </p>
    <form method="post">
      <?php wp_nonce_field('compare_permalinks_export_csv', 'compare_permalinks_export_csv_nonce'); ?>
      <input type="hidden" name="compare_permalinks_export_csv" value="1">
      <?php submit_button('Export'); ?>
    </form>
  </div>

  <div class="compare-permalinks-action">
    <h2>
      <?php _e('Import URLs from the Old Website', 'compare-permalinks') ?>
    </h2>
    <p>
      <?php _e('Upload your text file containing URLs, and we will compare them against the URLs on your current website.', 'compare-permalinks') ?>
    </p>
    <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field('compare_permalinks_file_upload', 'compare_permalinks_file_upload_nonce'); ?>
      <input type="file" name="imported-links" accept=".csv" required>
      <?php submit_button(__('Compare', 'compare-permalinks')); ?>
    </form>
  </div>
</div>
