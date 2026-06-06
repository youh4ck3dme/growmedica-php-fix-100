<?

function tabulator_zobrazeny($sql_str = NULL, $eshop_link = NULL) {
    global $limit;
    global $cTranslator;
    if (!is_numeric($_GET['page'])) {
        $page = 1;
    } else {
        $page = $_GET['page'];
    }
    $pages = 1;
    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        $pages = ceil(mysql_num_rows($ResultA) / $_SESSION['userPrefs']['prodMnozstvoNaStrane']);
    }
    mysql_free_result($ResultA);

    $limit = " limit " . (($page == 1 or ! isset($page)) ? "0" : ($page - 1) * $_SESSION['userPrefs']['prodMnozstvoNaStrane']) . ", " . $_SESSION['userPrefs']['prodMnozstvoNaStrane'];
    $url_inc = "&amp;show=1&amp;ItemID=" . $_GET['ItemID'] . "&amp;sort=" . $_GET['sort'] . "&amp;dir=" . $_GET['dir'];

    return sprintf("<table border='0' class=\"tabulator\" align=\"center\"><tr><td align=\"center\"><a href=\"" . (($page > 1) ? ROOTDIR . "/" . $eshop_link . "?page=" . ($page - 1) . $url_inc : "javascript:;") . "\">&laquo;</a></td><td align=\"center\">" . $cTranslator->getTranslation("zobraziť", 0) . " %s " . $cTranslator->getTranslation("z", 0) . " %s " . $cTranslator->getTranslation("strán", 0) . "</td><td align=\"right\"><a href=\"" . (($page < $pages) ? "" . ROOTDIR . "/" . $eshop_link . "?page=" . ($page + 1) . $url_inc . "" : "javascript:;") . "\">&raquo;</a></td></tr></table>", $page, $pages);
}

function pagination($query, $url = '?', $page = 1, $isAdmin = false) {
    global $limit;
    global $cTranslator;

    if ($isAdmin == false) {
        $items_per_page = $_SESSION['userPrefs']['prodMnozstvoNaStrane'];
    } else {
        $items_per_page = $_SESSION['userPrefs']['admin_items_per_page'];
    }
    $ResultA = mysql_query($query);

    $total = mysql_num_rows($ResultA);
    $adjacents = "2";

    $limit = " limit " . (($page == 1 or ! isset($page)) ? "0" : ($page - 1) * $items_per_page) . ", " . $items_per_page;

    $prevlabel = $cTranslator->getTranslation('« Predchádzajúca', 0);
    $nextlabel = $cTranslator->getTranslation('Ďalšia »', 0);

    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $items_per_page;

    $prev = $page - 1;
    $next = $page + 1;

    $lastpage = ceil($total / $items_per_page);

    if (strpos($url, '?') !== false) {
        $url = $url . '&';
    } else {
        $url = $url . '?';
    }

    $lpm1 = $lastpage - 1; // //last page minus 1

    $pagination = '';
    if ($lastpage > 1) {
        $pagination .= '<ul class="pagination">';

        if ($page > 1)
            $pagination.= '<li class="prev"><a href=' . $url . 'page=' . $prev . '>' . $prevlabel . '</a></li>';

        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination.= '<li><a class="current">' . $counter . '</a></li>';
                else
                    $pagination.= '<li><a href="' . $url . 'page=' . $counter . '">' . $counter . '</a></li>';
            }
        } elseif ($lastpage > 5 + ($adjacents * 2)) {

            if ($page < 1 + ($adjacents * 2)) {

                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination.= '<li><a class="current">' . $counter . '</a></li>';
                    else
                        $pagination.= '<li><a href="' . $url . 'page=' . $counter . '">' . $counter . '</a></li>';
                }
                $pagination.= '<li class="dot">...</li>';
                $pagination.= '<li><a href="' . $url . 'page=' . $lpm1 . '">' . $lpm1 . '</a></li>';
                $pagination.= '<li><a href="' . $url . 'page=' . $lastpage . '">' . $lastpage . '</a></li>';
            } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

                $pagination.= '<li><a href="' . $url . 'page=1">1</a></li>';
                $pagination.= '<li><a href="' . $url . 'page=2">2</a></li>';
                $pagination.= '<li class="dot">...</li>';
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= '<li><a class="current">' . $counter . '</a></li>';
                    else
                        $pagination.= '<li><a href="' . $url . 'page=' . $counter . '">' . $counter . '</a></li>';
                }
                $pagination.= '<li class="dot">..</li>';
                $pagination.= '<li><a href="' . $url . 'page=' . $lpm1 . '">' . $lpm1 . '</a></li>';
                $pagination.= '<li><a href="' . $url . 'page=' . $lastpage . '">' . $lastpage . '</a></li>';
            } else {

                $pagination.= '<li><a href="' . $url . 'page=1">1</a></li>';
                $pagination.= '<li><a href="' . $url . 'page=2">2</a></li>';
                $pagination.= '<li class="dot">..</li>';
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= '<li><a class="current">' . $counter . '</a></li>';
                    else
                        $pagination.= '<li><a href="' . $url . 'page=' . $counter . '">' . $counter . '</a></li>';
                }
            }
        }

        if ($page < $counter - 1)
            $pagination.= '<li class="next"><a href=' . $url . 'page=' . $next . '>' . $nextlabel . '</a></li>';

        $pagination.= '</ul>';
    }

    return $pagination;
}

