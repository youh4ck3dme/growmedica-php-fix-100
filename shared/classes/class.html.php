<?php

class HTML {

    static function createHeader() {
        global $Row;
        global $user;
        global $MODULE_HEADER;
        global $MODULE_INLINE_JS;
        global $MODULE_FOOTER;
        //global $MODULE_TITLE;
        //global $MODULE_DESCRIPTION;
        //global $MODULE_KEYWORDS;

        $nav = new Navigation($Row["menu_id"]);

        $output = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . n;
        $output .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . n;
        $output .= '<title>' . $nav->get_metainfo($Row, "PAGE_TITLE") . '</title>' . n;
        $output .= '<meta name="Description" content="' . $nav->get_metainfo($Row, "DESCRIPTION") . '">' . n;
        $output .= '<meta name="Keywords" content="' . (!empty($Row[$_SESSION["lang"] . "_product_keywords"]) ? trim(preg_replace('/\s\s+/', ' ', $Row[$_SESSION["lang"] . "_product_keywords"])) : $nav->get_metainfo($Row, "KEYWORDS")) . '">' . n;
        $output .= '<meta name="robots" content="index, follow">' . n;
        $output .= '<meta name="author" content="' . PROJECT_NAME . '">' . n;
        $output .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . n;
        //$output .= '<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">' . n;
        //$output .= '<meta content="width=1024" name="viewport">' . n;
        //$output .= '<meta http-equiv="Cache-control" content="public">' . n;
        $output .= '<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">' . n;
        $output .= '<meta http-equiv="Pragma" content="no-cache">' . n;
        $output .= '<meta http-equiv="Expires" content="0">' . n;

        $output .= '<base href="' . ROOTDIR . '/" />' . n;

        // Preload first slideshow image if slideshow is active
        $sql_slide = "SELECT src FROM " . TABLE_PREFIX . "menu_slideshow_prepojenie AS p 
                      LEFT JOIN " . TABLE_PREFIX . "menu_slideshow AS s USING (menu_slideshow_id) 
                      WHERE p.menu_id = '" . $Row["menu_id"] . "' AND s.src IS NOT NULL 
                      ORDER BY p.sorter ASC LIMIT 1;";
        $query_slide = @mysql_query($sql_slide);
        if ($query_slide && mysql_num_rows($query_slide) > 0) {
            $slide_row = mysql_fetch_assoc($query_slide);
            $output .= '<link rel="preload" as="image" href="photos/slideshow/' . $slide_row['src'] . '" fetchpriority="high">' . n;
        }

        $output .= '<link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-touch-icon.png">' . n;
        $output .= '<link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">' . n;
        $output .= '<link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">' . n;
        $output .= '<link rel="manifest" href="images/favicon/site.webmanifest">' . n;
        $output .= '<link rel="mask-icon" href="images/favicon/safari-pinned-tab.svg" color="#ffcc00">' . n;
        $output .= '<meta name="msapplication-TileColor" content="#ffffff">' . n;
        $output .= '<meta name="theme-color" content="#ffffff">' . n;

        $output .= '<!-- JS -->' . n;
        $output .= '<script type="text/javascript" src="js/jquery/jquery-1.12.4.min.js"></script>' . n;
        $output .= '<script type="text/javascript" src="js/jquery/jquery-ui-1.12.0/jquery-ui.min.js" defer></script>' . n;
        $output .= '<script type="text/javascript" src="js/jquery.waypoints.min.js" defer></script>' . n;;
        $output .= '<script type="text/javascript" src="js/bootstrap.5.3.3/js/bootstrap.bundle.min.js" defer></script>' . n;
        $output .= '<script type="text/javascript" src="js/jquery/slimmenu/jquery.slimmenu.min.js" defer></script>' . n;
        $output .= '<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4_patch.js" defer></script>' . n;
        $output .= '<script type="text/javascript" src="js/form-validator.js" defer></script>' . n;
        $output .= '<script type="text/javascript" src="js/slick/slick.min.js" defer></script>' . n;
        $output .= '<script type="text/javascript" src="js/jquery.matchHeight.js" defer></script>' . n;        
        $output .= '<script type="text/javascript" src="js/bootbox/bootbox.all.min.js" defer></script>' . n;

        $output .= '<!-- CSS -->' . n;
        $output .= '<link rel="stylesheet" type="text/css" href="js/bootstrap.5.3.3/css/bootstrap.min.css">' . n;
        //$output .= '<link rel="stylesheet" type="text/css" href="js/bootstrap/css/bootstrap-modal.css">' . n;
        //$output .= '<link rel="stylesheet" type="text/css" href="' . fileWithLastChange('css/bootstrap.extension.css') . '">' . n;
        $output .= '<link rel="stylesheet" type="text/css" href="js/jquery/slimmenu/slimmenu.min.css">' . n;
        $output .= '<link rel="stylesheet" type="text/css" href="css/animate.min.css" />' . n;
        $output .= '<link rel="stylesheet" type="text/css" href="js/slick/slick.css">' . n;
        $output .= '<link rel="stylesheet" type="text/css" href="js/slick/slick-theme.css">' . n;
        $output .= '<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-1.3.4.css">' . n;
        //$output .= '<link rel="stylesheet" type="text/css" href="fonts/fontello/css/objednavaj.css?v=20170302">' . n;
        //$output .= '<link rel="stylesheet" type="text/css" href="fonts/fontello/css/animation.css">' . n;
        //$output .= '<!--[if IE 7]><link rel="stylesheet" type="text/css" href="fonts/fontello/css/objednavaj-ie7.css"><![endif]-->' . n;
        $output .= '<link rel="stylesheet" type="text/css" href="' . fileWithLastChange('index.css') . '" />' . n;
        if ($user->isAdmin()) {
            $output .= '<script type="text/javascript" src="js/functions-admin.js" async="async"></script>' . n;
        }
        $output .= '<!--[if lt IE 9]>' . n;
        $output .= '<script>' . n;
        $output .= 'document.createElement(\'header\');' . n;
        $output .= 'document.createElement(\'nav\');' . n;
        $output .= 'document.createElement(\'section\');' . n;
        $output .= 'document.createElement(\'article\');' . n;
        $output .= 'document.createElement(\'aside\');' . n;
        $output .= 'document.createElement(\'footer\');' . n;
        $output .= '</script>' . n;
        $output .= '![endif]-->' . n;
        $output .= (!empty($MODULE_HEADER) ? '<!-- SUBORY VYUZIVANE MODULOM -->' . n . $MODULE_HEADER . n : '');
        if (!empty($MODULE_INLINE_JS)) {
            $output .= '<!-- POTREBNE JS ACTION MODULU -->' . n;
            $output .= '<script type="text/javascript">' . n;
            $output .= '<!--' . n;
            $output .= $MODULE_INLINE_JS . n;
            $output .= '//-->' . n;
            $output .= '</script>' . n;
        }        
        $output .= '<link rel="stylesheet" type="text/css" href="' . fileWithLastChange('mobile.css') . '" />' . n;

        // --- SEO SCHEMA INJECTION ---
        $fullDomain = ROOTDIR;
        
        $organizationSchema = array(
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => "GrowMedica s.r.o.",
            "url" => $fullDomain,
            "logo" => $fullDomain . "/images/favicon/android-chrome-512x512.png",
            "contactPoint" => array(
                array(
                    "@type" => "ContactPoint",
                    "email" => "info@growmedica.sk",
                    "contactType" => "customer service",
                    "name" => "Zákaznícka linka"
                )
            ),
            "address" => array(
                "@type" => "PostalAddress",
                "streetAddress" => "BELLOVA 6",
                "addressLocality" => "KOŠICE",
                "postalCode" => "040 01",
                "addressCountry" => "SK"
            )
        );
        
        $websiteSchema = array(
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => "GrowMedical",
            "url" => $fullDomain,
            "potentialAction" => array(
                "@type" => "SearchAction",
                "target" => array(
                    "@type" => "EntryPoint",
                    "urlTemplate" => $fullDomain . "/sk/hlavna-stranka/vyhladavanie?q={search_term_string}"
                ),
                "query-input" => "required name=search_term_string"
            )
        );
        
        $output .= '<!-- SEO SCHEMAS -->' . n;
        $output .= '<script type="application/ld+json">' . n . json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . n . '</script>' . n;
        $output .= '<script type="application/ld+json">' . n . json_encode($websiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . n . '</script>' . n;

        return $output;
    }

    static function createFooter() {
        //$output = '<script type="text/javascript" src="js/bootbox/bootbox.min.js"></script>' . n;
        $output .= (!empty($MODULE_HEADER) ? $MODULE_HEADER . n : '');
        $output .= '<script type="text/javascript" src="' . fileWithLastChange('js/site.js') . '"></script>' . n;
        return $output;
    }

    public function createPageContent() {
        global $menuPos;
        global $menuUrl;
        global $Row;

        if (preg_match("([A-Za-z]{2})", $_GET['param'], $parts)) {
            //$_SESSION['lang'] = $parts[0];
            $parts[0] = $_SESSION['lang'];
            $parts[1] = substr($_GET['param'], 3);
            $tparts = explode("/", $parts[1]);

            $queryInclude = array();
            for ($i = 0; $i <= sizeof(explode("/", $parts[1])); $i++) {
                if (strlen(implode("/", $tparts)) > 0) {
                    $queryInclude[] .= "`m`.`" . $parts[0] . "_name_seo` = '" . safetyMysql(implode("/", $tparts)) . "'";
                }
                array_pop($tparts);
            }

            $queryString = "SELECT mo.*, m.*, m." . $parts[0] . "_content AS content FROM " . TABLE_PREFIX . "menu AS m
                            LEFT JOIN " . TABLE_PREFIX . "module AS mo USING (module_id)
                            WHERE 1 AND (" . implode(" OR ", $queryInclude) . ")
                            ORDER BY char_length(" . $parts[0] . "_name_seo)
                            DESC LIMIT 1;";

            if ($Result = mysql_query($queryString)) {
                if (mysql_num_rows($Result) == 1) {
                    $Row = mysql_fetch_assoc($Result);
                    if (is_numeric($Row["redirect_to"])) {
                        $queryString = "SELECT mo.*, m.*, m." . $parts[0] . "_content AS content FROM " . TABLE_PREFIX . "menu AS m
                                        LEFT JOIN " . TABLE_PREFIX . "module AS mo USING (module_id)
                                        WHERE 1 AND menu_id = '" . $Row["redirect_to"] . "';";

                        if (!$ResultQ = mysql_query($queryString)) {
                            if (mysql_errno())
                                print("MySql Error (" . mysql_errno() . "): "
                                        . mysql_error());
                        }else {
                            if (mysql_num_rows($ResultQ) == 1) {
                                $Row = mysql_fetch_assoc($ResultQ);
                                $_SERVER["REQUEST_URI"] = reset(explode($_SESSION["lang"] . "/", $_SERVER["REQUEST_URI"])) . $_SESSION["lang"] . "/" . $Row[strtolower($_SESSION["lang"]) . "_name_seo"];
                            }
                        }
                    }
                    $menuPos = strpos($_SERVER['REQUEST_URI'], ($Row[strtolower($_SESSION['lang']) . '_name_seo'])) + strlen($Row[strtolower($_SESSION['lang']) . '_name_seo']);
                    $menuUrl = $Row[strtolower($_SESSION['lang']) . '_name_seo'];
                } else {
                    header('Location: ' . ROOTDIR . '/' . Menu::getHyperlinkById(NOT_FOUND_PAGE_ID));
                    exit;
                }
            } else {
                header('Location: ' . ROOTDIR . '/' . Menu::getHyperlinkById(NOT_FOUND_PAGE_ID));
                exit;
                if (mysql_errno())
                    die("MySql Error (" . mysql_errno() . "): "
                            . mysql_error() . "<br />");
            }
        }

        if (sizeof($parts) == 0 or empty($parts[1])) {
            $queryString = "select mo.*, m.*, m." . strtolower($_SESSION['lang']) . "_content as content from " . TABLE_PREFIX . "menu as m left join " . TABLE_PREFIX . "module as mo using (module_id) where 1 and m.default_content = '1';";
            if ($Result = mysql_query($queryString)) {
                if (mysql_num_rows($Result) == 1) {
                    $Row = mysql_fetch_assoc($Result);
                    if (is_numeric($Row["redirect_to"])) {
                        $queryString = "select mo.*, m.*, m." . strtolower($_SESSION['lang']) . "_content as content from " . TABLE_PREFIX . "menu as m left join " . TABLE_PREFIX . "module as mo using (module_id)  where 1 and menu_id = '" . $Row["redirect_to"] . "';";

                        if (!$ResultQ = mysql_query($queryString)) {
                            if (mysql_errno())
                                print("MySql Error (" . mysql_errno() . "): "
                                        . mysql_error());
                        }else {
                            if (mysql_num_rows($ResultQ) == 1) {
                                $Row = mysql_fetch_assoc($ResultQ);
                            }
                        }
                    }
                    $_SERVER["REQUEST_URI"] = reset(explode($_SESSION["lang"] . "/", $_SERVER["REQUEST_URI"])) . $_SESSION["lang"] . "/" . $Row[strtolower($_SESSION["lang"]) . "_name_seo"];
                    $menuPos = strpos($_SERVER['REQUEST_URI'], ($Row[strtolower($_SESSION['lang']) . '_name_seo'])) + strlen($Row[strtolower($_SESSION['lang']) . '_name_seo']);
                    $menuUrl = $Row[strtolower($_SESSION['lang']) . '_name_seo'];
                }
            } else {
                if (mysql_errno())
                    die("MySql Error (" . mysql_errno() . "): "
                            . mysql_error() . "<br />");
            }
        }
    }

}

?>
