<?php
/**
 * Shopify Products Exporter Utility
 *
 * This script exports products from the custom legacy DB into a Shopify-compatible CSV.
 * It can be run from the command line or directly via browser by an admin.
 */

require_once(__DIR__ . "/../shared/config.inc.php");

// Restrict access to CLI or Authenticated Admin
if (php_sapi_name() !== 'cli' && (empty($user) || !$user->isAdmin())) {
    header('HTTP/1.0 403 Forbidden');
    die("Access Denied: Only administrators can access this export tool.");
}

// Set limits to handle larger datasets
ini_set('memory_limit', '1024M');
set_time_limit(0);

// Use output buffer or write directly to a CSV file
$filename = "shopify_products_" . date("Ymd_His") . ".csv";

// If run via CLI, save to file. If web, output as download
if (php_sapi_name() === 'cli') {
    $outputFile = __DIR__ . "/" . $filename;
    $output = fopen($outputFile, 'w');
    echo "Exporting products to $outputFile...\n";
} else {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'w');
}

// Shopify Standard Import CSV Headers
fputcsv($output, array(
    'Handle', 'Title', 'Body (HTML)', 'Vendor', 'Type', 'Tags', 'Published',
    'Option1 Name', 'Option1 Value', 'Option2 Name', 'Option2 Value', 'Option3 Name', 'Option3 Value',
    'Variant SKU', 'Variant Grams', 'Variant Inventory Tracker', 'Variant Inventory Qty',
    'Variant Inventory Policy', 'Variant Fulfillment Service', 'Variant Price', 'Variant Compare At Price',
    'Variant Requires Shipping', 'Variant Taxable', 'Variant Barcode', 'Image Src', 'Image Position',
    'Image Alt Text', 'Gift Card', 'SEO Title', 'SEO Description', 'Variant Image', 'Variant Weight Unit',
    'Cost per item', 'Status'
));