function tabulator1($sql_str = NULL, $isAdmin = false) {
    global $limit;
    global $tl;

    if ($isAdmin == false) {
        $items_per_page = $_SESSION['userPrefs']['prodMnozstvoNaStrane'];
    } else {
        $items_per_page = $_SESSION['userPrefs']['admin_items_per_page'];
    }

    if (!is_numeric($_GET['page'])) {
        $page = 1;
    } else {
        $page = $_GET['page'];
    }
    $pages = 1;
    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        $pages = ceil(mysql_num_rows($ResultA) / $items_per_page);
        mysql_free_result($ResultA);
    }
    
    $limit = " limit " . (($page == 1 or ! isset($page)) ? "0" : ($page - 1) * $items_per_page) . ", " . $items_per_page;

    $url_inc = "&amp;show=1&amp;ItemID=" . $_GET['ItemID'] . "&amp;sort=" . $_GET['sort'] . "&amp;dir=" . $_GET['dir'];
}

function return_combobox($queryString = NULL, $retVal = NULL) {
    $Result = mysql_query($queryString);
    if ($Result) {
        $Return = '';
        while ($Row = mysql_fetch_assoc($Result)) {
            $Return .= '<option value="' . $Row['id'] . '"' . (($Row['id'] == $retVal) ? ' selected="selected"' : '') . '>' . $Row['name'] . '</option>';
        }
        return $Return;
    } else {
        $Result .= mysql_error();
    }
}

function ReturnSEOFriendlyUrl($itemName = NULL, $child_of = NULL, $languageCode = "sk") {
    $queryString = "select " . $languageCode . "_name_seo" . ", " . $languageCode . "_name" . " as name,child_of from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $child_of . "';";
    $Result = mysql_query($queryString);
    if ($Result) {
        if (mysql_num_rows($Result) == 1) {
            $Row = mysql_fetch_assoc($Result);
            if ($Row['child_of'] == 1) {
                if ($Row['name'] != "/" and ! empty($Row['name'])) {
                    return String::SEOFriendlyText($Row['name']) . "/" . String::SEOFriendlyText($itemName);
                }
            }
        }
    } else {
        print(mysql_error());
    }
    return String::SEOFriendlyText($itemName);

    //echo String::SEOFriendlyText($itemName);
    //die();
}

