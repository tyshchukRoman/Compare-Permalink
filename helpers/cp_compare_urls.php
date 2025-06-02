<?php

function cp_compare_urls($imported_links, $current_links) {
    $results = [];

    foreach ($imported_links as $imported_link) {
        if (in_array($imported_link, $current_links)) {
            $results[] = [
                'status' => 'match',
                'imported' => $imported_link,
                'current' => $imported_link,
                'highlight' => null
            ];
        } else {
            // Try to find closest match by similarity
            $best_match = null;
            $highest_similarity = 0;

            foreach ($current_links as $current_link) {
                similar_text($imported_link, $current_link, $percent);
                if ($percent > $highest_similarity) {
                    $highest_similarity = $percent;
                    $best_match = $current_link;
                }
            }

            $results[] = [
                'status' => 'mismatch',
                'imported' => $imported_link,
                'current' => $best_match,
                'similarity' => $highest_similarity,
            ];
        }
    }

    return $results;
}