try {
    // Determine target table prefix
    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'growmedica_';
    
    // Check if class String exists, otherwise define a fallback function
    $seoHelper = function($text) {
        if (class_exists('String') && method_exists('String', 'SEOFriendlyText')) {
            return \String::SEOFriendlyText($text);
        }
        // Basic fallback slugifier
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    };

    // 1. Fetch all menus (categories) to construct cache
    $menus = array();
    $menu_stmt = $db->query("SELECT menu_id, child_of, sk_name FROM `{$prefix}menu` WHERE sk_name IS NOT NULL");
    while ($m_row = $menu_stmt->fetch(PDO::FETCH_ASSOC)) {
        $menus[$m_row['menu_id']] = array(
            'id' => $m_row['menu_id'],
            'parent' => $m_row['child_of'],
            'name' => trim($m_row['sk_name'])
        );
    }

    // 2. Define helper to get product Type and Tags from categories
    $getProductTypeAndTags = function($productId) use ($db, $prefix, $menus) {
        $pm_stmt = $db->prepare("SELECT menu_id FROM `{$prefix}product_menu` WHERE product_id = :product_id");
        $pm_stmt->execute(array(':product_id' => $productId));
        $menuIds = $pm_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $genericNames = array(
            'obchod', 'zdravie', 'e-shop', 'eshop', 'hlavná stránka', 
            'hlavná ponuka', 'hlavné menu', 'hlavna stranka', 'hlavne menu'
        );
        
        $allTags = array();
        $primaryType = 'Product';
        
        foreach ($menuIds as $menuId) {
            $currId = $menuId;
            $isDirect = true;
            while (isset($menus[$currId])) {
                $name = $menus[$currId]['name'];
                $lowerName = mb_strtolower($name, 'UTF-8');
                if (!in_array($lowerName, $genericNames) && !empty($name)) {
                    $allTags[] = $name;
                    if ($isDirect && $primaryType === 'Product') {
                        $primaryType = $name;
                    }
                }
                $currId = $menus[$currId]['parent'];
                $isDirect = false;
            }
        }
        
        return array(
            'type' => $primaryType,
            'tags' => array_unique($allTags)
        );
    };

    // Query to pull products, joining with manufacturer/vendor
    $query = "SELECT p.*, m.sk_name as vendor_name 
              FROM `{$prefix}product` p 
              LEFT JOIN `{$prefix}manufacturer` m ON p.manufacturer_id = m.manufacturer_id 
              WHERE p.deleted = '0'";
              
    $stmt = $db->query($query);
    $count = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $handle = !empty($row['sk_name_seo']) ? $row['sk_name_seo'] : $seoHelper($row['sk_name']);
        $title = $row['sk_name'];
        $body = html_entity_decode($row['sk_description'], ENT_QUOTES, "UTF-8");
        $vendor = !empty($row['vendor_name']) ? $row['vendor_name'] : PROJECT_NAME;
        $published = ($row['available'] == '1') ? 'true' : 'false';
        $sku = $row['code_1'];
        $barcode = !empty($row['code_ean']) ? $row['code_ean'] : '';
        
        // Skladom is 1/0, map to basic quantities
        $qty = ($row['skladom'] == '1') ? '10' : '0';
        $price = $row['price'];
        $compare_price = ($row['price_old'] > 0 && $row['price_old'] > $row['price']) ? $row['price_old'] : '';
        
        // Use production URL for Shopify imports to successfully fetch images
        $image_base = 'https://growmedica.sk';
        $image_src = !empty($row['image_src']) ? $image_base . '/photos/original/' . $row['image_src'] : '';
        
        $status = ($row['available'] == '1') ? 'active' : 'draft';
        
        // Resolve categories to Shopify Type and Tags
        $catData = $getProductTypeAndTags($row['product_id']);
        $productType = $catData['type'];
        
        // Merge category tags with product keywords
        $keywords = $row['sk_keywords'];
        $keywords = str_replace(array("\r", "\n"), ", ", $keywords);
        $rawTags = explode(',', $keywords);
        
        $tagsArray = $catData['tags'];
        foreach ($rawTags as $t) {
            $t = trim($t);
            if (!empty($t)) {
                $tagsArray[] = $t;
            }
        }
        $tags = implode(', ', array_unique($tagsArray));
        
        fputcsv($output, array(
            $handle,                 // Handle
            $title,                  // Title
            $body,                   // Body (HTML)
            $vendor,                 // Vendor
            $productType,            // Type
            $tags,                   // Tags
            $published,              // Published
            'Title',                 // Option1 Name
            'Default Title',         // Option1 Value
            '',                      // Option2 Name
            '',                      // Option2 Value
            '',                      // Option3 Name
            '',                      // Option3 Value
            $sku,                    // Variant SKU
            '0',                     // Variant Grams
            'shopify',               // Variant Inventory Tracker
            $qty,                    // Variant Inventory Qty
            'deny',                  // Variant Inventory Policy
            'manual',                // Variant Fulfillment Service
            $price,                  // Variant Price
            $compare_price,          // Variant Compare At Price
            'true',                  // Variant Requires Shipping
            'true',                  // Variant Taxable
            $barcode,                // Variant Barcode
            $image_src,              // Image Src
            '1',                     // Image Position
            $title,                  // Image Alt Text
            'false',                 // Gift Card
            $row['sk_name'],         // SEO Title
            '',                      // SEO Description
            '',                      // Variant Image
            'kg',                    // Variant Weight Unit
            '',                      // Cost per item
            $status                  // Status
        ));
        $count++;
    }
    
    fclose($output);
    
    if (php_sapi_name() === 'cli') {
        echo "Successfully exported $count products to Shopify CSV format.\n";
    }

} catch (Exception $e) {
    fclose($output);
    if (php_sapi_name() === 'cli') {
        echo "Error during export: " . $e->getMessage() . "\n";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