function getContentByLabel($label, $display = 1) {
    global $user;
    global $cTranslator;

    $output = NULL;
    $query = "SELECT c." . strtolower($_SESSION['lang']) . "_content AS content, c.content_id FROM " . TABLE_PREFIX . "content AS c
              WHERE 1 AND c.label='" . $label . "';";
    $result = mysql_query($query);
    if ($result) {
        if (mysql_num_rows($result) == 1) {
            $row = mysql_fetch_object($result);
            $output = html_entity_decode($row->content, ENT_QUOTES, "UTF-8");
            if ($user->isAdmin() AND $display == 1) {
                $output .= '<div class="clear"></div>';
                $output .= '<div class="edit-link content">';
                $output .= '<a href="javascript:;" onclick="javascript:openPopupWindow(\'static-content\', ' . $row->content_id . ', 900, 600);return false;">' . $cTranslator->getTranslation('Upraviť text') . '</a>';
                $output .= '</div>';
                $output .= '<div class="clear"></div>';
            }
            return $output;
        } else {
            //	vytvorime zaznam pre sekciu
            $queryString = sprintf("INSERT INTO `" . TABLE_PREFIX . "content` (`" . strtolower($_SESSION['lang']) . "_content`, `label`) VALUES ('%s', '%s');", $label, $label);
            $ResultB = mysql_query($queryString);
            if (!$ResultB) {
                print(mysql_error());
            } else
                return $label;
            @mysql_free_result($ResultB);
        }
    }else {
        print(mysql_error());
    }
    @mysql_free_result($ResultA);
}

function check_seolinks($menu_id = NULL, $langCode = NULL) {
    $queryString = "select menu_id, " . $langCode . "_name as name, child_of from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $menu_id . "';";
    if ($result = mysql_query($queryString)) {
        if (mysql_num_rows($result) == 1) {
            $row = mysql_fetch_assoc($result);

            if (!is_null($row['child_of'])) {
                $pStr = get_seoparent_name($row['child_of'], $langCode);
                if (!empty($pStr)) {
                    $ret[] = $pStr;
                }
            }
            $ret[] = String::SEOFriendlyText($row['name']);

            $queryString = "update " . TABLE_PREFIX . "menu set " . $langCode . "_name_seo = '" . strtolower(implode("/", $ret)) . "' where 1 and menu_id = '" . $row['menu_id'] . "';";
            if (!$resultB = mysql_query($queryString)) {
                print(mysql_error() . "<br />");
            }

            $queryString = "select menu_id from " . TABLE_PREFIX . "menu where 1 and child_of = '" . $row['menu_id'] . "';";
            if ($resultC = mysql_query($queryString)) {
                while ($rowC = mysql_fetch_assoc($resultC)) {
                    check_seolinks($rowC['menu_id'], $langCode);
                }
            }
        }
    } else {
        if (mysql_errno())
            print("MySql Error (" . mysql_errno() . "): "
                    . mysql_error() . "<br />");
    }
}

function right_menu_id($menu_id = NULL) {
    $right_menu_id = array();
    $queryString = "select menu_id, child_of from " . TABLE_PREFIX . "menu where 1 and child_of = '" . $menu_id . "';";
    if ($result = mysql_query($queryString)) {
        if (mysql_num_rows($result) == 1) {
            while ($row = mysql_fetch_array($result)) {
                $right_menu_id[] = $row["menu_id"];
            }

            $queryString = "update " . TABLE_PREFIX . "menu set right_menu_id = '" . implode(";", $right_menu_id) . "' where 1 and menu_id = '" . $menu_id . "';";
            if (!$resultB = mysql_query($queryString)) {
                print(mysql_error() . "<br />");
            }
        }
    } else {
        if (mysql_errno())
            print("MySql Error (" . mysql_errno() . "): "
                    . mysql_error() . "<br />");
    }
}

function left_menu_id($menu_id = NULL) {
    global $left_menu_id;
    global $insert_menu_id;

    $queryString = "select menu_id, child_of from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $menu_id . "' limit 1;";
    if ($result = mysql_query($queryString)) {
        if (mysql_num_rows($result) == 1) {
            $row = mysql_fetch_array($result);

            if ($menu_id == 1) {
                $queryString = "update " . TABLE_PREFIX . "menu set left_menu_id = '" . implode(";", $left_menu_id) . "' where 1 and menu_id = '" . $insert_menu_id . "';";
                if (!$resultB = mysql_query($queryString)) {
                    print(mysql_error() . "<br />");
                }
            } else {
                $left_menu_id[] = $row['child_of'];
                left_menu_id($row['child_of']);
            }
        }
    } else {
        if (mysql_errno())
            print("MySql Error (" . mysql_errno() . "): "
                    . mysql_error() . "<br />");
    }
}

