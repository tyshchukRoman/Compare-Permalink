=== Compare Permalinks ===
Contributors: tyshchukroman  
Tags: migration, permalinks, redirects, SEO
Requires at least: 5.0  
Tested up to: 6.8  
Requires PHP: 8.0  
Stable tag: 1.0.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Easily compare imported permalinks with your current site's structure. Spot mismatches after migration, export results, and generate redirect rules.

== Description ==

**Compare Permalinks** helps you identify permalink changes after migrating your WordPress website. Upload a file containing old URLs and compare them with your site's current permalinks. You can also export redirect rules for importing into the "Redirection" plugin.

**Features:**

- Upload a file with old permalinks (one URL per line, in `.csv` format)
- Compare against the current site’s permalinks
- Highlight which permalinks are:
  - Still valid (matches)
  - Missing (not found on the site)
  - Redirected (via the "Redirection" plugin)
- Show detailed comparisons and similarity percentages
- Filter results by match status: match, mismatch, or redirected
- Toggle domain names on/off for easier link inspection
- Export results to CSV
- Select mismatched rows to export as "Redirection"-compatible rules
- Detect existing redirections (if the "Redirection" plugin is active)

Ideal for SEO teams, developers, and agencies performing site migrations.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/compare-permalinks` directory, or install through the WordPress Plugins screen.
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Install the "Redirection" plugin (optional but recommended).
4. Go to **Tools → Compare Permalinks** to get started.

== Frequently Asked Questions ==

= What file format do I need to upload? =  
A plain `.csv` file with one permalink per line (e.g., `/about-us`, `/contact`).

= What happens if the Redirection plugin is not installed? =  
Redirect detection will be disabled automatically, but you can still export redirect rules.

= Can I use full URLs instead of relative paths? =  
Yes, the plugin will extract the path (`/example-page/`) and ignore the domain.

== Screenshots ==

1. Upload and compare old permalinks with the current site.
2. Filter by match status and toggle domain visibility.
3. Export comparison results or redirection rules.
4. Generate "Redirection" plugin-compatible CSV exports.

== Changelog ==

= 1.0.0 =
* Initial release
* Permalink comparison with status and similarity
* CSV export of results
* Export redirects compatible with the Redirection plugin
* Redirection existence check
* Toggle domain visibility and filter view

== Upgrade Notice ==
= 1.0.0 =
Initial release

