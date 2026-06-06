<?php

if (!class_exists('cLanguage')) {
    require_once("class.language.php");
    $cLanguage = new cLanguage();
}

/**
 * 	translacna trieda
 */
class Translator {

    function Translator() {
        //	Nothing todo
    }

    function addTranslation($label) {
        global $cLanguage;
        foreach ($cLanguage->getLanguageCodes() as $key => $val) {
            $queryInclude1 .= '`desc_' . $val . '`,';
            $queryInclude2 .= '\'' . mysql_real_escape_string(trim($label)) . '\',';
        }
        $query = "INSERT INTO " . TABLE_PREFIX . "translation
			(" . $queryInclude1 . "label_name)
                    VALUES
                        (" . $queryInclude2 . "'" . mysql_real_escape_string(trim($label)) . "');";

        $result = mysql_query($query);
        if ($result) {
            return true;
        } else {
            die(mysql_error());
        }
        @mysql_free_result($result);
    }

    function getTranslation($label, $display = 1) {
        global $user;
        global $cLanguage;
        global $navigateId;

        $query = "SELECT translation_id AS id, desc_" . $_SESSION['lang'] . " AS descr
                  FROM " . TABLE_PREFIX . "translation
                  WHERE 1 AND label_name='" . mysql_real_escape_string(/* diakritikaFileName_v2 */trim($label)) . "';";

        $result = mysql_query($query);
        if ($result) {
            if (mysql_num_rows($result) == 0) {
                $this->addTranslation($label);
                return $this->getTranslation($label);
            } else {
                $row = mysql_fetch_object($result);
                $return = str_replace("\n", " ", html_entity_decode($row->{'descr'}, ENT_QUOTES, "UTF-8"));
                if ($user->isAdmin() AND $display == 1) {
                    $return .= '<span class="translation_edit" rel="' . str_replace('\n', ' ', html_entity_decode($row->{'descr'}, ENT_QUOTES, 'UTF-8')) . '" onclick="javascript:openPopupWindow(\'translation\', ' . $row->{'id'} . ', 810, 400);return false;"></span>'; // href='javascript:;'
                }
                return $return;
            }
        } else {
            die(mysql_error());
        }
        @mysql_free_result($resultA);
    }

}

?>