function parent_id_right_menu_id($menu_id = NULL) {
    $right_menu_id = array();

    $queryString2 = "select child_of from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $menu_id . "';";
    if ($result2 = mysql_query($queryString2)) {
        if (mysql_num_rows($result2) == 1) {
            $row2 = mysql_fetch_array($result2);

            $queryString = "select menu_id, child_of from " . TABLE_PREFIX . "menu where 1 and child_of = '" . $row2["child_of"] . "';";
            if ($result = mysql_query($queryString)) {
                if (mysql_num_rows($result) > 1) {
                    while ($row = mysql_fetch_array($result)) {
                        $right_menu_id[] = $row["menu_id"];
                    }

                    $queryString = "update " . TABLE_PREFIX . "menu set right_menu_id = '" . implode(";", $right_menu_id) . "' where 1 and menu_id = '" . $row2["child_of"] . "';";
                    if (!$resultB = mysql_query($queryString)) {
                        print(mysql_error() . "<br />");
                    }
                }
            } else {
                if (mysql_errno())
                    print("MySql Error (" . mysql_errno() . "): "
                            . mysql_error() . "<br />");
            }
        }
    }
}

function get_seoparent_name($menu_id, $langCode = NULL) {
    $queryString = "select menu_id, " . $langCode . "_name as name, " . $langCode . "_name_seo as name_seo, child_of from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $menu_id . "';";
    if ($result = mysql_query($queryString)) {
        if (mysql_num_rows($result) == 1) {
            $row = mysql_fetch_assoc($result);
            if ($row['name'] != "/") {
                return $row['name_seo'];
            }
        }
    }
}

/*
  function diakritikaFileName_v2($string) {
  //$string = strtolower($string);
  $hladane = array('š', 'ý', 'á', 'í', 'é', 'ú', 'ä', 'ô', 'ó', 'ž', 'ľ', 'ň', 'ď', 'č', 'ö', 'ó', 'Š', 'Ý', 'Á', 'Í', 'É', 'Ú', 'Ä', 'Ž', 'Ľ', 'Ň', 'Ď', 'Č', 'Ö', 'Ó', 'ť', 'Ť', ' ', 'ů', 'ě', 'ř', '&', '?');
  $nahradzane = array('s', 'y', 'a', 'i', 'e', 'u', 'a', 'o', 'o', 'z', 'l', 'n', 'd', 'c', 'ö', 'ó', 's', 'y', 'A', 'i', 'E', 'U', 'A', 'Z', 'L', 'N', 'D', 'C', 'o', 'O', 't', 'T', '-', 'u', 'e', 'r', '-', '');
  for ($i = 0; $i < count($hladane); $i++) {
  $string = str_replace($hladane[$i], $nahradzane[$i], $string);
  }
  return $string;
  }
 */

function encode_value($value) {
    if (!is_array($value)) {
        return str_replace(array('"', "'", "<", ">"), array("&quot;", "&apos;", "&lt;", "&gt;"), html_entity_decode($value, ENT_QUOTES, "UTF-8"));
    } else {
        return $value;
    }
}

function makeLog($operacia, $popis) {
    $query = "insert into " . TABLE_PREFIX . "log values(now(), '" . $_SESSION["user_id"] . "', '" . $_SESSION["username"] . "', '" . $_SERVER['REMOTE_ADDR'] . "','" . ($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) . "', '" . $operacia . "','" . $popis . "');";
    mysql_query($query);
}

function safetyMysql($mysql) {
    $return = str_replace("'", "", $mysql);
    $return = str_replace('"', "", $return);
    return $return;
}

function message($message) {
    print '<div id="message">';
    print $message;
    print '</div>';
}

function Pre($pre) {
    print '<pre>';
    print_r($pre);
    print '</pre>';
}

