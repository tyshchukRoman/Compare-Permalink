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
$imported_urls = cp_get_imported_urls('imported-links');

/*
 * Fetch all urls on current website
 */
$current_urls = array_map(fn($url) => cp_get_url_path($url), cp_get_current_urls());

/*
 * Find all new urls on current website
 */
$results = cp_compare_urls($imported_urls, $current_urls);

$site_url = rtrim(get_site_url(), '/');

?>

<div style="display: flex; gap: 10px; margin-bottom: 10px;">
  <select id="permalink-filter">
    <option value="all"><?php _e('All', 'compare-permalinks') ?></option>
    <option value="match"><?php _e('Matches', 'compare-permalinks') ?> ✅</option>
    <option value="mismatch"><?php _e('Mismatches', 'compare-permalinks') ?> ❌</option>
  </select>

  <button id="toggle-domain" class="button"><?php _e('Toggle Domain Name', 'compare-permalinks') ?></button>
</div>

<table class="widefat striped" id="permalink-table" data-site-url="<?php echo esc_attr($site_url); ?>">
  <thead>
    <tr>
      <th><?php _e('Imported Permalink', 'compare-permalinks') ?></th>
      <th><?php _e('Matched Site Permalink', 'compare-permalinks') ?></th>
      <th><?php _e('Status', 'compare-permalinks') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($results as $row): ?>
      <tr class="permalink-row <?php esc_attr_e($row['status']) ?>">
        <td class="imported-link" data-path="<?php echo esc_attr($row['imported']) ?>">
          <?php echo esc_html($row['imported']) ?>
        </td>
        <td class="matched-link" data-path="<?php echo esc_attr($row['current'] ?? '') ?>">
          <?php echo esc_html($row['current'] ?? '—') ?>
        </td>
        <td>
          <?php if ($row['status'] === 'match'): ?>
            ✅ <?php _e('Match', 'compare-permalinks') ?>
          <?php else: ?>
            ❌ <?php _e('Mismatch', 'compare-permalinks') ?> (<?php echo round($row['similarity'], 1) ?>%)
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<button id="export-csv" class="button" style="margin-top: 10px;"><?php _e('Export CSV', 'compare-permalinks') ?></button>

<script>
  // Filtering logic
  document.getElementById('permalink-filter').addEventListener('change', function () {
    const selected = this.value;
    const rows = document.querySelectorAll('.permalink-row');

    rows.forEach(row => {
      if (selected === 'all') {
        row.style.display = '';
      } else if (!row.classList.contains(selected)) {
        row.style.display = 'none';
      } else {
        row.style.display = '';
      }
    });
  });

  // Toggle domain logic
  document.getElementById('toggle-domain').addEventListener('click', function () {
    const table = document.getElementById('permalink-table');
    const siteUrl = table.dataset.siteUrl;

    const toggleCellLink = (cell, type) => {
      const path = cell.dataset.path;
      if (!path || path === '—') return;

      const isLinked = cell.querySelector('a');
      if (isLinked) {
        cell.textContent = path;
      } else {
        const a = document.createElement('a');
        a.href = siteUrl + path;
        a.textContent = siteUrl + path;
        a.target = '_blank';
        cell.textContent = '';
        cell.appendChild(a);
      }
    };

    document.querySelectorAll('.imported-link').forEach(cell => toggleCellLink(cell, 'imported'));
    document.querySelectorAll('.matched-link').forEach(cell => toggleCellLink(cell, 'matched'));
  });
</script>

<script>
  document.getElementById('export-csv').addEventListener('click', function () {
    const rows = document.querySelectorAll('.permalink-row');
    const headers = ['Imported Permalink', 'Matched Site Permalink', 'Status', 'Similarity %'];
    const csv = [headers];

    rows.forEach(row => {
      const imported = row.querySelector('.imported-link')?.dataset.path || '';
      const matched = row.querySelector('.matched-link')?.dataset.path || '';
      const status = row.classList.contains('match') ? 'Match' : 'Mismatch';
      const similarityText = row.querySelector('td:last-child')?.textContent.match(/\(([\d.]+)%\)/);
      const similarity = similarityText ? similarityText[1] : (status === 'Match' ? '100' : '');

      csv.push([imported, matched, status, similarity]);
    });

    // Convert to CSV format
    const csvContent = csv.map(row => row.map(val => `"${val}"`).join(',')).join('\n');

    // Trigger download
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'permalink-comparison.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });
</script>

