<?

class String {

    public function String() {

    }

    public static function SEOFriendlyText($input) {
        $ochr = array("%", "/", "á", "ä", "č", "ď", "é", "í", "ľ", "ĺ", "ň", "ó", "ô", "ř", "ŕ", "š", "ť", "ú", "ý", "ž", " ", "Á", "Č", "Ď", "É", "Í", "Ľ", "Ĺ", "Ň", "Ó", "Ř", "?", "Š", "Ť", "Ú", "Ý", "Ž", "é", "ě", "č", "á", "", "", "&", "", ",", "ů", "?", "!", ".", "+");
        $rchr = array("%25", "/", "a", "a", "c", "d", "e", "i", "l", "l", "n", "o", "o", "r", "r", "s", "t", "u", "y", "z", "-", "A", "C", "D", "E", "I", "L", "L", "N", "O", "R", "?", "S", "T", "U", "Y", "Z", "e", "e", "c", "a", "o", "u", "-and-", "e", "-", "u", "", "", "-", "-pls-");

        for ($i = 0; $i < sizeof($ochr); $i++) {
            $input = str_replace($ochr[$i], $rchr[$i], $input);
        }
        //return strtolower($input);
        return trim(preg_replace('![^a-z0-9]+!i', '-', strtolower($input)), '-');
    }

    //
    // používa sa?????????????????????????
    //
    public static function diakritika($input) {
        $ochr = array("á", "ä", "č", "ď", "é", "í", "ľ", "ĺ", "ň", "ó", "ô", "ř", "ŕ", "š", "ť", "ú", "ý", "ž", " ", "Á", "Č", "Ď", "É", "Í", "Ľ", "Ĺ", "Ň", "Ó", "Ř", "?", "Š", "Ť", "Ú", "Ý", "Ž", "é", "ě", "č", "á", "ô", "ú");
        $rchr = array("a", "a", "c", "d", "e", "i", "l", "l", "n", "o", "o", "r", "r", "s", "t", "u", "y", "z", " ", "A", "C", "D", "E", "I", "L", "L", "N", "O", "R", "?", "S", "T", "U", "Y", "Z", "e", "e", "c", "a", "o", "u");

        for ($i = 0; $i < sizeof($ochr); $i++) {
            $input = str_replace($ochr[$i], $rchr[$i], $input);
        }
        return $input;
    }

    public static function pageContent($id) {
        $content_query = mysql_query('SELECT ' . $_SESSION['lang'] . '_content AS content FROM ' . TABLE_PREFIX . 'menu WHERE 1 AND menu_id="' . mysql_real_escape_string($id) . '"');
        if ($content_query) {
            $content = mysql_fetch_assoc($content_query);
            return $content['content'];
        } else {
            return mysql_error();
        }
    }

}

?>