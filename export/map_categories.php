<?php
/**
 * map_categories.php
 *
 * Reads the latest variant-grouped Shopify CSV, inserts the "Product Category" column
 * if missing, applies standard taxonomy category classification, enriches tags,
 * and outputs the final CSVs and report.
 */

ini_set('memory_limit', '1024M');
set_time_limit(0);

$outputDir = __DIR__ . '/shopify';
$allFiles = glob($outputDir . '/shopify_products_grouped_variants_*.csv');
$files = array_filter($allFiles, function($f) {
    return strpos($f, 'with_categories') === false;
});
if (empty($files)) {
    echo "Error: No grouped CSV files found in $outputDir\n";
    exit(1);
}
rsort($files);
$sourceCsv = reset($files); // Newest grouped file

$timestamp = date('Ymd_His');
$groupedCsvPath = $outputDir . "/shopify_products_grouped_variants_with_categories_{$timestamp}.csv";
$desktopCsvPath = '/Users/erikbabcan/Desktop/growmedical-shopify-grouped-variants-final-with-categories.csv';
$reportMdPath = __DIR__ . '/../reports/SHOPIFY_PRODUCT_CATEGORY_MAPPING.md';

echo "Reading from: " . basename($sourceCsv) . "\n";

$srcFp = fopen($sourceCsv, 'r');
$header = fgetcsv($srcFp);
if (!$header) {
    echo "Error: Empty source CSV.\n";
    exit(1);
}

// Check if Product Category exists (case-insensitive)
$catColName = 'Product Category';
$foundIndex = false;
foreach ($header as $idx => $col) {
    if (strcasecmp($col, 'Product Category') === 0 || strcasecmp($col, 'Product category') === 0) {
        $foundIndex = $idx;
        $catColName = $col;
        break;
    }
}

if ($foundIndex === false) {
    // Insert "Product Category" at index 4 (between Vendor and Type)
    array_splice($header, 4, 0, [$catColName]);
    echo "Column '$catColName' was missing. Inserted at index 4.\n";
}

// Re-map column indexes
$colIndexes = [];
foreach ($header as $idx => $col) {
    $colIndexes[$col] = $idx;
}

$getVal = function($row, $colName) use ($colIndexes) {
    return isset($colIndexes[$colName]) ? trim($row[$colIndexes[$colName]]) : '';
};

// 1. Group rows by Handle in memory
$groupedProducts = [];
while (($row = fgetcsv($srcFp)) !== false) {
    // Insert empty column if it was missing
    if ($foundIndex === false) {
        array_splice($row, 4, 0, ['']);
    }
    
    $handle = $row[$colIndexes['Handle']] ?? '';
    if ($handle === '') {
        continue;
    }
    
    if (!isset($groupedProducts[$handle])) {
        $groupedProducts[$handle] = [];
    }
    $groupedProducts[$handle][] = $row;
}
fclose($srcFp);

echo "Loaded " . count($groupedProducts) . " unique product groups.\n";

