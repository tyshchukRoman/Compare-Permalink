=== Compare Permalinks ===
Contributors: tisukRoman  
Tags: migration, permalinks, redirects, redirection, export, SEO  
Requires at least: 5.0  
Tested up to: 6.8  
Requires PHP: 7.2  
Stable tag: 1.0.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Easily compare imported permalinks with your current site's structure. Spot mismatches after migration, export results, and generate redirect rules.

== Description ==

**Compare Permalinks** helps you identify permalink changes after migrating your WordPress website. Upload a file containing old URLs and compare them with your site's current permalinks. Also, you can export redirect rules for importing them into "Redirection" plugin.

Features:

- Upload a file with old permalinks (one URL per line)
- Compare against the current site’s permalinks
- Highlight which permalinks are:
  - Still valid (matches)
  - Missing (not found on the site)
  - New (present on site but not in uploaded list)
- Show detailed comparison and similarity percentage
- Filter results by match/mismatch
- Toggle domain names on/off for link inspection
- Export results to CSV
- Select mismatched rows to export as "Redirection" plugin-compatible rules
- Check if a redirection already exists (if "Redirection" plugin is active)

Ideal for SEO teams, developers, and agencies doing site migrations.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/compare-permalinks` directory, or install through the WordPress plugins screen directly.
2. Activate the plugin through the ‘Plugins’ screen in WordPress.
3. Go to **Tools → Compare Permalinks** to begin.

== Frequently Asked Questions ==

= What file format do I need to upload? =  
A plain `.csv` file with one permalink per line (e.g., `/about-us`, `/contact`).

= What happens if the Redirection plugin is not installed? =  
Redirect detection will be disabled automatically but you can export redirect rules anyway

= Can I use full URLs instead of relative paths? =  
Yes, the plugin will extract the path (`/example-page/`) and ignore the domain.

== Screenshots ==

1. Upload and compare old permalinks with the current site.
2. Filter by match status and toggle domain visibility.
3. Export comparison results or redirection rules.
4. Generate Redirection plugin-compatible CSV exports.

== Changelog ==

= 1.0.0 =
* Initial release
* Permalink comparison with status and similarity
* CSV export of results
* Export redirects compatible with Redirection plugin
* Redirection existence check
* Toggle domain and filter view

== Upgrade Notice ==

= 1.0.0 =
First version — compare permalinks after migration and export redirect rules.