function diff_date_string($value) {
    global $cTranslator;

    switch ($calue) {
        case 0:
            return $cTranslator->getTranslation('dnes');
            break;

        case 1:
            return $cTranslator->getTranslation('včera');
            break;

        case $calue > 1 && $calue < 7:
            return $cTranslator->getTranslation('pred') . ' ' . $calue . ' ' . $cTranslator->getTranslation('dňami');
            break;

        case $calue > 6 && $calue < 14:
            return $cTranslator->getTranslation('pred týždňom');
            break;

        case $calue > 13 && $calue < 30:
            return $cTranslator->getTranslation('pred') . ' ' . floor($calue / 7) . ' ' . $cTranslator->getTranslation('týždňami');
            break;

        case $calue > 29 && $calue < 60:
            return $cTranslator->getTranslation('pred mesiacom');
            break;

        case $calue > 59 && $calue < 365:
            return $cTranslator->getTranslation('pred') . ' ' . floor($calue / 30) . ' ' . $cTranslator->getTranslation('mesiacmi');
            break;

        case $calue > 364 && $calue < 730:
            return $cTranslator->getTranslation('pred rokom');
            break;

        case $calue > 729:
            return $cTranslator->getTranslation('pred') . ' ' . floor($calue / 365) . ' ' . $cTranslator->getTranslation('rokmi');
            break;

        default:
            return $cTranslator->getTranslation('nevedno kedy');
            break;
    }
}

/**
 * Truncate
 * Slúži na skracovanie textu
 * STRING = text
 * LIMIT = požadovaná dlžka textu / default: 200
 * BREAK = znak, ktorým má byť text rozdelený / default: .
 * PAD = reťazec doplnený po skrátení textu / default: ...
 */
function truncate($string, $limit = 200, $break = " ", $pad = "...") {
    if (strlen($string) <= $limit)
        return $string;

    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}

/**
 * randomString
 * Slúži na generovanie náhodných znakov, napr. pri uploade obrázkov
 * LENGTH = počet znakov / default: 10
 */
function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * phone_validate
 * Validuje telefónne číslo
 * Formát:
 * xxxx xxx xxx
 * +42x xxx xxx xxx
 * 0042x xxx xxx xxx
 */
function phoneValidate($number) {
    return preg_match("~^(((\+|00)42\d)? ?\d{3,4} ?\d{3} ?\d{3})$~", $number);
}

/**
 * percentageDiscount
 * Počíta percentuálnu zľavu
 * PRICE = cena produktu
 * OLD_PRICE = nová cena produktu
 * PRECISION = zaokruhlenie
 */
function percentageDiscount($price, $old_price, $precision) {
    return round(((($old_price - $price) / $old_price) * 100), $precision);
}

/**
 * invertColor
 * Generuje opačnú farbu :D
 */
function invertColor($start_colour) {
    $colour_red = hexdec(substr($start_colour, 1, 2));
    $colour_green = hexdec(substr($start_colour, 3, 2));
    $colour_blue = hexdec(substr($start_colour, 5, 2));

    $new_red = dechex(255 - $colour_red);
    $new_green = dechex(255 - $colour_green);
    $new_blue = dechex(255 - $colour_blue);

    if (strlen($new_red) == 1) {
        $new_red .= '0';
    }
    if (strlen($new_green) == 1) {
        $new_green .= '0';
    }
    if (strlen($new_blue) == 1) {
        $new_blue .= '0';
    }

    $new_colour = '#' . $new_red . $new_green . $new_blue;

    return $new_colour;
}

/**
 * crossDomainPosted
 * Kontroluje odkiaľ pochádzal request
 */
function crossDomainPosted() {
    if (isset($_SERVER['HTTP_REFERER'])) {
        $parse_url = parse_url($_SERVER['HTTP_REFERER']);
        if ($parse_url['host'] != $_SERVER['HTTP_HOST']) {
            return false;
        } else {
            return true;
        }
    }
}

/**
 * preparePrice
 * pripraví cenu pre uloženie do DB
 */
function preparePrice($price) {
    $search = array(',', ' ');
    $replace = array('.', '');
    return str_replace($search, $replace, (string) $price);
}
function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
    if ($considerHtml) {
		// if the plain text is shorter than the maximum length, return the whole text
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach ($lines as $line_matchings) {
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
					unset($open_tags[$pos]);
					}
				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length) {
				break;
			}
		}
	} else {
		if (strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}
	// if the words shouldn't be cut in the middle...
	if (!$exact) {
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		if (isset($spacepos)) {
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
	}
	// add the defined ending to the text
	$truncate .= $ending;
	if($considerHtml) {
		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}
	}
	return $truncate;
}