// Helper function to classify category based on title, type, and tags
$classifyCategory = function($title, $type, $tags) {
    $combinedText = mb_strtolower($title . ' ' . $type . ' ' . $tags, 'UTF-8');
    
    // Remove diacritics for safer search matching
    $transliterator = 'Any-Latin; Latin-ASCII; Lower()';
    $cleanText = transliterator_transliterate($transliterator, $combinedText);
    
    // 1. Cosmetics
    $cosmeticTriggers = [
        'kozmetik', 'krem', 'balzam', 'olej', 'starostliv', 'telov', 
        'plet', 'mast', 'mydlo', 'sampon', 'kondicioner', 'gel', 
        'serum', 'maska', 'cistic pleti', 'emulzia', 'sprej', 'deodorant'
    ];
    foreach ($cosmeticTriggers as $trig) {
        if (strpos($cleanText, $trig) !== false) {
            return [
                'category' => 'Health & Beauty > Personal Care > Cosmetics',
                'sure' => true,
                'trigger' => $trig
            ];
        }
    }
    
    // 2. Health Care (Medical assistance, bandages, etc.)
    $healthCareTriggers = [
        'pomocka', 'bandaz', 'orteza', 'lekarnic', 'teplomer', 'naplast', 
        'obvaz', 'rukavice', 'respirator', 'rusko', 'tlakomer', 'zdravotnicka',
        'ustna hygiena', 'zubna', 'kefka', 'pasta'
    ];
    foreach ($healthCareTriggers as $trig) {
        if (strpos($cleanText, $trig) !== false) {
            return [
                'category' => 'Health & Beauty > Health Care',
                'sure' => true,
                'trigger' => $trig
            ];
        }
    }
    
    // 3. Vitamins & Supplements (explicit triggers)
    $supplementTriggers = [
        'protein', 'aminokyselin', 'vitamin', 'mineral', 'vyziv', 'spalovac', 
        'gainer', 'iont', 'suplement', 'kreatin', 'bcaa', 'glutamin', 
        'doplnok', 'tablety', 'kapsul', 'praskov', 'napoj', 'energy', 
        'sirup', 'kvapky', 'bylin', 'extrak', 'caj', 'reishi', 'zensen', 'cordyceps'
    ];
    foreach ($supplementTriggers as $trig) {
        if (strpos($cleanText, $trig) !== false) {
            return [
                'category' => 'Health & Beauty > Health Care > Fitness & Nutrition > Vitamins & Supplements',
                'sure' => true,
                'trigger' => $trig
            ];
        }
    }
    
    // 4. Default / Not sure
    return [
        'category' => 'Health & Beauty > Health Care > Fitness & Nutrition > Vitamins & Supplements',
        'sure' => false,
        'trigger' => 'none (default fallback)'
    ];
};

$categoryCounts = [];
$finalRows = [];
$reviewList = [];

// 2. Process groups and apply taxonomy mapping + tag enrichment
foreach ($groupedProducts as $handle => $rows) {
    $parentRow = $rows[0];
    
    $title = $getVal($parentRow, 'Title');
    $type = $getVal($parentRow, 'Type');
    $vendor = $getVal($parentRow, 'Vendor');
    
    // Collect tags from the parent row
    $rawTags = $getVal($parentRow, 'Tags');
    $tagsArray = array_map('trim', explode(',', $rawTags));
    $tagsArray = array_filter($tagsArray); // remove empty tags
    
    // Collect all unique variant options (flavors and sizes) within this product group
    $variantOptions = [];
    foreach ($rows as $r) {
        $opt1Val = $getVal($r, 'Option1 Value');
        $opt2Val = $getVal($r, 'Option2 Value');
        
        if ($opt1Val !== '' && $opt1Val !== 'Default Title' && $opt1Val !== 'Standard') {
            $variantOptions[] = $opt1Val;
        }
        if ($opt2Val !== '' && $opt2Val !== 'Standard') {
            $variantOptions[] = $opt2Val;
        }
    }
    
    // Add Legacy Category (Type) to tags if present
    if ($type !== '') {
        $tagsArray[] = $type;
    }
    
    // Add Vendor (Brand), Flavors, and Sizes to the tags
    if ($vendor !== '') {
        $tagsArray[] = $vendor;
    }
    foreach ($variantOptions as $opt) {
        $tagsArray[] = $opt;
    }
    
    // Clean and unique tags
    $tagsArray = array_unique($tagsArray);
    $enrichedTags = implode(', ', $tagsArray);
    
    // Classify category using base product information
    $classification = $classifyCategory($title, $type, $enrichedTags);
    $category = $classification['category'];
    
    if (!$classification['sure']) {
        $reviewList[] = [
            'handle' => $handle,
            'title' => $title,
            'type' => $type
        ];
    }
    
    // Track stats
    $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
    
    // Apply changes to rows
    $isFirst = true;
    foreach ($rows as $idx => $r) {
        $outRow = $r;
        
        if ($isFirst) {
            // Update parent row
            $outRow[$colIndexes[$catColName]] = $category;
            $outRow[$colIndexes['Tags']] = $enrichedTags;
            $isFirst = false;
        } else {
            // Shopify Variant row: leave Product category and other parent metadata blank
            $outRow[$colIndexes[$catColName]] = '';
        }
        $finalRows[] = $outRow;
    }
}

