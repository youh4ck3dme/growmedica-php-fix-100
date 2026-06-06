<?php

class Menu {

    /**
     * getHyperLinkByID
     *
     * Užitočná funkcia, ktorá na základe zadaného ID názov podstránky, v akom je zadaný v databáze.
     * Nevýhodou je, že je viazaná na ID (ktoré sa až tak často nemení), výhodou je zase to, že sa
     * automaticky aktualizuje po zmene názvu podstránky.
     *
     * @param int		$id         id stránky, ktorej link potrebujeme
     *
     * @return link
     *
     */
    static function getHyperLinkByID($id = NULL) {
        if (is_numeric($id)) {
            $queryString = 'SELECT ' . $_SESSION["lang"] . '_name_seo AS name_seo FROM ' . TABLE_PREFIX . 'menu
                            WHERE 1 AND menu_id="' . $id . '";';
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                if (mysql_num_rows($Result) == 1) {
                    $Row = mysql_fetch_object($Result);
                    return $_SESSION["lang"] . '/' . $Row->name_seo;
                }
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $id . ')');
        }
    }

    /**
     * getHyperLinkTextByID
     *
     * Užitočná funkcia, ktorá na základe zadaného ID vygeneruje linku v tvare ROOT/sk/nazov1/nazov2 presne v tvare, v akom potrebujeme.
     * Nevýhodou je, že je viazaná na ID (ktoré sa až tak často nemení), výhodou je zase to, že sa automaticky
     * aktualizuje po zmene názvu podstránky.
     *
     * @param int		$id         id stránky, ktorej link potrebujeme
     *
     * @return link
     *
     */
    static function getHyperLinkTextByID($id = NULL) {
        if (is_numeric($id)) {
            $queryString = 'SELECT ' . $_SESSION["lang"] . '_name AS name FROM ' . TABLE_PREFIX . 'menu
                            WHERE 1 AND menu_id="' . $id . '";';
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                if (mysql_num_rows($Result) == 1) {
                    $Row = mysql_fetch_object($Result);
                    return $Row->name;
                }
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $id . ')');
        }
    }

    /**
     * menuHasChilds
     *
     * Užitočná funkcia, ktorá zisťuje či má položka deti.
     *
     * @param int		$parentId         id stránky, ktorej deti potrebujeme zistiť
     *
     * @return bool
     *
     */
    static function menuHasChilds($parentId = NULL) {

        if (is_numeric($parentId)) {
            $query = 'SELECT ' . $_SESSION["lang"] . '_name_seo AS name_seo, ' . $_SESSION["lang"] . '_name AS name, menu_id FROM ' . TABLE_PREFIX . 'menu
                            WHERE 1 AND child_of="' . $parentId . '" AND private="0"
                            ORDER BY sorter ASC;';
            if (!$result = mysql_query($query)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                if (mysql_num_rows($result) > 0)
                    return true;
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $id . ')');
        }
        return false;
    }

    /**
     * getMyChilds
     *
     * Užitočná funkcia, ktorá vráti Id podstránok.
     *
     * @param int		$parentId         id stránky, ktorej deti potrebujeme zistiť
     *
     * @return array
     *
     */
    static function getMyChilds($parentId = NULL) {
        $output = array();
        if (is_numeric($parentId)) {
            $query = "SELECT " . $_SESSION["lang"] . "_name_seo AS name_seo, " . $_SESSION["lang"] . "_name AS name, menu_id FROM " . TABLE_PREFIX . "menu
                      WHERE 1 AND child_of = '" . $parentId . "' AND private = '0'
                      ORDER BY sorter ASC;";
            if (!$result = mysql_query($query)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                if (mysql_num_rows($result) > 0) {
                    while ($row = mysql_fetch_object($result)) {
                        array_push($output, $row->menu_id);
                    }
                }
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $id . ')');
        }
        return $output;
    }

    /**
     * returnMenu
     *
     * Funkcia vhodná na výpis bočného menu. Vráti menu ako nezoradený zoznam UL a LI.
     * V prípade, že je $childs ponechaný prázdny, vyberie len položky z vlastnej úrovne.
     * V opačnom prípade vylistuje aj podpoložky. Funkcia obsah automaticky vypisuje, nie je
     * potrebné používať funkciu print
     *
     * @param int		$parentId       id stránky, ktorej deti potrebujeme vypísať
     * @param bool		$childs         prepínač zobrazovania sub položiek
     * @param int		$navigateId     id aktívnej stránky
     * @param var		$class          názov klasy, ktorá je priradená k elementu <ul>
     * @param int		$divide         číslo podľa ktorého je menu delené
     *
     * @return bool
     *
     */
    static function returnMenu($parentId, $childs = false, $navigateId = "", $class = NULL, $divide = NULL, $extra = NULL, $selectedChilds = FALSE) {
        global $Row;
        $parent_pages = explode(';', $Row['left_menu_id']); // skúži na vypisanie všetkých aktívnych stránok
        $attach = $detach = array();
        if($extra) {
            $extraArray = explode(',', $extra);

            foreach ($extraArray as $key => $value) {
                if((int)trim($value) > 0)
                    $attach[] = (int)trim($value);
                else
                    $detach[] = -1 * (int)trim($value);
            }
        }

        if (is_numeric($parentId)) {
            $queryString = 'SELECT ' . $_SESSION["lang"] . '_name AS name, menu_id, child_of, right_menu_id FROM ' . TABLE_PREFIX . 'menu
                            WHERE 1 AND child_of="' . $parentId . '" AND ' . $_SESSION["lang"] . '_name <> "" and private="0"
                            ORDER BY sorter ASC;';
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                if (mysql_num_rows($Result) != '0' OR count($attach) > 0) {
                    $i = 0;
                    $output = '<ul' . ($class != NULL ? ' class="' . $class . '"' : '') . '>';
                    while ($Item = mysql_fetch_assoc($Result)) {
                        $i++;
                        $deti = explode(";", $Item['right_menu_id']); // strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id']))
                        //$class = ($Item['menu_id'] == $navigateId OR in_array($Item['menu_id'], $parent_pages) OR ( $_GET['param'] == Menu::getHyperLinkByID($Item['menu_id'])) OR ( strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id'])))) ? ' class="selected"' : ''; //((strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id']))) ? ' class="selected"' : '');
                        $class2 = ($Item['menu_id'] == $navigateId OR in_array($Item['menu_id'], $parent_pages) OR (isset($_GET['param']) && $_GET['param'] == Menu::getHyperLinkByID($Item['menu_id'])) OR ( strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id'])))) ? ' selected' : ''; //((strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id']))) ? ' class="selected"' : '');
                        $class2 .= in_array($Item['menu_id'], $parent_pages) ? ' parent' : ''; //((strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id']))) ? ' class="selected"' : '');

                        if (Menu::menuHasChilds($Item['menu_id']) and $childs == true ) {
                            $class2 .= " subsubmenu";
                        }

                        $output .= '<li' . ($class2 != NULL ? ' class="' . $class2 . '"' : '') . '><a href="' . Menu::getHyperLinkByID($Item['menu_id']) . '" title="' . Menu::getHyperLinkByID($Item['menu_id']) . '">' . $Item["name"] . '</a>';
                        if (Menu::menuHasChilds($Item['menu_id']) and $childs == true ) {
                            $output .=   Menu::returnMenu($Item['menu_id'], true, $navigateId);
                        }
                        $output .= '</li>';


                        /*
                        if(!in_array($Item['menu_id'], $detach)) {
                            $output .= '<li' . ($class2 != NULL ? ' class="' . $class2 . '"' : '') . '><a href="' . Menu::getHyperLinkByID($Item['menu_id']) . '" >' . $Item["name"] . '</a>';
                            if (Menu::menuHasChilds($Item['menu_id']) and ($childs == true OR strpos($class2, 'selected')) AND $selectedChilds == TRUE) {
                                $output .=   Menu::returnMenu($Item['menu_id'], $childs, $navigateId, NULL, NULL, NULL, TRUE);
                            }
                            $output .= '</li>';
                        }
                        */

                        /*
                        if ($i % 4 == 0) {
                            $output .= '<li class="divider"></li>';
                        }*/
                        if ($divide != NULL AND ( $i % $divide) == 0) {
                            $output .= '</ul>';
                            $output .= '<ul' . ($class != NULL ? ' class="' . $class . '"' : '') . '>';
                        }
                    }
                    if(count($attach) > 0) {
                        foreach($attach AS $row) {
                            if($navigateId == $row)
                                $class3 = $class2 . ' selected';
                            else
                                $class3 = '';
                            $output .= '<li' . ($class3 != NULL ? ' class="' . $class3 . '"' : '') . '><a href="' . Menu::getHyperLinkByID($row) . '" >' . Menu::getHyperLinkTextByID($row) . '</a>';
                            $output .= '</li>';
                        }
                    }
                    $output .= '</ul>';
                }
                return $output;
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $parentId . ')');
        }
    }

    static function returnMenuBS($parentId, $childs = false, $navigateId = "", $class = NULL, $divide = NULL, $extra = NULL, $selectedChilds = FALSE) {
        global $Row;
        $parent_pages = explode(';', $Row['left_menu_id']); // skúži na vypisanie všetkých aktívnych stránok
        $attach = $detach = array();
        if($extra) {
            $extraArray = explode(',', $extra);

            foreach ($extraArray as $key => $value) {
                if((int)trim($value) > 0)
                    $attach[] = (int)trim($value);
                else
                    $detach[] = -1 * (int)trim($value);
            }
        }

        if (is_numeric($parentId)) {
            $queryString = 'SELECT ' . $_SESSION["lang"] . '_name AS name, menu_id, child_of, right_menu_id FROM ' . TABLE_PREFIX . 'menu
                            WHERE 1 AND child_of="' . $parentId . '" AND ' . $_SESSION["lang"] . '_name <> "" and private="0"
                            ORDER BY sorter ASC;';
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                if (mysql_num_rows($Result) != '0' OR count($attach) > 0) {
                    $i = 0;
                    $output = '<ul class="nav ' . $class . '">';
                    while ($Item = mysql_fetch_assoc($Result)) {
                        $i++;
                        $deti = explode(";", $Item['right_menu_id']);
                        $class2 = ($Item['menu_id'] == $navigateId OR in_array($Item['menu_id'], $parent_pages) OR (isset($_GET['param']) && $_GET['param'] == Menu::getHyperLinkByID($Item['menu_id'])) OR ( strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id'])))) ? ' active' : '';
                        $class2 .= in_array($Item['menu_id'], $parent_pages) ? ' parent' : '';

                        if (Menu::menuHasChilds($Item['menu_id']) and $childs == true ) {
                            $class2 .= " has-menu";
                        }

                        $output .= '<li class="nav-item ' . $class2 . '"><a href="' . Menu::getHyperLinkByID($Item['menu_id']) . '" class="nav-link">' . $Item["name"] . '</a>';
                        if (Menu::menuHasChilds($Item['menu_id']) and $childs == true ) {
                            $output .=   Menu::returnMenuBS($Item['menu_id'], true, $navigateId);
                        }
                        if (Menu::menuHasChilds($Item['menu_id']) and $childs == true ) {
                            $output .= '<span></span>';
                        }
                        $output .= '</li>';

                        if ($divide != NULL AND ( $i % $divide) == 0) {
                            $output .= '</ul>';
                            $output .= '<ul' . ($class != NULL ? ' class="' . $class . '"' : '') . '>';
                        }
                    }
                    if(count($attach) > 0) {
                        foreach($attach AS $row) {
                            if($navigateId == $row)
                                $class3 = $class2 . ' selected';
                            else
                                $class3 = '';
                            $output .= '<li' . ($class3 != NULL ? ' class="' . $class3 . '"' : '') . '><a href="' . Menu::getHyperLinkByID($row) . '" >' . Menu::getHyperLinkTextByID($row) . '</a>';
                            $output .= '</li>';
                        }
                    }
                    $output .= '</ul>';
                }
                return $output;
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $parentId . ')');
        }
    }
/*
    static function returnMenu($parentId, $childs = false, $navigateId = "", $class = NULL, $divide = NULL) {
        global $Row;
        $parent_pages = explode(';', $Row['left_menu_id']); // skúži na vypisanie všetkých aktívnych stránok

        if (is_numeric($parentId)) {
            $queryString = 'SELECT ' . $_SESSION["lang"] . '_name AS name, menu_id, child_of, right_menu_id FROM ' . TABLE_PREFIX . 'menu
                            WHERE 1 AND child_of="' . $parentId . '" AND ' . $_SESSION["lang"] . '_name <> "" and private="0"
                            ORDER BY sorter ASC;';
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                if (mysql_num_rows($Result) != '0') {
                    $i = 0;
                    $output = '<ul' . ($class != NULL ? ' class="' . $class . '"' : '') . '>';
                    while ($Item = mysql_fetch_assoc($Result)) {
                        $i++;
                        $deti = explode(";", $Item['right_menu_id']); // strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id']))
                        $class = ($Item['menu_id'] == $navigateId OR in_array($Item['menu_id'], $parent_pages) OR ( $_GET['param'] == Menu::getHyperLinkByID($Item['menu_id'])) OR ( strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id'])))) ? ' class="selected"' : ''; //((strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Item['menu_id']))) ? ' class="selected"' : '');
						if (Menu::menuHasChilds($Item['menu_id']) and $childs == true ) {
							$class=" class='subsubmenu'";
						}
                        $output .= '<li' . $class . '><a href="' . Menu::getHyperLinkByID($Item['menu_id']) . '" title="' . Menu::getHyperLinkByID($Item['menu_id']) . '">' . $Item["name"] . '</a>';
                        if (Menu::menuHasChilds($Item['menu_id']) and $childs == true ) {
                            $output .=   Menu::returnMenu($Item['menu_id'], true, $navigateId);
                        }
                        $output .= '</li>';
                        if ($divide != NULL AND ( $i % $divide) == 0) {
                            $output .= '</ul>';
                            $output .= '<ul' . ($class != NULL ? ' class="' . $class . '"' : '') . '>';
                        }
                    }
                    $output .= '</ul>';
                }
                return $output;
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $parentId . ')');
        }
    }
*/
    /**
     * popupMenu
     *
     * Vygeneruje vyskakovacie menu. Premenná $first v stave true signalizuje, že
     * menu je "zavesené" už na niečo (čiže je už súčasťou menu) alebo nie.
     * Doporučuje sa používať najmä v tvare: Menu::popupMenu(1)
     *
     * @param int		$parentId       id stránky, ktorej deti potrebujeme vypísať
     *
     * @return bool
     *
     */
    static function popupMenu($parentId, $class = 'main-menu') {
        global $navigateId;
        global $navigateParentId;
        if (is_numeric($parentId)) {
            $queryString = "SELECT " . $_SESSION["lang"] . "_name AS name, menu_id FROM " . TABLE_PREFIX . "menu
                            WHERE 1 AND child_of = '" . $parentId . "' AND private = '0'
                            ORDER BY sorter ASC;";
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                $output = '<ul id="'.(($rowM==0)?'nav':' ' ).'" class="' . $class . '">';
                while ($Row = mysql_fetch_assoc($Result)) {

                    // zistenie pridavneho stylu
                    $style = '';
                    if (Menu::menuHasChilds($Row['menu_id'])) {
                        $style .= "sub-menu";
                    } // strpos($_SERVER['REQUEST_URI'], Menu::getHyperLinkByID($Row['menu_id']))
                    if (($_GET['param'] == Menu::getHyperLinkByID($Row['menu_id'])) or $Row['menu_id'] == $navigateId or $Row['menu_id'] == $navigateParentId OR isPraParent($Row['menu_id'])) {
                        if (!empty($style))
                            $style.= ' ';
                        $style .= 'selected';
                    }

                    $output .= '<li' . (!empty($style) ? ' class="' . $style . '"' : '') . '>';
                    if (strpos($styl, "sub-menu")) {
                        $linka = "javascript:;";
                    } else {
                        $linka = Menu::getHyperLinkByID($Row['menu_id']);
                    }

                    $output .= '<a href="' . Menu::getHyperLinkByID($Row['menu_id']) . '"><span>' . $Row["name"] . '</span></a>';

                    if (Menu::menuHasChilds($Row['menu_id']))
						$output .=  Menu::returnMenu($Row['menu_id'], true);

                    $output .= '</li>';
                }
                $output .= '</ul >';
                echo $output;
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $parentId . ')');
        }
    }
    /**
     * popupMenuBS
     *
     * Vygeneruje vyskakovacie menu pre Bootstrap 5.3. Premenná $first v stave true signalizuje, že
     * menu je "zavesené" už na niečo (čiže je už súčasťou menu) alebo nie.
     * Doporučuje sa používať najmä v tvare: Menu::popupMenu(1)
     *
     * @param int       $parentId       id stránky, ktorej deti potrebujeme vypísať
     *
     * @return bool
     *
     */
    static function popupMenuBS($parentId, $class = 'main-menu') {
        global $navigateId;
        global $navigateParentId;
        if (is_numeric($parentId)) {
            $queryString = "SELECT " . $_SESSION["lang"] . "_name AS name, menu_id FROM " . TABLE_PREFIX . "menu
                            WHERE 1 AND child_of = '" . $parentId . "' AND private = '0'
                            ORDER BY sorter ASC;";
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno()) {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            } else {
                $output = '<ul id="'.(($rowM==0)?'nav':' ' ).'" class="' . $class . '">';
                while ($Row = mysql_fetch_assoc($Result)) {

                    // zistenie pridavneho stylu
                    $style = '';
                    if (Menu::menuHasChilds($Row['menu_id'])) {
                        $style .= "has-menu";
                    }
                    if (($_GET['param'] == Menu::getHyperLinkByID($Row['menu_id'])) or $Row['menu_id'] == $navigateId or $Row['menu_id'] == $navigateParentId OR isPraParent($Row['menu_id'])) {
                        if (!empty($style))
                            $style.= ' ';
                        $style .= 'active';
                    }

                    $output .= '<li' . (!empty($style) ? ' class="nav-item ' . $style . '"' : '') . '>';
                    $output .= '<a href="' . Menu::getHyperLinkByID($Row['menu_id']) . '" class="nav-link">' . $Row["name"] . '</a>';
                    if (Menu::menuHasChilds($Row['menu_id'])) {
                        $output .= '<span></span>';
                    }

                    if (Menu::menuHasChilds($Row['menu_id']))
                        $output .=  Menu::returnMenuBS($Row['menu_id'], true);

                    $output .= '</li>';
                }
                $output .= '</ul >';
                echo $output;
            }
        } else {
            var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $parentId . ')');
        }
    }

    /**
     * crumbleNavigation
     *
     * Omrvinková navigácia je navigačná pomôcka používa v používateľskom rozhraní.
     *
     *
     * @return xyz
     *
     */
    static function crumbleNavigation() {
        global $navigateArrayNumbers;
        global $navigateId;
        global $cTranslator;

        $output = '';
        if (count($navigateArrayNumbers) != 0) {
            $output .= '<ul>';
            
            // --- SEO BREADCRUMBLIST SCHEMA PREPARATION ---
            $domainUrl = ROOTDIR;
            $breadcrumbItems = array();
            $position = 1;
            // ---------------------------------------------
            
            for ($i = 0; $i < count($navigateArrayNumbers); $i++) {
                $query = "SELECT " . $_SESSION["lang"] . "_name AS name, menu_id FROM " . TABLE_PREFIX . "menu
                            WHERE 1 AND menu_id='" . $navigateArrayNumbers[$i] . "' AND private='0'
                            LIMIT 1;";
                if (!$result = mysql_query($query)) {
                    if (mysql_errno()) {
                        var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                        return'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                    }
                } else {
                    $row = mysql_fetch_object($result);
                    if ($row->menu_id == 1 OR count($navigateArrayNumbers) == 1) {
                        //$output .= '<li><a href="' . ROOTDIR . '">' . $cTranslator->getTranslation("Uvod", 0) . '</a> &gt; </li>';
                    } else {
                        $crumbUrl = Menu::getHyperLinkByID($row->menu_id);
                        $output .= '<li><a href="' . $crumbUrl . '">' . $row->name . '</a> &gt; </li>';
                        
                        // SEO Add item
                        $breadcrumbItems[] = array(
                            "@type" => "ListItem",
                            "position" => $position++,
                            "name" => $row->name,
                            "item" => $domainUrl . '/' . $crumbUrl
                        );
                    }
                }
            }
            $activeText = Menu::getHyperLinkTextByID($navigateId);
            $output .= '<li><span>' . $activeText . '</span></li>';
            $output .= '</ul>';
            
            // SEO Add last item (current page)
            $breadcrumbItems[] = array(
                "@type" => "ListItem",
                "position" => $position++,
                "name" => $activeText,
                "item" => $domainUrl . $_SERVER['REQUEST_URI']
            );
            
            $breadcrumbSchema = array(
                "@context" => "https://schema.org",
                "@type" => "BreadcrumbList",
                "itemListElement" => $breadcrumbItems
            );
            $output .= '<!-- SEO BREADCRUMB SCHEMA -->' . n;
            $output .= '<script type="application/ld+json">' . n . json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . n . '</script>' . n;
        }
        return $output;
    }

    /* ADMIN FUNCTIONS */

    static public function print_tree_combobox($menu_id = NULL, $retVal = NULL, $level = 0) {
        $level++;
        $queryString = "select * from " . TABLE_PREFIX . "menu where 1 and child_of " . ((!is_numeric($menu_id)) ? ' is null' : ' = \'' . $menu_id . '\'') . ";";
        $Result = mysql_query($queryString);
        if ($Result) {
            while ($Row = mysql_fetch_assoc($Result)) {
                for ($i = 2; $i < $level; $i++) {
                    $ad .='&nbsp;&nbsp;';
                }
                if (is_array($retVal)) {
                    $status = (in_array($Row['menu_id'], $retVal) ? ' selected="selected"' : '');
                } else {
                    $status = (($Row['menu_id'] == $retVal) ? ' selected="selected"' : '');
                }
                $value = empty($Row[strtolower($_SESSION['lang']) . '_name']) ? '[]' : $Row[strtolower($_SESSION['lang']) . '_name'];
                print('<option value="' . $Row['menu_id'] . '"' . $status . '>' . $ad . $value . '</option>');
                Menu::print_tree_combobox($Row['menu_id'], $retVal, $level);
                unset($ad);
            }
        } else {
            print(mysql_error());
        }
        mysql_free_result($Result);
    }

    static function gettree_table($menu_id = NULL) {
        global $level, $_parny, $cLanguage;
        $level++;
        $queryString = "SELECT menu.*, module.name AS Expr1 FROM " . TABLE_PREFIX . "menu AS menu
                        LEFT JOIN " . TABLE_PREFIX . "module AS module USING (module_id)
                        WHERE 1 AND child_of " . ((!is_numeric($menu_id)) ? ' IS NULL' : ' = \'' . $menu_id . '\'') . " ORDER BY sorter ASC;";
        $Result = mysql_query($queryString);
        if ($Result) {
            while ($Row = mysql_fetch_assoc($Result)) {
                ?>
                <tr class="<?= ((!$_parny) ? 'style1' : 'style2'); ?>" title="ID: <?= $Row['menu_id']; ?>">
                    <td class="title" style="padding-left: <?= ($level * 13); ?>px;">
                        <a href="./index.php?module=menu&amp;action=update&amp;menu_id=<?= $Row['menu_id'] . '&amp;child_of=' . $Row['child_of']; ?>">
                            <?
                            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                print '[ ' . $Row[strtolower($val) . '_name'] . ' ] ';
                            }
                            ?>
                        </a>
                    </td>
                    <td valign="top" align="left" style="width: 86px;"><input disabled="disabled" type="checkbox"<?= (($Row['private'] == 0) ? ' checked="checked"' : ''); ?> /></td>
                    <td valign="top" align="left"><?= $Row['Expr1']; ?></td>
                    <td class="actions">
                        <?
                        if ($Row['menu_id'] != 1) {
                            ?>
                            <a class="sorter-link" href="javascript:;" onclick="javascript:sortItemC(<?= $Row['child_of']; ?>);">Poradie</a>
                            <a class="slideshow-link" href="javascript:;" onclick="javascript:slideshow(<?= $Row['menu_id']; ?>);">Slideshow</a>
                            <a class="insert-link" href="./index.php?module=menu&amp;child_of=<?= $Row['menu_id']; ?>&amp;action=insert">Pridať</a>
                            <a class="edit-link" href="./index.php?module=menu&amp;action=update&amp;menu_id=<?= $Row['menu_id'] . '&amp;child_of=' . $Row['child_of']; ?>">Upraviť</a>
                            <a class="remove-link" href="javascript:;" onclick="javascript:ConfirmBoxAc('Naozaj si želáte odstrániť túto položku z menu?', './index.php?module=menu&amp;action=delete&amp;menu_id=<?= $Row['menu_id']; ?>', '');">Zmazať</a>
                            <?
                        } else
                            print '<a href="./index.php?module=menu&amp;child_of=' . $Row['menu_id'] . '&amp;action=insert">Pridať stránku do hlavného menu</a>';
                        ?>
                    </td>
                </tr>
                <?
                $_parny = !$_parny;
                Menu::gettree_table($Row['menu_id']);
            }
        } else {
            print(mysql_error());
        }
        mysql_free_result($Result);
        $level--;
    }

    static function gettree_table_gallery($menu_id = NULL) {
        global $level, $_parny, $cLanguage;
        $level++;
        $queryString = "select menu.*, module.name as Expr1 from " . TABLE_PREFIX . "menu as menu left join " . TABLE_PREFIX . "module as module using (module_id) where 1 and child_of " . ((!is_numeric($menu_id)) ? ' is null' : ' = \'' . $menu_id . '\'') . " and module_id = '4' order by sorter asc;";
        $Result = mysql_query($queryString);
        if ($Result) {

            while ($Row = mysql_fetch_assoc($Result)) {
                print '<p style="margin: 0px; padding-left: ' . ($level * 13) . 'px; width: 40%;">
						<a href="javascript:;" onclick="javascript:window.parent.frames[\'foto_right\'].location = \'foto_right.php?menu_id=' . $Row['menu_id'] . '\';javascript:editCategoryName(\'' . $line->sk_name . '\', \'' . $line->id . '\');">';
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    print '[ ' . $Row[strtolower($val) . '_name'] . ' ] ';
                }
                print '</a></p>';
                $_parny = !$_parny;
                Menu::gettree_table_gallery($Row['menu_id']);
            }
        } else {
            print(mysql_error());
        }
        mysql_free_result($Result);
        $level--;
    }

    function categoryTitleById($id) {

        $result = mysql_query('SELECT m.menu_id, c.' . $_SESSION['lang'] . '_name_seo AS name FROM ' . TABLE_PREFIX . 'product_menu AS m
                                LEFT JOIN ' . TABLE_PREFIX . 'menu AS c USING(menu_id)
                                WHERE m.product_id="' . $id . '";');

        while ($row = mysql_fetch_object($result)) {
            $menu_seo[] = $row->name;
        }

        $menu_seo = findLongestStringFromArray($menu_seo);
        $menu_seo = 'sk/' . $menu_seo['string'];

        if (@ereg("([A-Za-z]{2})", $menu_seo, $parts)) {
            //$_SESSION['lang'] = $parts[0];
            $parts[0] = $_SESSION['lang'];
            $parts[1] = substr($menu_seo, 3);
            $tparts = explode("/", $parts[1]);

            $queryInclude = array();

            for ($i = 0; $i <= sizeof(explode("/", $parts[1])); $i++) {
                if (strlen(implode("/", $tparts)) > 0) {
                    $queryInclude[] .= "`m`.`" . $parts[0] . "_name_seo` = '" . safetyMysql(implode("/", $tparts)) . "'";
                }
                array_pop($tparts);
            }

            if (empty($queryInclude)) {
                return false;
            }

            $queryString = "SELECT menu_id, sk_name AS name FROM " . TABLE_PREFIX . "menu AS m WHERE 1 AND (" . implode(" or ", $queryInclude) . ") ORDER BY char_length(" . $parts[0] . "_name_seo) DESC;";

            if ($Result = mysql_query($queryString)) {
                if (mysql_num_rows($Result) != 0) {
                    while ($row = mysql_fetch_object($Result)) {
                        if ($row->menu_id != eshop_main_category) {
                            $output[] = $row->name;
                        }
                    }
                }
            } else {
                if (mysql_errno())
                    echo $menu_seo . ': ' . $queryString . '<br />';
                die("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
            }
        }
        $output = array_reverse($output);

        return implode(' | ', $output);
    }

}
?>