function getEshopLeadingCategory($product_id) {
    if (is_numeric($product_id)) {
        $query = 'SELECT m.menu_id, m.left_menu_id, m.child_of  
                    FROM ' . TABLE_PREFIX . 'menu AS m 
                    JOIN ' . TABLE_PREFIX . 'product_menu AS pm ON(m.menu_id = pm.menu_id)
                    WHERE 1 AND pm.product_id = "' . $product_id . '" ';
        
        if($result = mysql_query($query)) {
            if(mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    if($row['child_of'] == ESHOP_MAIN_CATEGORY)
                        $leadingId = $row['menu_id'];
                    else {
                        $leftMenuArray = explode(';', $row['left_menu_id']);
                        $values = array_values(array_reverse($leftMenuArray));
                        $leadingId = $values[2];
                    }
                    
                }
            }
        }
        else echo 'Error (' . mysql_errno() . '): ' . mysql_error();

    /*    $leftMenuArray = explode(';', $leftMenu);
        foreach ($leftMenuArray as $key => $value) {
            if($value == ESHOP_MAIN_CATEGORY) $leadingKey = $value;
        }*/
        return $leadingId;/*

        $queryLeading = 'SELECT ' . $_SESSION['lang'] . '_name_seo AS name_seo, ' . $_SESSION['lang'] . '_name AS name, menu_id 
                    FROM ' . TABLE_PREFIX . 'menu  
                    WHERE 1 AND menu_id = "' . $leadingId . '";';
        if($resultLeading = mysql_query($queryLeading)) {
            if(mysql_num_rows($resultLeading) == 1) {
                $output = mysql_fetch_object($resultLeading);
            }
        }
        else echo 'Error (' . mysql_errno() . '): ' . mysql_error();*/
    }
    else {
        var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $id . ')');
    }
    //return $output;
}
function getParentId($menu_id) {
    if (is_numeric($menu_id)) {
        $query = 'SELECT left_menu_id, child_of  
                    FROM ' . TABLE_PREFIX . 'menu
                    WHERE 1 AND menu_id = "' . $menu_id . '" ';
        
        if($result = mysql_query($query)) {
            if(mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    $leftMenuArray = explode(';', $row['left_menu_id']);
                    $values = array_values($leftMenuArray);
                    $parentId = $values[0];
                }
            }
        }
        else echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        return $parentId;
    }
    else {
        var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $id . ')');
    }
}
function isPraParent($menu_id) {
    global $navigateId;
    
    if (is_numeric($menu_id)) {
        $query = 'SELECT left_menu_id, child_of  
                    FROM ' . TABLE_PREFIX . 'menu
                    WHERE 1 AND menu_id = "' . $navigateId . '" ';
        
        if($result = mysql_query($query)) {
            if(mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    $leftMenuArray = explode(';', $row['left_menu_id']);
                }
            }
        }
        else echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        if (in_array($menu_id, $leftMenuArray))
            return TRUE;
        else
            return FALSE;
    }
    else {
        var_deb('Metóda vyžaduje číselnu hodnotu', 'Chyba (' . $id . ')');
    }

}

function getNumberOfProductInStock($product_id = NULL, $stock_taking = NULL) {

        if ($product_id == NULL)
            $product_id = $_GET['product_id'];
        if ($product_id == NULL)
            $product_id = $this->get_product_id();
        if ($stock_taking == NULL) {
            $query = 'SELECT stock_taking FROM ' . TABLE_PREFIX . 'product WHERE 1 AND product_id = ' . $product_id . ';';
            if ($result = mysql_query($query)) {
                while ($row = mysql_fetch_object($result))
                    $stock_taking = (int)$row->stock_taking;
            }
            else
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }

        if($stock_taking == 1) {
            $query_product = 'SELECT COUNT(product_id) AS pocet
                          FROM ' . TABLE_PREFIX . 'product_stock
                          WHERE product_id = ' . $product_id . ' 
                          AND (order_type_id IS NULL OR order_type_id IN ("5"))';
        }
        else {
            $query_product = 'SELECT pocet
                          FROM ' . TABLE_PREFIX . 'product_type
                          WHERE product_id = ' . $product_id . '';
        }
        

        if ($result_product = mysql_query($query_product)) {
            while ($row_product = mysql_fetch_object($result_product))
                return $row_product->pocet;
            mysql_free_result($result_product);
        }
        else
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    }

