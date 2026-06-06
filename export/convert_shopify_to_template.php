<?php
/**
 * convert_shopify_to_template.php
 *
 * Converts a Shopify-compatible CSV file (exported by export_shopify.php)
 * into the Shopify product_template.csv format.
 *
 * Usage: php convert_shopify_to_template.php <source_file.csv> [output_file.csv]
 */

if ($argc < 2) {
    echo "Usage: php " . $argv[0] . " <source_shopify.csv> [output_template.csv]\n";
    exit(1);
}

$sourceFile = $argv[1];
$outputFile = isset($argv[2]) ? $argv[2] : __DIR__ . '/product_template.csv';

if (!file_exists($sourceFile)) {
    echo "Error: Source file '$sourceFile' does not exist.\n";
    exit(1);
}

$srcFp = fopen($sourceFile, 'r');
if (!$srcFp) {
    echo "Error: Cannot open source file '$sourceFile'.\n";
    exit(1);
}

$destFp = fopen($outputFile, 'w');
if (!$destFp) {
    echo "Error: Cannot open destination file '$outputFile' for writing.\n";
    fclose($srcFp);
    exit(1);
}

// Read header
$srcHeader = fgetcsv($srcFp, 0, ',', '"', '\\');
if (!$srcHeader) {
    echo "Error: Source file is empty.\n";
    fclose($srcFp);
    fclose($destFp);
    exit(1);
}

// Map column names to indexes
$headerMap = [];
foreach ($srcHeader as $idx => $h) {
    $headerMap[trim(strtolower($h))] = $idx;
}

// Define the template headers
$targetHeaders = [
    'Title', 'URL handle', 'Description', 'Vendor', 'Product category', 'Type', 'Tags',
    'Published on online store', 'Status', 'SKU', 'Barcode', 'Option1 name', 'Option1 value',
    'Option1 Linked To', 'Option2 name', 'Option2 value', 'Option2 Linked To', 'Option3 name',
    'Option3 value', 'Option3 Linked To', 'Price', 'Compare-at price', 'Cost per item',
    'Charge tax', 'Tax code', 'Unit price total measure', 'Unit price total measure unit',
    'Unit price base measure', 'Unit price base measure unit', 'Inventory tracker',
    'Inventory quantity', 'Continue selling when out of stock', 'Weight value (grams)',
    'Weight unit for display', 'Requires shipping', 'Fulfillment service', 'Product image URL',
    'Image position', 'Image alt text', 'Variant image URL', 'Gift card', 'SEO title',
    'SEO description', 'Color (product.metafields.shopify.color-pattern)',
    'Google Shopping / Google product category', 'Google Shopping / Gender', 'Google Shopping / Age group',
    'Google Shopping / Manufacturer part number (MPN)', 'Google Shopping / Ad group name',
    'Google Shopping / Ads labels', 'Google Shopping / Condition', 'Google Shopping / Custom product',
    'Google Shopping / Custom label 0', 'Google Shopping / Custom label 1', 'Google Shopping / Custom label 2',
    'Google Shopping / Custom label 3', 'Google Shopping / Custom label 4'
];

fputcsv($destFp, $targetHeaders, ',', '"', '\\');

// Helpers to get/format columns
$getVal = function($row, $colName) use ($headerMap) {
    $colKey = strtolower($colName);
    return isset($headerMap[$colKey]) && isset($row[$headerMap[$colKey]]) ? $row[$headerMap[$colKey]] : '';
};

$formatBool = function($val) {
    if ($val === null || $val === '') return '';
    $lower = strtolower(trim($val));
    if ($lower === 'true' || $lower === '1' || $lower === 'yes') return 'TRUE';
    if ($lower === 'false' || $lower === '0' || $lower === 'no') return 'FALSE';
    return $val;
};

$formatStatus = function($val) {
    if ($val === '') return '';
    return ucfirst(strtolower(trim($val)));
};

$formatPolicy = function($val) {
    if ($val === '') return '';
    $lower = strtolower(trim($val));
    if ($lower === 'deny') return 'DENY';
    if ($lower === 'continue') return 'CONTINUE';
    return strtoupper($val);
};

