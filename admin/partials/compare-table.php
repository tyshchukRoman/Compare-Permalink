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

$results = array_map(function($row) {
  $redirection_exists = cp_redirection_exists($row['imported'], $row['current']);

  if($redirection_exists) {
    $row['status'] = 'redirect';
  }

  return $row;
}, $results);

$site_url = rtrim(get_site_url(), '/');

?>

<div style="display: flex; gap: 10px; margin-bottom: 10px;">
  <select id="permalink-filter">
    <option value="all"><?php esc_html_e('All', 'compare-permalinks') ?></option>
    <option value="match"><?php esc_html_e('Matches', 'compare-permalinks') ?> ✅</option>
    <option value="mismatch"><?php esc_html_e('Mismatches', 'compare-permalinks') ?> ❌</option>
  </select>

  <button id="toggle-domain" class="button"><?php esc_html_e('Toggle Domain Name', 'compare-permalinks') ?></button>
</div>

<table class="widefat striped" id="permalink-table" data-site-url="<?php esc_attr_e($site_url); ?>">
  <thead>
    <tr>
      <th><?php esc_html_e('Imported Permalink', 'compare-permalinks') ?></th>
      <th><?php esc_html_e('Site Permalink', 'compare-permalinks') ?></th>
      <th><?php esc_html_e('Status', 'compare-permalinks') ?></th>
      <th><?php esc_html_e('Redirection', 'compare-permalinks') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($results as $row): ?>
      <tr class="permalink-row <?php esc_attr_e($row['status']) ?>">
        <td class="imported-link" data-path="<?php esc_attr_e($row['imported']) ?>">
          <?php echo esc_html($row['imported']) ?>
        </td>
        <td class="matched-link" data-path="<?php esc_attr_e($row['current'] ?? '') ?>">
          <?php echo esc_html($row['current'] ?? '—') ?>
        </td>
        <td>
          <?php if ($row['status'] === 'match'): ?>
            ✅ <?php esc_html_e('Match', 'compare-permalinks') ?>
          <?php elseif($row['status'] === 'mismatch'): ?>
            ❌ <?php esc_html_e('Mismatch', 'compare-permalinks') ?> (<?php echo round($row['similarity'], 1) ?>%)
          <?php elseif($row['status'] === 'redirect'): ?>
            🔁 <?php esc_html_e('Redirects', 'compare-permalinks') ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($row['status'] === 'mismatch' && !empty($row['current'])): ?>
            <label>
              <input 
                type="checkbox" 
                class="add-redirect-checkbox" 
                data-old="<?php esc_attr_e($row['imported']) ?>" 
                data-new="<?php esc_attr_e($row['current']) ?>"
              >
              <?php esc_html_e('Add Redirection', 'compare-permalinks') ?>
            </label>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div style="display: flex; gap: 10px; margin-block: 10px;">
  <button id="export-csv" class="button">
    <?php esc_html_e('Export Results', 'compare-permalinks') ?>
  </button>

  <button id="export-redirection-rules" class="button">
    <?php esc_html_e('Export Redirection Rules', 'compare-permalinks') ?>
  </button>
</div>


<script>

// Filter rows by status
document.getElementById('permalink-filter').addEventListener('change', function () {
  const selected = this.value;
  const rows = document.querySelectorAll('.permalink-row');

  rows.forEach(row => {
    row.style.display = (selected === 'all' || row.classList.contains(selected)) ? '' : 'none';
  });
});

// Toggle domain display
document.getElementById('toggle-domain').addEventListener('click', function () {
  const siteUrl = document.getElementById('permalink-table').dataset.siteUrl;

  const toggleCellLink = (cell) => {
    const path = cell.dataset.path;
    if (!path || path === '—') return;

    const hasLink = cell.querySelector('a');
    if (hasLink) {
      cell.textContent = path;
      cell.dataset.path = path;
    } else {
      const fullUrl = siteUrl + path;
      const a = document.createElement('a');
      a.href = fullUrl;
      a.textContent = fullUrl;
      a.target = '_blank';
      cell.textContent = '';
      cell.appendChild(a);
    }
  };

  document.querySelectorAll('.imported-link, .matched-link').forEach(cell => toggleCellLink(cell));
});

// Export CSV of comparison results
document.getElementById('export-csv').addEventListener('click', function () {
  const rows = document.querySelectorAll('.permalink-row');
  const headers = ['Imported Permalink', 'Matched Site Permalink', 'Status', 'Similarity %'];
  const csv = [headers];
  const siteName = '<?php esc_html_e(get_bloginfo('name')); ?>';

  rows.forEach(row => {
    const imported = row.querySelector('.imported-link')?.dataset.path || '';
    const matched = row.querySelector('.matched-link')?.dataset.path || '';
    const status = row.classList.contains('match') ? 'Match' : 'Mismatch';
    const similarityMatch = row.querySelector('td:nth-child(3)')?.textContent.match(/\(([\d.]+)%\)/);
    const similarity = similarityMatch ? similarityMatch[1] : (status === 'Match' ? '100' : '');
    csv.push([imported, matched, status, similarity]);
  });

  const csvContent = csv.map(r => r.map(v => `"${v}"`).join(',')).join('\n');
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = siteName + '-permalink-comparison.csv';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
});

// Export redirection rules
document.getElementById('export-redirection-rules').addEventListener('click', function () {
  const checkboxes = document.querySelectorAll('.add-redirect-checkbox:checked');
  if (checkboxes.length === 0) {
    alert('<?php esc_html_e('No redirects selected.', 'compare-permalinks') ?>');
    return;
  }

  const rows = [];
  const siteUrl = '<?php echo esc_url(home_url()); ?>';
  const siteName = '<?php echo get_bloginfo('name'); ?>';

  checkboxes.forEach(cb => {
    let oldUrl = cb.dataset.old.trim();
    let newUrl = cb.dataset.new.trim();
    if (!oldUrl.startsWith('/')) oldUrl = '/' + oldUrl;
    if (!newUrl.startsWith('/')) newUrl = '/' + newUrl;
    rows.push([oldUrl, newUrl, '0', '301']);
  });

  const csvContent = rows.map(row => row.join(',')).join('\n');
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = siteName + '-redirection-rules.csv';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
});

</script>