/*
 * findLongestStringFromArray
 * nájde a vráti najdlhší string v poli
 */
function findLongestStringFromArray($array = array()) {
    if (!empty($array)) {
        $lengths = array_map('strlen', $array);
        $maxLength = max($lengths);
        $key = array_search($maxLength, $lengths);
        return array('length' => $maxLength, 'key' => $key, 'string' => $array[$key]);
    }
}

function getProductNumberByManufacturer($id) {
    $query = 'SELECT product_id FROM ' . TABLE_PREFIX . 'product 
            WHERE 1 
            AND manufacturer_id = ' . (int)$id . ';';
    if($result = mysql_query($query)) {
        return mysql_num_rows($result);
    }
    else
        echo 'Error (' . mysql_errno() . '): ' . mysql_error();
}

function nextInvoiceID() {

    $query = 'SELECT MAX(invoice) 
                FROM ' . TABLE_PREFIX . 'order
                WHERE 1 ';
        
    if($result = mysql_query($query)) {
        if(mysql_num_rows($result) == 1) {
            $row = mysql_fetch_row($result);
            $invoice = $row[0] + 1;
        }
    }
    else echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    
    return $invoice;
}
function fileWithLastChange($file) {
    if (file_exists($file)) {
        $date = filemtime($file);
        clearstatcache();
        return $file . '?v=' . date('Ymdhis', $date);
    }
    else
        return $file;
}


/* TRUSTPAY */
//
// getSign
// trustpay hash funkcia
//
function getSign($key, $message) {
    return strtoupper(hash_hmac('sha256', pack('A*', $message), pack('A*', $key)));
}
//
// trustPayTransferLink
// generuje link na trustpay
//
function trustPayTransferLink($price, $reference) {
    $message = TRUSTPAY_AID . $price . TRUSTPAY_CURRENCY . $reference;
    return 'https://ib.trustpay.eu/mapi/pay.aspx?AID=' . TRUSTPAY_AID . '&AMT=' . $price . '&CUR=' . TRUSTPAY_CURRENCY . '&REF=' . $reference . '&SIG=' . getSign(TRUSTPAY_KEY, $message) . '&RURL=' . TRUSTPAY_URL_SUCCESS . '&CURL=' . TRUSTPAY_URL_CANCEL . '&EURL=' . TRUSTPAY_URL_ERROR;
}
function trustPayCreditLink($price, $reference) {
    $message = TRUSTPAY_AID . $price . TRUSTPAY_CURRENCY . $reference;
    return 'https://ib.trustpay.eu/mapi/cardpayments.aspx?AID=' . TRUSTPAY_AID . '&AMT=' . $price . '&CUR=' . TRUSTPAY_CURRENCY . '&REF=' . $reference . '&SIG=' . getSign(TRUSTPAY_KEY, $message) . '&RURL=' . TRUSTPAY_URL_SUCCESS . '&CURL=' . TRUSTPAY_URL_CANCEL . '&EURL=' . TRUSTPAY_URL_ERROR;
}
//https://ib.test.trustpay.eu/mapi/pay.aspx

//
// trustPayCode
// prekladá error kódy
//
function trustPayCode($code) {
    if (empty($code) AND $code !== '0') {
        return 'false code';
    }
    $return = array(
        '0' => 'Success',
        '1' => 'Pending',
        '2' => 'Announced',
        '3' => 'Authorized',
        '4' => 'Processing',
        '5' => 'AuthorizedOnly – reserved for future use',
        '1001' => 'Invalid request',
        '1002' => 'Unknown account',
        '1003' => 'Merchant account disabled',
        '1004' => 'Invalid sign',
        '1005' => 'User cancel',
        '1007' => 'Disposable balance',
        '1008' => 'Service not allowed',
        '1009' => 'PaySafeCard timeout',
        '1010' => 'Transaction not found',
        '1011' => 'Unsupported transaction',
        '1100' => 'General Error',
        '1101' => 'Unsupported currency conversion'
    );
    if (empty($return[$code])) {
        return 'no-code';
    } else {
        return $return[$code];
    }
}
?>