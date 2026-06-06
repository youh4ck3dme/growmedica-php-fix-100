<?

class Navigation {

    private $navParts = array();

    public function Navigation($menu_id = NULL) {
        $this->_getNode($menu_id);
        $this->navParts = array_reverse($this->navParts);
    }

    private function _getNode($menu_id = NULL) {
        $queryString = "select private, " . strtolower($_SESSION['lang']) . "_name_seo as name_seo, " . strtolower($_SESSION['lang']) . "_name as name, menu_id, child_of from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $menu_id . "' and child_of is not null;";
        $Result = mysql_query($queryString);
        if ($Result) {
            if (mysql_num_rows($Result) == 1) {
                $Row = mysql_fetch_assoc($Result);

                if (sizeof($this->navParts) == 0 and $Row['hidden_item'] == 1) {
                    $this->_getNode($Row['child_of']);
                } else {
                    $this->navParts[] = '<a href="./' . strtolower($_SESSION['lang']) . '/' . $Row['name_seo'] . '">' . $Row['name'] . '</a>';
                    $this->_getNode($Row['child_of']);
                }
            }
        } else {
            if (mysql_errno())
                die("MySql Error (" . mysql_errno() . "): "
                        . mysql_error() . "<br />");
        }
        mysql_free_result($Result);
    }

    public function getData() {
        return implode("&nbsp;&nbsp;&gt;&nbsp;&nbsp;", $this->navParts);
    }