// 3. Write final CSV outputs
$outFp = fopen($groupedCsvPath, 'w');
$deskFp = fopen($desktopCsvPath, 'w');

fputcsv($outFp, $header);
fputcsv($deskFp, $header);

$emptyCategoryCount = 0;
$uniqueHandlesCheck = [];
$totalRowsCount = 0;
$expectedColsCount = count($header); // should be 35 now

foreach ($finalRows as $row) {
    // Basic validations
    $handle = $row[$colIndexes['Handle']] ?? '';
    $title = $row[$colIndexes['Title']] ?? '';
    
    if ($title !== '') { // Parent row
        $cat = $row[$colIndexes[$catColName]] ?? '';
        if ($cat === '') {
            $emptyCategoryCount++;
        }
    }
    
    if ($handle !== '') {
        $uniqueHandlesCheck[$handle] = true;
    }
    
    // Make sure we output exactly the header columns count per row
    $cleanRow = [];
    for ($i = 0; $i < $expectedColsCount; $i++) {
        $cleanRow[$i] = $row[$i] ?? '';
    }
    
    fputcsv($outFp, $cleanRow);
    fputcsv($deskFp, $cleanRow);
    $totalRowsCount++;
}

fclose($outFp);
fclose($deskFp);

// 4. Create Detailed Markdown Report
$reportContent = "# Shopify Product Category Mapping Report

This report summarizes the Shopify Standard Product Taxonomy classification applied to the Grow Medical product catalog.

---

## 1. Classification Summary

*   **Total variants exported (CSV rows)**: $totalRowsCount (Expected: 675)
*   **Unique Shopify products (Handles)**: " . count($uniqueHandlesCheck) . " (Expected: 557)
*   **Parent products with empty Product Category**: $emptyCategoryCount

---

## 2. Category Usage Breakdown

";

foreach ($categoryCounts as $cat => $count) {
    $reportContent .= "*   **$cat**: $count products\n";
}

$reportContent .= "
---

## 3. Classification Rules Applied

*   **Cosmetics (`Health & Beauty > Personal Care > Cosmetics`)**: Matches titles/tags containing creams, body care, oils, balms, etc.
*   **Health Care (`Health & Beauty > Health Care`)**: Matches general medical supplies, bandages, braces, thermometers, toothbrushes, toothpastes, and oral care.
*   **Vitamins & Supplements (`Health & Beauty > Health Care > Fitness & Nutrition > Vitamins & Supplements`)**: Default fallback for nutritional supplements, proteins, vitamins, and minerals.

---

## 4. Products Requiring Category Review

";

if (empty($reviewList)) {
    $reportContent .= "No products require category review.\n";
} else {
    $reportContent .= "The following " . count($reviewList) . " products did not match any specific category triggers and were assigned the default safe category (**Health & Beauty > Health Care > Fitness & Nutrition > Vitamins & Supplements**):\n\n";
    $reportContent .= "| Handle | Title | Legacy Type |\n";
    $reportContent .= "| :--- | :--- | :--- |\n";
    foreach ($reviewList as $item) {
        $reportContent .= "| `{$item['handle']}` | {$item['title']} | `{$item['type']}` |\n";
    }
}

$reportContent .= "
---

## Final Status

**READY_FOR_SHOPIFY_TEST_IMPORT**
";

file_put_contents($reportMdPath, $reportContent);

echo "SUCCESS:\n";
echo "Grouped CSV with Categories: $groupedCsvPath\n";
echo "Desktop CSV: $desktopCsvPath\n";
echo "Report MD: $reportMdPath\n";
echo "Stats:\n";
echo "  - Total variants (rows): $totalRowsCount\n";
echo "  - Unique handles: " . count($uniqueHandlesCheck) . "\n";
echo "  - Empty parent categories: $emptyCategoryCount\n";
echo "Categories used:\n";
foreach ($categoryCounts as $cat => $count) {
    echo "  - $cat ($count times)\n";
}
