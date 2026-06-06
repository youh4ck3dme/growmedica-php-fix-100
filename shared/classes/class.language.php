<?

class cLanguage {

    public function __construct() {

    }

    public function getLanguageCodes() {
        $return = array();
        $queryString = "SELECT code, name FROM " . TABLE_PREFIX . "language WHERE 1;";
        if ($result = mysql_query($queryString)) {
            while ($row = mysql_fetch_object($result)) {
                $return[$row->name] = $row->code;
            }
        }
        return $return;
    }

    public function renderLanguageSwitch() {

        $queryString = "SELECT code, name FROM " . TABLE_PREFIX . "language WHERE 1;";
        $output = '<div id="language-selector">';
        //$output = $cTranslator->getTranslation("Vyberte jazyk");
        $output .= '<ul>';
        if ($result = mysql_query($queryString)) {
            while ($row = mysql_fetch_object($result)) {
                $output .= '<li class="ico-' . $row->code . '"><a href="' . ROOTDIR . '?lang=' . $row->code . '">' . $row->code . '</a></li>';
            }
        }
        $output .= '</ul>';
        $output .= '</div>';

        return $output;
    }

}

?>