    // DOPLNENE DOPLNKOVE FUNC
    public function get_metainfo($Row = NULL, $fieldName = "page_title", $end = true) {
        global $meta;
        global $lang;
        global $MODULE_TITLE;
        global $MODULE_DESCRIPTION;
        global $MODULE_KEYWORDS;

        if (ADVANCED_META == '1') {
            if ($Row['child_of'] != '1') { // pokročilé META
                // META TITLE
                if ($fieldName == "PAGE_TITLE") {
                    if (!empty($Row[$lang . "_" . strtolower($fieldName)])) {
                        $output = $Row[$lang . '_' . strtolower($fieldName)];
                    } else {
                        if (!empty($MODULE_TITLE)) {
                            $output = $MODULE_TITLE;
                        } else {
                            $output = $Row[$lang . '_name'];
                        }
                    }
                    if ($end == true) {
                        $query = 'SELECT ' . $lang . '_name AS name FROM ' . TABLE_PREFIX . 'menu WHERE menu_id IN (' . str_replace(';', ',', $Row['left_menu_id']) . ',' . $Row['menu_id'] . ') AND menu_id!="1" AND menu_id!="' . ESHOP_MAIN_CATEGORY . '" ORDER BY char_length(right_menu_id) ASC;';
                        $result = mysql_query($query);
                        while ($row = mysql_fetch_assoc($result)) {
                            $parents[] = $row['name'];
                        }
                        mysql_free_result($result);
                        $temp_output = $output . META_TITLE_LINKER . implode(META_TITLE_LINKER, $parents) . META_TITLE_LINKER . $meta[$lang][strtoupper($fieldName)];
                        if (substr_count($temp_output, $output) != '1') {
                            $output = implode(META_TITLE_LINKER, $parents) . META_TITLE_LINKER . $meta[$lang][strtoupper($fieldName)];
                        } else {
                            $output = $temp_output;
                        }
                    }
                }
                // META TITLE END
                //
                // META DESCRIPTION
                if ($fieldName == "DESCRIPTION") {
                    if (!empty($Row[$lang . '_product_' . strtolower($fieldName)])) {
                        $output = truncate(str_replace("\n", ' ', strip_tags($Row[$lang . '_product_' . strtolower($fieldName)])), '150', '.');
                    } else {
                        if (!empty($Row[$lang . '_' . strtolower($fieldName)])) {
                            $output = str_replace("\n", ' ', strip_tags($Row[$lang . '_' . strtolower($fieldName)]));
                        } elseif ($Row[$lang . '_content']) {
                            $output = truncate(str_replace("\n", ' ', strip_tags($Row[$lang . '_content'])), '150', '.');
                        } else {
                            $output = $meta[$lang][strtoupper($fieldName)];
                        }
                    }
                    $output = $this->get_metainfo($Row, 'PAGE_TITLE', false) . '. ' . $output;
                }
                // META DESCRIPTION END
                //
                // META KEYWORDS
                if ($fieldName == "KEYWORDS") {
                    if (!empty($Row[$lang . '_product_' . strtolower($fieldName)])) {
                        foreach (explode(', ', $Row[$lang . '_product_' . strtolower($fieldName)]) as $value) {
                            $output[] = $value;
                        }
                    } elseif (!empty($MODULE_KEYWORDS)) {
                        $output[] = $MODULE_KEYWORDS;
                    } else {
                        $output[] = $meta[$lang][strtoupper($fieldName)];
                    }

                    $query = 'SELECT ' . $lang . '_keywords AS keywords FROM ' . TABLE_PREFIX . 'menu WHERE menu_id IN (' . str_replace(';', ',', $Row['left_menu_id']) . ',' . $Row['menu_id'] . ') AND menu_id!="1" ORDER BY menu_id DESC;';
                    $result = mysql_query($query);
                    while ($row = mysql_fetch_assoc($result)) {
                        foreach (explode(' ', $row['keywords']) as $value) {
                            $output[] = $value;
                        }
                    }
                    mysql_free_result($result);
                    $output[] = $this->get_metainfo($Row, 'PAGE_TITLE', false);
                    $output = array_unique($output);
                    $output = implode(', ', array_slice($output, 0 , 10));
                }
                // META KEYWORDS END
            } else { // základné META
                // META TITLE
                if ($fieldName == "PAGE_TITLE") {
                    if (!empty($Row[$lang . "_" . strtolower($fieldName)])) {
                        $output = $Row[$lang . '_' . strtolower($fieldName)];
                    } else {
                        if (!empty($MODULE_TITLE)) {
                            $output = $MODULE_TITLE;
                        } else {
                            $output = $Row[$lang . '_name'];
                        }
                    }
                    if ($end == true) {
                        $output = $output . META_TITLE_LINKER . $meta[$lang][strtoupper($fieldName)];
                    }
                }
                // META TITLE END
                //
                // META DESCRIPTION
                if ($fieldName == "DESCRIPTION") {
                    if (!empty($Row[$lang . '_' . strtolower($fieldName)])) {
                        $output = str_replace("\n", ' ', strip_tags($Row[$lang . '_' . strtolower($fieldName)]));
                    } else {
                        if (!empty($MODULE_DESCRIPTION)) {
                            $output = $MODULE_DESCRIPTION;
                        } else {
                            $output = truncate(str_replace("\n", ' ', strip_tags($Row[$lang . '_content'])), '150', '.');
                        }
                    }
                    if (strlen($output) > '50') {
                        $output = $this->get_metainfo($Row, 'PAGE_TITLE', false) . '. ' . $output;
                    } else {
                        $output = $meta[$lang][strtoupper($fieldName)];
                    }
                }
                // META DESCRIPTION END
                //
                // META KEYWORDS
                if ($fieldName == "KEYWORDS") {
                    if (!empty($Row[$lang . '_' . strtolower($fieldName)])) {
                        $output = $Row[$lang . '_' . strtolower($fieldName)];
                    } else {
                        $output = $MODULE_KEYWORDS;
                    }
                    if (strlen($output) > '50') {
                        $output = $this->get_metainfo($Row, 'PAGE_TITLE', false) . ' ' . $output;
                    } else {
                        $output = $meta[$lang][strtoupper($fieldName)];
                    }
                }
                // META KEYWORDS END
            }
            return $output;


            /*
              if (@ereg("([A-Za-z]{2})", $_GET['param'], $parts)) {
              $parts[0] = $_SESSION['lang'];
              $parts[1] = substr($_GET['param'], 3);
              $tparts = explode("/", $parts[1]);

              $queryInclude = array();
              for ($i = 0; $i <= sizeof(explode("/", $parts[1])); $i++) {
              if (strlen(implode("/", $tparts)) > 0) {
              $queryInclude[] .= $parts[0] . '_name_seo="' . safetyMysql(implode("/", $tparts)) . '"';
              }
              array_pop($tparts);
              }
              }
              $sql = 'SELECT ' . $lang . '_name AS title, ' . $lang . '_description AS description, ' . $lang . '_keywords AS keywords FROM ' . TABLE_PREFIX . 'menu
              WHERE (' . implode(" OR ", $queryInclude) . ');';
              $query = mysql_query($sql);
              $row = mysql_fetch_array($query);
              print_r($row);

              if ($fieldName == "PAGE_TITLE") { // META TITLE
              return ((isset($Row[$lang . "_" . strtolower($fieldName)]) and ! empty($Row[$lang . "_" . strtolower($fieldName)])) ? $Row[$lang . "_" . strtolower($fieldName)] . " :: " . $meta[$lang][strtoupper($fieldName)] : strip_tags($this->getData()) . " :: " . $meta[$lang][strtoupper($fieldName)]);
              }
              if ($fieldName == "DESCRIPTION") { // META DESCRIPTION
              if (empty($row['description'])) {
              if (empty($MODULE_DESCRIPTION)) {
              return $meta[$lang]['DESCRIPTION'];
              } else {
              return $MODULE_DESCRIPTION;
              }
              } else {
              $sentence = explode('. ', strip_tags($row['description']));
              }

              $i = 0;
              foreach ($sentence as $key => $txt) {
              if (strlen($txt) <= 10 AND $i == 0) {
              $short_sentence = $txt . '. ';
              } elseif ($i == 0) {
              $result[] = $txt . '.';
              }
              if (isset($short_sentence) AND $i != 0) {
              $result[] = $short_sentence . $txt . '.';
              unset($short_sentence);
              }
              $i++;
              }

              $t = htmlentities($result[0], null, 'utf-8');
              $r = str_replace(array(' ', '&nbsp;', '\n', 'Â'), array('', '', '', ''), $t);
              $r = html_entity_decode(trim($r));
              if ($r !== '.') {
              $MODULE_DESCRIPTION = $result[0];
              }
              return $MODULE_DESCRIPTION;
              }
              if ($fieldName == "KEYWORDS") { // META KEYWORDS
              return $MODULE_KEYWORDS;
              }
             */
        } else {
            if ($fieldName == "PAGE_TITLE" AND ! empty($MODULE_TITLE)) {
                return $MODULE_TITLE;
            }
            if ($fieldName == "DESCRIPTION" AND ! empty($MODULE_DESCRIPTION)) {
                return $MODULE_DESCRIPTION;
            }
            if ($fieldName == "KEYWORDS" AND ! empty($MODULE_KEYWORDS)) {
                return $MODULE_KEYWORDS;
            }
        }
        if ($fieldName == "PAGE_TITLE") {
            return ((isset($Row[$lang . "_" . strtolower($fieldName)]) and ! empty($Row[$lang . "_" . strtolower($fieldName)])) ? $Row[$lang . "_" . strtolower($fieldName)] : strip_tags($this->getData()) . " :: " . $meta[$lang][strtoupper($fieldName)]);
        } else {
            return ((isset($Row[$lang . "_" . strtolower($fieldName)]) and ! empty($Row[$lang . "_" . strtolower($fieldName)])) ? $Row[$lang . "_" . strtolower($fieldName)] : $meta[$lang][strtoupper($fieldName)]);
        }
    }

    public function get_tree($Row = NULL, $fieldName = "page_title") {
        global $meta;
        return ($this->getData());
    }

    public function get_rootdirname($rootdir = NULL) {
        $queryString = "select " . strtolower($_SESSION['lang']) . "_name as name from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $rootdir . "';";
        $Result = mysql_query($queryString);
        if ($Result) {
            if (mysql_num_rows($Result) == 1) {
                $Row = mysql_fetch_assoc($Result);
                return $Row['name'];
            }
        } else {
            if (mysql_errno())
                die("MySql Error (" . mysql_errno() . "): "
                        . mysql_error() . "<br />");
        }
        mysql_free_result($Result);
    }

}

?>