<?php
/**
 * split_csv.php
 *
 * Splits product_template.csv into smaller chunks (max 10MB each)
 * to respect Shopify's 15MB upload limit, keeping variants of the same
 * handle grouped together.
 */

$sourceFile = __DIR__ . '/product_template.csv';
$maxBytes = 4 * 1024 * 1024; // 4 MB

if (!file_exists($sourceFile)) {
    echo "Error: Source file '$sourceFile' not found.\n";
    exit(1);
}

$srcFp = fopen($sourceFile, 'r');
if (!$srcFp) {
    echo "Error: Cannot open '$sourceFile'.\n";
    exit(1);
}

$header = fgetcsv($srcFp, 0, ',', '"', '\\');
if (!$header) {
    echo "Error: Empty source file.\n";
    exit(1);
}

// Function to write a chunk to a CSV file
$writePart = function($partNum, $header, $rows) {
    $filename = __DIR__ . "/product_template_part_{$partNum}.csv";
    $fp = fopen($filename, 'w');
    if (!$fp) {
        echo "Error: Cannot write to $filename\n";
        return false;
    }
    
    // Write header
    fputcsv($fp, $header, ',', '"', '\\');
    
    // Write rows
    foreach ($rows as $row) {
        fputcsv($fp, $row, ',', '"', '\\');
    }
    
    fclose($fp);
    return $filename;
};

$currentPart = 1;
$partRows = [];
$partBytesEst = 0;

// Read and group by Handle
$groups = [];
while (($row = fgetcsv($srcFp, 0, ',', '"', '\\')) !== false) {
    $handle = $row[1]; // URL handle is column 2 (index 1)
    
    if ($handle === '') {
        continue;
    }
    
    if (!isset($groups[$handle])) {
        $groups[$handle] = [];
    }
    $groups[$handle][] = $row;
}
fclose($srcFp);

echo "Loaded " . count($groups) . " product groups.\n";

$createdFiles = [];

foreach ($groups as $handle => $rows) {
    // Estimate size of this group when written to CSV
    $groupBytes = 0;
    foreach ($rows as $row) {
        // rough estimate by joining with commas and adding newline
        $groupBytes += strlen(implode(',', $row)) + 10; // offset for quotes
    }
    
    // Check if adding this group exceeds the max size limit
    if ($partBytesEst + $groupBytes > $maxBytes && !empty($partRows)) {
        // Write the current part
        $file = $writePart($currentPart, $header, $partRows);
        if ($file) {
            $createdFiles[] = $file;
            echo "Created: " . basename($file) . " (" . number_format($partBytesEst / (1024 * 1024), 2) . " MB)\n";
        }
        $currentPart++;
        $partRows = [];
        $partBytesEst = 0;
    }
    
    // Append to current part
    foreach ($rows as $row) {
        $partRows[] = $row;
    }
    $partBytesEst += $groupBytes;
}

// Write the last part
if (!empty($partRows)) {
    $file = $writePart($currentPart, $header, $partRows);
    if ($file) {
        $createdFiles[] = $file;
        echo "Created: " . basename($file) . " (" . number_format($partBytesEst / (1024 * 1024), 2) . " MB)\n";
    }
}

echo "Successfully split CSV into " . count($createdFiles) . " parts.\n";
