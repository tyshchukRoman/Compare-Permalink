<?php

function cp_compare_urls($imported, $current) {
    $results = [];

    foreach ($imported as $imported_link) {
        if (in_array($imported_link, $current)) {
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

            foreach ($current as $cur) {
                similar_text($imported_link, $cur, $percent);
                if ($percent > $highest_similarity) {
                    $highest_similarity = $percent;
                    $best_match = $cur;
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