// Function to process a buffer of variants belonging to the same product handle
$flushBuffer = function($buffer) use ($destFp, $getVal, $formatBool, $formatStatus, $formatPolicy) {
    if (empty($buffer)) return;

    // Detect which option corresponds to color
    $colorOptions = [];
    $firstRow = $buffer[0];
    
    $optNames = [
        1 => trim(strtolower($getVal($firstRow, 'Option1 Name'))),
        2 => trim(strtolower($getVal($firstRow, 'Option2 Name'))),
        3 => trim(strtolower($getVal($firstRow, 'Option3 Name'))),
    ];

    $colorIndices = [];
    $colorTranslations = ['color', 'colour', 'farba', 'barva', 'farbe', 'couleur'];
    foreach ($optNames as $idx => $name) {
        if (in_array($name, $colorTranslations)) {
            $colorIndices[] = $idx;
        }
    }

    // Collect all unique colors from all rows in the buffer
    $colors = [];
    foreach ($buffer as $row) {
        foreach ($colorIndices as $idx) {
            $colorVal = trim($getVal($row, "Option{$idx} Value"));
            if ($colorVal !== '' && strtolower($colorVal) !== 'default title') {
                $colors[] = $colorVal;
            }
        }
    }
    $colors = array_unique($colors);
    $colorsStr = !empty($colors) ? implode('; ', $colors) : '';

    // Write rows
    foreach ($buffer as $i => $row) {
        $isFirst = ($i === 0);

        // Map fields
        $title = $isFirst ? $getVal($row, 'Title') : '';
        $handle = $getVal($row, 'Handle');
        $description = $isFirst ? $getVal($row, 'Body (HTML)') : '';
        $vendor = $isFirst ? $getVal($row, 'Vendor') : '';
        $type = $isFirst ? $getVal($row, 'Type') : '';
        $tags = $isFirst ? $getVal($row, 'Tags') : '';
        $published = $isFirst ? $formatBool($getVal($row, 'Published')) : '';
        $status = $isFirst ? $formatStatus($getVal($row, 'Status')) : '';
        $giftCard = $isFirst ? $formatBool($getVal($row, 'Gift Card')) : '';
        $seoTitle = $isFirst ? $getVal($row, 'SEO Title') : '';
        $seoDesc = $isFirst ? $getVal($row, 'SEO Description') : '';

        // Category matching - check type or leave blank
        $productCategory = ''; // We can map or leave empty. In Shopify, Category is a standard taxonomy field.
        
        $sku = $getVal($row, 'Variant SKU');
        $barcode = $getVal($row, 'Variant Barcode');

        // Option 1
        $opt1Name = $isFirst ? $getVal($row, 'Option1 Name') : '';
        $opt1Val = $getVal($row, 'Option1 Value');
        if ($opt1Val === 'Default Title') {
            // Default Shopify values
            $opt1Name = '';
            $opt1Val = '';
        }
        $opt1Linked = '';
        if ($isFirst && in_array(strtolower($opt1Name), $colorTranslations)) {
            $opt1Linked = 'product.metafields.shopify.color-pattern';
        }

        // Option 2
        $opt2Name = $isFirst ? $getVal($row, 'Option2 Name') : '';
        $opt2Val = $getVal($row, 'Option2 Value');
        $opt2Linked = '';
        if ($isFirst && in_array(strtolower($opt2Name), $colorTranslations)) {
            $opt2Linked = 'product.metafields.shopify.color-pattern';
        }

        // Option 3
        $opt3Name = $isFirst ? $getVal($row, 'Option3 Name') : '';
        $opt3Val = $getVal($row, 'Option3 Value');
        $opt3Linked = '';
        if ($isFirst && in_array(strtolower($opt3Name), $colorTranslations)) {
            $opt3Linked = 'product.metafields.shopify.color-pattern';
        }

        $price = $getVal($row, 'Variant Price');
        $comparePrice = $getVal($row, 'Variant Compare At Price');
        $cost = $getVal($row, 'Cost per item');
        $chargeTax = $formatBool($getVal($row, 'Variant Taxable'));
        
        $taxCode = ''; // Leaving blank as it's not mapped from Shopify CSV

        $inventoryTracker = $getVal($row, 'Variant Inventory Tracker');
        $inventoryQty = $getVal($row, 'Variant Inventory Qty');
        $continueSelling = $formatPolicy($getVal($row, 'Variant Inventory Policy'));

        $weightGrams = $getVal($row, 'Variant Grams');
        $weightUnit = $getVal($row, 'Variant Weight Unit');
        $requiresShipping = $formatBool($getVal($row, 'Variant Requires Shipping'));
        $fulfillmentService = $getVal($row, 'Variant Fulfillment Service');

        $imgUrl = $getVal($row, 'Image Src');
        $imgPos = $getVal($row, 'Image Position');
        $imgAlt = $getVal($row, 'Image Alt Text');
        $variantImgUrl = $getVal($row, 'Variant Image');

        // Output array corresponding to targetHeaders order
        $outputRow = [
            $title,                     // Title
            $handle,                    // URL handle
            $description,               // Description
            $vendor,                    // Vendor
            $productCategory,           // Product category
            $type,                      // Type
            $tags,                      // Tags
            $published,                 // Published on online store
            $status,                    // Status
            $sku,                       // SKU
            $barcode,                   // Barcode
            $opt1Name,                  // Option1 name
            $opt1Val,                   // Option1 value
            $opt1Linked,                // Option1 Linked To
            $opt2Name,                  // Option2 name
            $opt2Val,                   // Option2 value
            $opt2Linked,                // Option2 Linked To
            $opt3Name,                  // Option3 name
            $opt3Val,                   // Option3 value
            $opt3Linked,                // Option3 Linked To
            $price,                     // Price
            $comparePrice,              // Compare-at price
            $cost,                      // Cost per item
            $chargeTax,                 // Charge tax
            $taxCode,                   // Tax code
            '',                         // Unit price total measure
            '',                         // Unit price total measure unit
            '',                         // Unit price base measure
            '',                         // Unit price base measure unit
            $inventoryTracker,          // Inventory tracker
            $inventoryQty,              // Inventory quantity
            $continueSelling,           // Continue selling when out of stock
            $weightGrams,               // Weight value (grams)
            $weightUnit,                // Weight unit for display
            $requiresShipping,          // Requires shipping
            $fulfillmentService,        // Fulfillment service
            $imgUrl,                    // Product image URL
            $imgPos,                    // Image position
            $imgAlt,                    // Image alt text
            $variantImgUrl,             // Variant image URL
            $giftCard,                  // Gift card
            $seoTitle,                  // SEO title
            $seoDesc,                   // SEO description
            $isFirst ? $colorsStr : '', // Color (product.metafields.shopify.color-pattern)
            '',                         // Google Shopping / Google product category
            '',                         // Google Shopping / Gender
            '',                         // Google Shopping / Age group
            '',                         // Google Shopping / Manufacturer part number (MPN)
            '',                         // Google Shopping / Ad group name
            '',                         // Google Shopping / Ads labels
            '',                         // Google Shopping / Condition
            '',                         // Google Shopping / Custom product
            '',                         // Google Shopping / Custom label 0
            '',                         // Google Shopping / Custom label 1
            '',                         // Google Shopping / Custom label 2
            '',                         // Google Shopping / Custom label 3
            '',                         // Google Shopping / Custom label 4
        ];

        fputcsv($destFp, $outputRow, ',', '"', '\\');
    }
};

$currentHandle = null;
$buffer = [];
$rowCount = 0;
$convertedCount = 0;

while (($row = fgetcsv($srcFp, 0, ',', '"', '\\')) !== false) {
    $rowCount++;
    $handle = $getVal($row, 'Handle');

    if ($handle === '') {
        // If there's no handle, skip or treat as part of current if we are in one
        continue;
    }

    if ($currentHandle !== null && $handle !== $currentHandle) {
        // Flush previous handle buffer
        $flushBuffer($buffer);
        $convertedCount += count($buffer);
        $buffer = [];
    }

    $currentHandle = $handle;
    $buffer[] = $row;
}

// Flush last handle buffer
if (!empty($buffer)) {
    $flushBuffer($buffer);
    $convertedCount += count($buffer);
}

fclose($srcFp);
fclose($destFp);

echo "Successfully converted $convertedCount rows from Shopify format to template format.\n";
echo "Output saved to: $outputFile\n";
