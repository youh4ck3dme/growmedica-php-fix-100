<?php

/*
  Trieda Installator
  Skontroluje ci existuju tabulky eshopu a ak nie, tak ich vytvori
 */

class Installator {

    static function checkIfTableExist() {
        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'color` (
			  `color_id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
			  `code` varchar(7) CHARACTER SET utf8 NOT NULL,
			  PRIMARY KEY (`color_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'delivery_payment_rel` (
			  `rel_id` int(11) NOT NULL AUTO_INCREMENT,
			  `delivery_type_id` int(11) NOT NULL,
			  `payment_type_id` int(11) NOT NULL,
			  PRIMARY KEY (`rel_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'delivery_type` (
			  `delivery_type_id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `price` float NOT NULL DEFAULT \'0\',
			  `default_choice` enum(\'1\',\'0\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'0\',
			  `price_eur` float NOT NULL,
			  PRIMARY KEY (`delivery_type_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'image_category` (
			  `image_category_id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `product_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`image_category_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'image_file` (
			  `image_file_id` int(11) NOT NULL AUTO_INCREMENT,
			  `file_name` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `full_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `image_category_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`image_file_id`),
			  KEY `image_category_id` (`image_category_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'manufacturer` (
			  `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT,
			  `sk_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `en_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
			  `de_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `hu_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `doplnok` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `logo` varchar(200) CHARACTER SET utf8 NOT NULL,
			  PRIMARY KEY (`manufacturer_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'order` (
			  `order_id` int(11) NOT NULL AUTO_INCREMENT,
			  `amount` int(11) NOT NULL,
			  `user_id` int(11) DEFAULT NULL,
			  `order_preview` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `to_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `to_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `delivery_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  `payment_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  `order_state_id` int(11) DEFAULT NULL,
			  `order_vs` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT \'variabilný symbol objednávky\',
			  `fullname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `price_total` float NOT NULL,
			  `date_o` datetime NOT NULL,
			  `date_e` datetime NOT NULL,
			  `checked` enum(\'1\',\'0\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'0\',
			  `var_symbol` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`order_id`),
			  KEY `user_id` (`user_id`),
			  KEY `order_state_id` (`order_state_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'order_product` (
			  `order_product_id` int(11) NOT NULL AUTO_INCREMENT,
			  `order_id` int(11) DEFAULT NULL,
			  `user_id` int(11) DEFAULT NULL,
			  `product_id` int(11) DEFAULT NULL,
			  `amount` int(11) DEFAULT NULL,
			  `price` float NOT NULL,
			  `color` varchar(100) CHARACTER SET utf8 COLLATE utf8_slovak_ci NOT NULL,
			  `size` int(11) DEFAULT NULL,
			  PRIMARY KEY (`order_product_id`),
			  KEY `order_id` (`order_id`),
			  KEY `user_id` (`user_id`),
			  KEY `product_id` (`product_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'order_state` (
			  `order_state_id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
			  PRIMARY KEY (`order_state_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'payment_type` (
			  `payment_type_id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `price` float NOT NULL DEFAULT \'0\',
			  `default_choice` enum(\'1\',\'0\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'0\',
			  `price_eur` float NOT NULL,
			  `description` text CHARACTER SET utf8 COLLATE utf8_slovak_ci NOT NULL,
  			  `payment_action` enum(\'credit\',\'cash\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'cash\',
			  PRIMARY KEY (`payment_type_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'product` (
			  `product_id` int(11) NOT NULL AUTO_INCREMENT,
			  `sk_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
			  `sk_name_seo` text COLLATE utf8_unicode_ci,
			  `sk_description` text COLLATE utf8_unicode_ci NOT NULL,
			  `sk_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `en_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `en_name_seo` text COLLATE utf8_unicode_ci,
			  `en_description` text COLLATE utf8_unicode_ci NOT NULL,
			  `en_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `de_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
			  `de_name_seo` text COLLATE utf8_unicode_ci NOT NULL,
			  `de_description` text COLLATE utf8_unicode_ci NOT NULL,
			  `de_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `hu_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
			  `hu_name_seo` text COLLATE utf8_unicode_ci NOT NULL,
			  `hu_description` text COLLATE utf8_unicode_ci NOT NULL,
			  `hu_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `menu_id` int(11) DEFAULT NULL,
			  `code_1` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT \'kód produktu\',
			  `image_src` text COLLATE utf8_unicode_ci NOT NULL,
			  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `date` date DEFAULT NULL COMMENT \'dátum kedy bol produkt pridaný do systému\',
			  `available` enum(\'1\',\'0\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'1\' COMMENT \'produkt je možné objednať\',
			  `skladom` enum(\'1\',\'0\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'1\',
			  `manufacturer_id` int(11) DEFAULT NULL,
			  `price` float NOT NULL,
			  `price_old` float NOT NULL COMMENT \'pôvodná cena produktu\',
			  `deleted` enum(\'0\',\'1\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'0\' COMMENT \'ak je produkt označený ako zmazaný, nebude ho možne viac objednať\',
			  `recommend` enum(\'0\',\'1\') COLLATE utf8_unicode_ci NOT NULL,
			  `sorter` int(11) NOT NULL,
			  `upravene` int(11) NOT NULL DEFAULT \'0\',
			  `rating_value` int(11) NOT NULL DEFAULT \'0\',
			  `rating_count` int(11) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`product_id`),
			  KEY `manufacturer_id` (`manufacturer_id`),
			  KEY `menu_id` (`menu_id`),
			  KEY `for_sale` (`available`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'product_color` (
			  `product_color_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `product_id` int(11) unsigned NOT NULL,
			  `color_id` int(11) NOT NULL,
			  `src` text CHARACTER SET utf8,
			  `univerzal` enum(\'0\',\'1\') CHARACTER SET utf8 NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`product_color_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'product_files` (
			  `file_id` int(11) NOT NULL AUTO_INCREMENT,
			  `product_id` int(11) NOT NULL,
			  `name` varchar(100) NOT NULL,
			  `ext` varchar(10) NOT NULL,
			  `src` varchar(150) NOT NULL,
			  `description` text NOT NULL,
			  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `sorter` int(11) NOT NULL,
			  PRIMARY KEY (`file_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'product_menu` (
			  `product_menu_id` int(11) NOT NULL AUTO_INCREMENT,
			  `product_id` int(11) DEFAULT NULL,
			  `menu_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`product_menu_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'product_type` (
			  `product_type_id` int(11) NOT NULL AUTO_INCREMENT,
			  `product_id` int(11) DEFAULT NULL,
			  `product_color_id` int(11) unsigned NOT NULL,
			  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `univerzal` enum(\'1\',\'0\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'0\',
			  `pocet` int(6) NOT NULL,
			  PRIMARY KEY (`product_type_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . 'user_address_book` (
			  `user_address_book_id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `city1` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `city2` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `fname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `lname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `address2` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `address1` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `state1` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `state2` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `psc1` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `psc2` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  `mobil` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `ico` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  `dic` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  `cname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `utvar` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `divizia` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `cislo_kancelarie` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
			  PRIMARY KEY (`user_address_book_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . '_docs_category` (
			  `docs_category_id` int(11) NOT NULL AUTO_INCREMENT,
			  `category_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`docs_category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . '_photo_category` (
			  `photo_category_id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `owner` int(10) unsigned DEFAULT NULL,
			  `_date` datetime NOT NULL,
			  `name_seo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `sk_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `sk_name_seo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `en_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `en_name_seo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `cz_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `cz_name_seo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `hu_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `hu_name_seo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `de_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `de_name_seo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `pl_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `pl_name_seo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `ru_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `ru_name_seo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `fr_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `fr_name_seo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`photo_category_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }

        /*         * ****************************************************************** */

        $queryTable = '
			CREATE TABLE IF NOT EXISTS `' . TABLE_PREFIX . '_photo_images` (
			  `photo_images_id` int(11) NOT NULL AUTO_INCREMENT,
			  `photo_category_id` int(11) DEFAULT NULL,
			  `menu_id` int(11) NOT NULL,
			  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `src` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `image_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `owner` int(10) unsigned DEFAULT NULL,
			  `date` datetime NOT NULL,
			  `sorter` int(11) NOT NULL,
			  PRIMARY KEY (`photo_images_id`),
			  KEY `photo_category_id` (`photo_category_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		';

        if (mysql_query($queryTable)) {

        } else {
            echo 'Error #' . mysql_errno() . ' @ checkIfTableExist: ' . mysql_error();
        }
    }

}

/*
  Trieda Categories
  Trieda, ktora popisuje kategorie a podkategorie

  array 		_categories	-	zoznam kategorii
 */

interface iCategories {

    public function get_categories();      //	just getter

    public function set_categories($value);     //	just setter

    public function find_subcategories($menu_id = NULL); //	metoda, ktora vyhladava podkategorie volanej kategorie; rekurzivna metoda
}

class Categories implements iCategories {

    private $_categories;

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_categories() {
        return $this->_categories;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function set_categories($value) {
        $this->_categories = $value;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function find_subcategories($menu_id = NULL) {
        $query = 'SELECT menu_id, sk_name, m.module FROM ' . TABLE_PREFIX . 'menu JOIN ' . TABLE_PREFIX . 'module AS m USING (module_id) WHERE child_of = ' . addslashes($menu_id) . ' AND module = "eshop"';

        if ($result = mysql_query($query)) {
            while ($row = mysql_fetch_object($result)) {
                $this->_categories[] = array('id' => $row->menu_id, 'name' => $row->sk_name);
                $this->find_subcategories($row->menu_id);
            }
        } else
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    }

}

/*
 * ******************************************************************************************************************************************
 * ******************************************************************************************************************************************
 * ******************************************************************************************************************************************
 */
/*
  Trieda Catalogue
  Trieda, ktora popisuje katalog produktov, jeho kategorie a podkategorie

  string 		_product_deleted	-	parameter "deleted" pre select produktov
  string 		_product_available	-	parameter "available" pre select produktov
  string 		_menu_hidden		-	parameter "hidden" pre select kategorie katalogu
  integer 	_catalogue_limit	-	LIMIT pri zostavovani katalogu
  string 		_catalogue_order	-	ORDER BY pri zostavovani katalogu
  array 		_catalogue_menu_id	-	pole kategorii pri zostavovani katalogu
  string 		lang 				-	aktualna jazykova mutacia
 */

interface iCatalogue {

    public function __construct();

    public function get_product_deleted();

    public function get_product_available();

    public function get_catalogue_limit();

    public function get_catalogue_order();

    public function get_catalogue_menu_id();

    public function get_catalogue_page();

    public function get_menu_hidden();

    public function get_dph_price_visibility();

    public function set_product_deleted($value);

    public function set_product_available($value);

    public function set_catalogue_limit($value);

    public function set_catalogue_order($value);

    public function set_catalogue_menu_id($value = NULL);

    public function set_catalogue_page($value);

    public function set_menu_hidden($value);

    public function set_dph_price_visibility($value);

    public function set_manufacturer($value);

    public function get_catalogue();

    public function get_category_name();

    public function generate_sorting_form();

    public function submit_search_form();

    public function generate_search_form();

    public function catalogue_paginator();

    public function display_recommended($value);

    public function alsoBuyedProducts($product_id);

    public function bestSellingProducts();
}

class Catalogue implements iCatalogue {

    private $_product_deleted;
    private $_product_available;
    private $_menu_hidden;
    private $_catalogue_limit;
    private $_catalogue_order;
    private $_catalogue_menu_id;
    private $_catalogue_page;
    private $_catalogue_pages_count;
    private $_catalogue_items_count;
    private $_dph_price_visibility;
    private $_manufacturer;
    private $_fulltext_ids;
    static $lang;

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Konstrukt triedy, ktory nastavuje parametre pre select produktov pri zostavovani katalogu.
      Metody set_product_* nastavuju parametre viditelnosti produktu vo WHERE.
      Metody set_catalogue_* nastavuju LIMIT a ORDER BY.

      Ak ich treba nastavit inak, tak sa to urobi tu alebo hned po vytvoreni objektu nizsie v kode.
     */

    public function __construct() {
        global $navigateArrayUrlWithoutBase;

        Installator::checkIfTableExist();

        $this->set_product_deleted(0);
        $this->set_product_available(1);

        $this->set_menu_hidden(0);

        $this->lang = $_SESSION['lang'];

        if ($navigateArrayUrlWithoutBase[0] == 'strana') {
            $this->set_catalogue_page($navigateArrayUrlWithoutBase[1]);
        } else {
            $this->set_catalogue_page(1);
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Get metody pre pristup k vlastnostiam triedy.
     */

    public function get_product_deleted() {
        return $this->_product_deleted;
    }

    public function get_product_available() {
        return $this->_product_available;
    }

    public function get_catalogue_limit() {
        return $this->_catalogue_limit;
    }

    public function get_catalogue_order() {
        return $this->_catalogue_order;
    }

    public function get_catalogue_menu_id() {
        return $this->_catalogue_menu_id;
    }

    public function get_catalogue_page() {
        return $this->_catalogue_page;
    }

    public function get_catalogue_pages_count() {
        return $this->_catalogue_pages_count;
    }

    public function get_catalogue_items_count() {
        return $this->_catalogue_items_count;
    }

    public function get_menu_hidden() {
        return $this->_menu_hidden;
    }

    public function get_dph_price_visibility() {
        return $this->_dph_price_visibility;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Set metody pre zadanie hodnot vlastnostiam triedy.
     */

    public function set_product_deleted($value) {
        $this->_product_deleted = $value;
    }

    public function set_product_available($value) {
        $this->_product_available = $value;
    }

    public function set_catalogue_limit($value) {
        $this->_catalogue_limit = $value;
    }

    public function set_catalogue_order($value) {
        $this->_catalogue_order = $value;
    }

    public function set_catalogue_menu_id($value = NULL) {
        global $navigateId;

        if (empty($value)) {
            $this->_catalogue_menu_id[] = $navigateId;
            $this->find_subcategories($navigateId);
        } else {
            $this->_catalogue_menu_id[] = $value;
            $this->find_subcategories($value);
        }
    }

    public function set_catalogue_page($value) {
        $this->_catalogue_page = $value;
    }

    public function set_catalogue_pages_count($value) {
        if($this->_catalogue_limit != 0)
        	$this->_catalogue_pages_count = ceil($value / $this->_catalogue_limit);
    }

    public function set_catalogue_items_count($value) {
        $this->_catalogue_items_count = $value;
    }

    public function set_menu_hidden($value) {
        $this->_menu_hidden = $value;
    }

    public function set_dph_price_visibility($value) {
        if (is_bool($value)) {
            $this->_dph_price_visibility = $value;
        } else {
            $this->_dph_price_visibility = false;
        }
    }

    public function set_manufacturer($value) {
        $this->_manufacturer = $value;
    }

    public function set_filter($type) {
        $array = array('new', 'action', 'sale', 'recommend', 'novelty');
        if(in_array($type, $array)) {
        	switch ($type) {
    			case 'new':
    				$this->_filter = ' AND p.date > DATE_SUB(NOW(), INTERVAL ' . NEW_PRODUCT_LENGTH . ') ';
    				break;
    			case 'action':
    				$this->_filter = ' AND (' . $type . ' = "1" OR (p.price_old > 0 AND p.price < p.price_old)) ';
    				break;
    			default:
    				$this->_filter = ' AND ' . $type . ' = "1" ';
    				break;
        	}
        }
    /*    else {
            throw new ErrorException("Class:Eshop,Method:set_specLabels,Error:Invadlid parameter, paremeter must value  action or	sale or recommend");
        }*/

    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Rekurzivna metoda pre vytvorenie zoznamu podkategorii zvolenej kategorie katalogu.
      Zoznam sa uklada do pola _catalogue_menu_id.
     */

    private function find_subcategories($menu_id = NULL) {
        $query = 'SELECT menu_id FROM ' . TABLE_PREFIX . 'menu WHERE child_of = ' . addslashes($menu_id) . '';

        if ($result = mysql_query($query)) {
            while ($row = mysql_fetch_object($result)) {
                $this->_catalogue_menu_id[] = $row->menu_id;
                $this->find_subcategories($row->menu_id);
            }
        } else
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Metoda pre zostavenie katalogu.
      Vyuziva vlastnosti triedy get_product_* a get_catalogue_*
      Vysledok vracia ako pole s objektom.
     */

    public function get_catalogue() {
        global $user;

        $query_products = 'SELECT product_id FROM ' . TABLE_PREFIX . 'product AS p' .
                (($this->get_catalogue_menu_id() != NULL) ? ' JOIN ' . TABLE_PREFIX . 'product_menu AS pm USING (product_id) ' : '') . '
                            WHERE' .
                (($this->get_catalogue_menu_id() != NULL) ? ' pm.menu_id IN ("' . implode('", "', $this->get_catalogue_menu_id()) . '")' : ' 1') .
                (($this->_fulltext_ids != NULL) ? ' AND p.product_id IN (' . implode(',', $this->_fulltext_ids) . ')' : '') . '
                            AND
                                deleted="' . $this->get_product_deleted() . '"
                            AND
                                available="1"' .
                $this->_filter .
                ($this->_manufacturer ? 'AND manufacturer_id="' . $this->_manufacturer . '" ' : '') . '
                            GROUP BY p.product_id;';
        if ($result_products = mysql_query($query_products)) {
            $this->set_catalogue_pages_count(mysql_num_rows($result_products));
            $this->set_catalogue_items_count(mysql_num_rows($result_products));
        }

        $query_products = 'SELECT p.product_id,
                                p.' . $this->lang . '_name AS name,
                                p.' . $this->lang . '_description AS description,
                                p.' . $this->lang . '_keywords AS keywords,
                                p.price as price1,
                                p.available,
                                p.code_1,
                                p.code_ean,
                                p.price,
                                p.price_old,
                                if(p.price_old != 0, p.price_old, p.price) AS discount_price_w_vat,
                                p.image_src,
                                p.manufacturer_id,
                                p.action,
                                p.sale,
                                p.recommend,
                                p.novelty,
                                p.delivery_time,
                                p.date' .
                (($this->get_catalogue_menu_id() != NULL) ? ', pm.menu_id' : '') . '
                        FROM ' . TABLE_PREFIX . 'product AS p' .
                (($this->get_catalogue_menu_id() != NULL) ? ' JOIN ' . TABLE_PREFIX . 'product_menu AS pm USING (product_id) ' : '') . '
                        WHERE' .
                (($this->get_catalogue_menu_id() != NULL) ? ' pm.menu_id IN ("' . implode('", "', $this->get_catalogue_menu_id()) . '")' : ' 1') .
                (($this->_fulltext_ids != NULL) ? ' AND p.product_id IN (' . implode(',', $this->_fulltext_ids) . ')' : '') .
                (($this->recommended === true) ? ' AND p.recommend="1"' : '') . '
                        AND
                            deleted="' . $this->get_product_deleted() . '"
                        AND
                            available="1" ' .
                $this->_filter .
                ($this->_manufacturer ? 'AND manufacturer_id="' . $this->_manufacturer . '" ' : '') . '
                        GROUP BY p.product_id
                        ORDER BY ' . $this->get_catalogue_order() .
                        ($this->get_catalogue_limit() != 0 ? ' LIMIT ' . (($this->get_catalogue_page() - 1) * $this->get_catalogue_limit()) . ',' . $this->get_catalogue_limit() : '') . ';';


        if ($result_products = mysql_query($query_products)) {
            while ($row_products = mysql_fetch_object($result_products)) {
                if (!empty($this->_fulltext_ids)) {
                    if (in_array($row_products->product_id, $this->_fulltext_ids)) {
                        $output[] = $row_products;
                    }
                } else {
                    $output[] = $row_products;
                }
            }

            if (!empty($output)) {
                return $output;
            } else {
                return array();
            }
        } else {
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Metoda vracia nazov aktualnej kategorie.
     */

    public function get_category_name() {
        global $navigateEnd;
        global $navigateId;


        if (strpos($_GET['param'], 'strana')) {
            $navigateEnd = end(explode('/', rtrim(substr($_GET['param'], 0, strpos($_GET['param'], 'strana')), '/')));
        }

        if (is_numeric($navigateEnd)) {
            $query_name = 'SELECT ' . $this->lang . '_name AS name FROM ' . TABLE_PREFIX . 'menu WHERE menu_id = ' . $navigateEnd;

            if ($result_name = mysql_query($query_name)) {
                while ($row_name = mysql_fetch_object($result_name))
                    return $row_name->name;
            } else
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        } else
            return Menu::getHyperLinkTextById($navigateId);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function generate_sorting_form() {
        global $cTranslator;
        global $user;


        return '
			<form id="sorting-form" name="sorting" method="post" class="row">
                <div class="sorters-container col-lg-4 col-lg-pull-4 col-md-4 col-md-pull-5 col-sm-4 col-sm-pull-4 col-xs-6">
                            ' . $cTranslator->getTranslation("Zoradiť podľa :", 0) . '<br />
                            <select name="sort_by">
                            	<option' . (($_SESSION['userPrefs']['orderBy'] == 'sorter') ? ' selected="selected"' : '') . ' value="sorter">---</option>
                                <option' . (($_SESSION['userPrefs']['orderBy'] == 'default') ? ' selected="selected"' : '') . ' value="default">' . $cTranslator->getTranslation("príznaku", 0) . '</option>
                                <option' . (($_SESSION['userPrefs']['orderBy'] == 'price' OR $_SESSION['userPrefs']['orderBy'] == 'discount_price_w_vat') ? ' selected="selected"' : '') . ' value="price">' . $cTranslator->getTranslation("ceny", 0) . '</option>
                                <option' . (($_SESSION['userPrefs']['orderBy'] == 'name') ? ' selected="selected"' : '') . ' value="name">' . $cTranslator->getTranslation("abecedne", 0) . '</option>
								<option' . (($_SESSION['userPrefs']['orderBy'] == 'product_id') ? ' selected="selected"' : '') . ' value="product_id">' . $cTranslator->getTranslation("dátumu pridania", 0) . '</option>
                            </select>
                            <select name="dir">
                                <option' . (($_SESSION['userPrefs']['orderSort'] == 'ASC') ? ' selected="selected"' : '') . ' value="ASC">A - Z</option>
								<option' . (($_SESSION['userPrefs']['orderSort'] == 'DESC') ? ' selected="selected"' : '') . ' value="DESC">Z - A</option>
                            </select>
                </div>
                <div class="filter-container form-inline col-lg-4 col-md-5 col-sm-4 col-sm-push-4 col-xs-12">
                	<div>
	                	<div class="checkbox col-md-6 col-sm-12 col-xs-3 col-vs-6">
							<label>
								<input type="checkbox" name="filter[]" value="novelty"' . ((in_array('novelty', $_SESSION['userPrefs']['filter']) OR $_SESSION['base']['filter'] == 'novelty') ? ' checked="checked"' : '') . '>' .
								$cTranslator->getTranslation('nový', 0) . '
							</label>
						</div>
	                	<div class="checkbox col-md-6 col-sm-12 col-xs-3 col-vs-6">
							<label>
								<input type="checkbox" name="filter[]" value="sale"' . ((in_array('sale', $_SESSION['userPrefs']['filter']) OR $_SESSION['base']['filter'] == 'sale') ? ' checked="checked"' : '') . '>' .
								$cTranslator->getTranslation('výpredaj', 0) . '
							</label>
						</div>
	                	<div class="checkbox col-md-6 col-sm-12 col-xs-3 col-vs-6">
							<label>
								<input type="checkbox" name="filter[]" value="recommend"' . ((in_array('recommend', $_SESSION['userPrefs']['filter']) OR $_SESSION['base']['filter'] == 'recommend') ? ' checked="checked"' : '') . '>' .
								$cTranslator->getTranslation('odporúčaný', 0) . '
							</label>
						</div>
	                	<div class="checkbox col-md-6 col-sm-12 col-xs-3 col-vs-6">
							<label>
								<input type="checkbox" name="filter[]" value="action"' . ((in_array('action', $_SESSION['userPrefs']['filter']) OR $_SESSION['base']['filter'] == 'action') ? ' checked="checked"' : '') . '>' .
								$cTranslator->getTranslation('akcia', 0) . '
							</label>
						</div>
					</div>
                </div>
                <div class="limit-control-container col-lg-4 col-md-3 col-sm-4 col-xs-6">
                            ' . $cTranslator->getTranslation("Počet na stránku :", 0) . '
                                <select class="catalogue_limit" name="catalogue_limit">
                                    <option value="15"' . (($this->get_catalogue_limit() == 15) ? ' selected="selected"' : '') . '>15</option>
                                    <option value="30"' . (($this->get_catalogue_limit() == 30) ? ' selected="selected"' : '') . '>30</option>
                                    <option value="60"' . (($this->get_catalogue_limit() == 60) ? ' selected="selected"' : '') . '>60</option>
                                    <option value="120"' . (($this->get_catalogue_limit() == 120) ? ' selected="selected"' : '') . '>120</option>
                                    <option value="240"' . (($this->get_catalogue_limit() == 240) ? ' selected="selected"' : '') . '>240</option>
                                </select>
                                <input type="submit" name="submit-sorting" class="button" value="OK" />
                </div>
			</form>';
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function submit_search_form() {
        if (isset($_GET['q'])) {
            //$query = 'SELECT product_id, MATCH (sk_name, sk_description, sk_keywords) AGAINST ("' . mysql_real_escape_string($_GET['q']) . '" IN BOOLEAN MODE) AS score FROM ' . TABLE_PREFIX . 'product WHERE MATCH (sk_name, sk_description, sk_keywords) AGAINST ("' . mysql_real_escape_string($_GET['q']) . '" IN BOOLEAN MODE) ORDER BY score';
            $query = 'SELECT product_id FROM ' . TABLE_PREFIX . 'product WHERE sk_name LIKE "' . mysql_real_escape_string($_GET['q']) . '" OR sk_description LIKE "' . mysql_real_escape_string($_GET['q']) . '" OR sk_keywords LIKE "' . mysql_real_escape_string($_GET['q']) . '" OR code_1 LIKE "' . mysql_real_escape_string($_GET['q']) . '";';
            if ($result = mysql_query($query)) {
                while ($row = mysql_fetch_object($result)) {
                    $this->_fulltext_ids[] = $row->product_id;
                }
            }
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function generate_search_form() {
        global $cTranslator;

        return '
			<form id="search-form" name="search" method="get" action="' . Menu::getHyperlinkById(31) . '"  >
                            <label><input type="text" name="q" id="catalogue_keyword" value="' . (!empty($_GET['q']) ? $_GET['q'] : '') . '" autocomplete="off" placeholder="' . $cTranslator->getTranslation("Zadajte kľúčové slovo", 0) . '" /></label>
                            <input class="search-button" type="submit" value="' . $cTranslator->getTranslation("Hľadať", 0) . '" />
			</form>';
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function catalogue_paginator() {
// OBSOLETE
// TO BE DELETED
// REPLACED BY CLASS PAGINATOR

        global $navigateId;
        global $cTranslator;

        if ($this->_catalogue_page == 1 && $this->_catalogue_page < $this->_catalogue_pages_count) {
            return '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/strana/' . ($this->_catalogue_page + 1) . (!empty($_GET['catalogue_keyword']) ? '?catalogue_keyword=' . $_GET['catalogue_keyword'] : '') . '">' . $cTranslator->getTranslation('Nasledujúca stránka') . '</a>';
        }

        if ($this->_catalogue_page > 1 && $this->_catalogue_page < $this->_catalogue_pages_count) {
            return '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/strana/' . ($this->_catalogue_page - 1) . (!empty($_GET['catalogue_keyword']) ? '?catalogue_keyword=' . $_GET['catalogue_keyword'] : '') . '">' . $cTranslator->getTranslation('Predošlá stránka') . '</a> | <a href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/strana/' . ($this->_catalogue_page + 1) . '">' . $cTranslator->getTranslation('Nasledujúca stránka') . '</a>';
        }

        if ($this->_catalogue_page > 1 && $this->_catalogue_page == $this->_catalogue_pages_count) {
            return '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/strana/' . ($this->_catalogue_page - 1) . (!empty($_GET['catalogue_keyword']) ? '?catalogue_keyword=' . $_GET['catalogue_keyword'] : '') . '">' . $cTranslator->getTranslation('Predošlá stránka') . '</a>';
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function display_recommended($value) {
        if (empty($value)) {
            $this->recommended = false;
        } else {
            $this->recommended = $value;
        }
    }

    public function alsoBuyedProducts($product_id) {
        if ($product_id == NULL)
            return false;

        $query = 'SELECT order_id FROM ' . TABLE_PREFIX . 'order_product WHERE 1 AND product_id="' . $product_id . '";';
        $result = mysql_query($query);
        $orders_id = array();
        while ($row = mysql_fetch_object($result)) {
            $orders_id[] = $row->order_id; //$this->get_product($row->product_id);
        }
        $query1 = 'SELECT product_id FROM ' . TABLE_PREFIX . 'order_product WHERE 1 AND  FIND_IN_SET(order_id, "' . implode(',', $orders_id) . '") AND product_id!="' . $product_id . '" GROUP BY product_id;';
        $result1 = mysql_query($query1);
        $output = array();
        while ($row1 = mysql_fetch_object($result1)) {
            $output[] = Product::get_product($row1->product_id);
        }
        return $output;
    }

    public function bestSellingProducts() {
        $query1 = 'SELECT COUNT(product_id) AS total, product_id FROM ' . TABLE_PREFIX . 'order_product
                   LEFT JOIN ' . TABLE_PREFIX . 'product USING(product_id)
                   WHERE 1 AND available="1" GROUP BY product_id ORDER BY total DESC LIMIT 10;';
        $result1 = mysql_query($query1);
        $output = array();
        while ($row1 = mysql_fetch_object($result1)) {
            $output[] = Product::get_product($row1->product_id);
        }
        return $output;
    }

}

/*
 * ******************************************************************************************************************************************
 * ******************************************************************************************************************************************
 * ******************************************************************************************************************************************
 */
/*
  Trieda Produkt
  Trieda, ktora popisuje produkty

  integer 	_product_id			-	ID produktu
  string 		_product_deleted	-	parameter "deleted" pre select produktov
  string 		_product_available	-	parameter "available" pre select produktov

  string 		lang 				-	aktualna jazykova mutacia
 */

interface iProduct {

    public function __construct();

    public function get_product_id();   //	just getter...

    public function get_product_deleted();  //	just getter...

    public function get_product_available(); //	just getter...

    public function get_dph_price_visibility();

    public function set_product_id($value);   //	just setter...

    public function set_product_deleted($value); //	just setter...

    public function set_product_available($value); //	just setter...

    public function set_dph_price_visibility($value);

    static function get_manufacturer($manufacturer_id);    //	vrati nazov a logo vyrobcu produktu

    public function get_product_name($product_id = NULL);   //	vrati nazov produktu; ak je id NULL, potom si vezme id z objektu

    public function get_product($product_id = NULL);    //	vrati detail produktu; ak je id NULL, potom si vezme id z objektu

    public function get_product_photo($product_id = NULL);   //	vrati foto produktu; ak je id NULL, potom si vezme id z objektu

    public function get_product_photogallery($product_id = NULL); //	vrati fotogaleriu produktu; ak je id NULL, potom si vezme id z objektu

    public function get_product_files($product_id = NULL);   //	vrati zoznam stiahnutelnych suborov produktu; ak je id NULL, potom si vezme id z objektu

    public function get_product_colors($product_id = NULL);   //	vrati zoznam farieb produktu; ak je id NULL, potom si vezme id z objektu

    public function get_product_sizes($product_id = NULL, $product_color_id);   //	vrati zoznam farieb produktu; ak je id NULL, potom si vezme id z objektu

    static function find_file_icon($ext);   //	najde nazov suboru ikony suboru produktu

    static function find_user_discount($price = 0); //	najde zlavu uzivatela

    public function get_color_name($color_id);

    public function get_size_name($size_id);

    public function related_product();

    public function availableProductsCount($product_id, $size_id);
}

class Product implements iProduct {

    private $_product_id;
    private $_product_deleted;
    private $_product_available;
    private $_dph_price_visibility;
    static $lang;

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Konstrukt triedy, ktory nastavuje parametre pre select produktu
      Metody set_product_* nastavuju parametre viditelnosti produktu vo WHERE.

      Ak ich treba nastavit inak, tak sa to urobi tu alebo hned po vytvoreni objektu nizsie v kode.
     */

    public function __construct() {
        global $navigateEnd;

        Installator::checkIfTableExist();

        $this->set_product_id($navigateEnd);
        $this->set_product_deleted(0);
        $this->set_product_available(1);

        $this->lang = $_SESSION['lang'];
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Get metody pre pristup k vlastnostiam triedy.
     */

    public function get_product_id() {
        return $this->_product_id;
    }

    public function get_product_deleted() {
        return $this->_product_deleted;
    }

    public function get_product_available() {
        return $this->_product_available;
    }

    public function get_dph_price_visibility() {
        return $this->_dph_price_visibility;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Set metody pre zadanie hodnot vlastnostiam triedy.
     */

    public function set_product_id($value) {
        $this->_product_id = (int) $value;
    }

    public function set_product_deleted($value) {
        $this->_product_deleted = $value;
    }

    public function set_product_available($value) {
        $this->_product_available = $value;
    }

    public function set_dph_price_visibility($value) {
        if (is_bool($value)) {
            $this->_dph_price_visibility = $value;
        } else {
            $this->_dph_price_visibility = false;
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      Metoda pre ziskanie informacii o vyrobcovi.
      Je staticka, pretoze je volana aj bez instancie triedy pri zostavovani katalogu
      Z toho dovodu ani nemozeme volat $this->lang ale musime namiesto nej pouzit jej SESSION ekvivalent
     */

    static function get_manufacturer($manufacturer_id) {
        if ($manufacturer_id > 0) {
            $query_manufacturer = 'SELECT ' . $_SESSION['lang'] . '_name AS name, logo
			                       FROM ' . TABLE_PREFIX . 'manufacturer
			                       WHERE manufacturer_id = ' . $manufacturer_id . '';

            if ($result_manufacturer = mysql_query($query_manufacturer)) {
                while ($row_manufacturer = mysql_fetch_object($result_manufacturer))
                    $output[] = $row_manufacturer;

                if (!empty($output))
                    return $output;
                else
                    return array();
            } else
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        } else
            return array();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_product_name($product_id = NULL) {
        if ($product_id == NULL)
            $product_id = $this->get_product_id();

        if (empty($this->lang))
            $lang = 'sk';
        else
            $lang = $this->lang;

        $query_product = 'SELECT ' . $lang . '_name AS name
		                  FROM ' . TABLE_PREFIX . 'product
		                  WHERE product_id = ' . $product_id . '';

        if ($result_product = mysql_query($query_product)) {
            while ($row_product = mysql_fetch_object($result_product))
                return $row_product->name;
        } else
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_product($product_id = NULL) {
        global $user;
        if ($product_id == NULL)
            $product_id = $this->get_product_id();

        $query_product = 'SELECT
                                                        product_id,
							' . $this->lang . '_name AS name,
							' . $this->lang . '_name_seo AS name_seo,
							' . $this->lang . '_description AS description,
							' . $this->lang . '_keywords AS keywords,
							manufacturer_id,
							price AS expr1,
							price,
							price_old,
							code_1,
							image_src,
							recommend,
							action,
							sale,
							novelty,
							delivery_time
		                   FROM ' . TABLE_PREFIX . 'product
		                   WHERE
		                   		product_id = ' . $product_id . '
		                   	AND
		                   		deleted = "' . $this->get_product_deleted() . '"
		                   	AND
		                   		available = "' . $this->get_product_available() . '"';

        if ($result_product = mysql_query($query_product)) {
            while ($row_product = mysql_fetch_object($result_product)) {
                return $row_product;
            }
        } else
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_product_photo($product_id = NULL) {
        if ($product_id == NULL)
            $product_id = $this->get_product_id();

        $query_product = 'SELECT image_src
		                  FROM ' . TABLE_PREFIX . 'product
		                  WHERE product_id = ' . $product_id . '';

        if ($result_product = mysql_query($query_product)) {
            while ($row_product = mysql_fetch_object($result_product))
                return $row_product->image_src;
        } else
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_product_photogallery($product_id = NULL) {
        if ($product_id == NULL)
            $product_id = $this->get_product_id();

        $query_photos = 'SELECT src FROM ' . TABLE_PREFIX . '_photo_images WHERE photo_category_id = ' . $product_id . ' ORDER BY sorter';

        if ($result_photos = mysql_query($query_photos)) {
            while ($row_photos = mysql_fetch_object($result_photos))
                $output[] = $row_photos;

            if (!empty($output))
                return $output;
            else
                return array();
        } else
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_product_files($product_id = NULL) {
        if ($product_id == NULL)
            $product_id = $this->get_product_id();

        $query_files = 'SELECT file_id as id, name, src, ext FROM ' . TABLE_PREFIX . 'product_files WHERE product_id = ' . $product_id;

        if ($result_files = mysql_query($query_files)) {
            while ($row_files = mysql_fetch_object($result_files))
                $output[] = $row_files;

            if (!empty($output))
                return $output;
            else
                return array();
        } else
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_product_colors($product_id = NULL) {
        if ($product_id == NULL)
            $product_id = $this->get_product_id();

        $query_colors = 'SELECT * FROM ' . TABLE_PREFIX . 'product_color
                         JOIN ' . TABLE_PREFIX . 'color USING(color_id)
                         WHERE product_id="' . $product_id . '"';
        if ($result_colors = mysql_query($query_colors)) {
            if (mysql_num_rows($result_colors) != 0) {
                while ($row_colors = mysql_fetch_object($result_colors)) {
                    $output[] = $row_colors;
                }

                if (!empty($output)) {
                    return $output;
                } else {
                    return array();
                }
            } else {
                $query_colors1 = 'SELECT * FROM ' . TABLE_PREFIX . 'product_color
                         WHERE product_id="' . $product_id . '"';
                if ($result_colors1 = mysql_query($query_colors1)) {
                    while ($row_colors1 = mysql_fetch_object($result_colors1)) {
                        $output[] = $row_colors1;
                    }

                    if (!empty($output)) {
                        return $output;
                    } else {
                        return array();
                    }
                }
            }
        } else {
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
        /*
          if ($product_id == NULL)
          $product_id = $this->get_product_id();

          $query_colors = 'SELECT * FROM ' . TABLE_PREFIX . 'product_color JOIN ' . TABLE_PREFIX . 'color USING(color_id) WHERE product_id = ' . $product_id;
          if ($result_colors = mysql_query($query_colors)) {
          while ($row_colors = mysql_fetch_object($result_colors))
          $output[] = $row_colors;

          if (!empty($output)) {
          $query_colors = 'SELECT pt.product_id, CONCAT(c.color_id,";",product_type_id) AS color_id, CONCAT(c.name," ",pt.name) AS name, c.code FROM ' . TABLE_PREFIX . 'product_type AS pt JOIN ' . TABLE_PREFIX . 'product_color AS pc USING(product_color_id) JOIN ' . TABLE_PREFIX . 'color AS c USING(color_id) WHERE pc.product_id = ' . $product_id;
          if ($result_colors = mysql_query($query_colors)) {
          while ($row_colors = mysql_fetch_object($result_colors))
          $output[] = $row_colors;
          }

          return $output;
          } else {
          $query_colors = 'SELECT * FROM ' . TABLE_PREFIX . 'product_color WHERE product_id = ' . $product_id;
          if ($result_colors = mysql_query($query_colors)) {
          while ($row_colors = mysql_fetch_object($result_colors))
          $output[] = $row_colors;

          if (!empty($output))
          return $output;
          else
          return array();
          }
          }
          } else
          echo 'Error (' . mysql_errno() . '): ' . mysql_error();
         */
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_product_sizes($product_id = NULL, $product_color_id) {
        if ($product_id == NULL)
            $product_id = $this->get_product_id();

        $query_colors = 'SELECT product_type_id, product_id, name, product_color_id, univerzal, SUM(pocet) AS amount FROM ' . TABLE_PREFIX . 'product_type
                                WHERE product_id="' . $product_id . '" AND product_color_id="' . $product_color_id . '" GROUP BY product_id, product_color_id ORDER BY name';
        if ($result_colors = mysql_query($query_colors)) {
            while ($row_colors = mysql_fetch_object($result_colors))
                $output[] = $row_colors;

            if (!empty($output))
                return $output;
            else
                return array();
        } else {
            echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function find_file_icon($ext) {
        $array = array(
            'revit.png' => array('rvt'),
            'archi-cad.png' => array('pla', 'pln'),
        );

        while (list($key, $val) = each($array)) {
            while (list($key2, $polozka) = each($val)) {
                if ($polozka == $ext)
                    return $key;
            }
        }

        return 'file.gif';
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function find_user_discount($price = 0) {
        $query_discount = 'SELECT discount FROM ' . TABLE_PREFIX . 'user WHERE user_id = ' . $_SESSION['user_id'];

        if ($result_discount = mysql_query($query_discount)) {
            while ($row_discount = mysql_fetch_object($result_discount))
                return round($price * (1 - ($row_discount->discount / 100)), 2);
        } else
            return round($price, 2);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_color_name($color_id) {

        $query_colors = 'SELECT c.name FROM ' . TABLE_PREFIX . 'product_color AS pc
                         JOIN ' . TABLE_PREFIX . 'color AS c USING(color_id)
                         WHERE pc.product_color_id="' . $color_id . '"';
        if ($result_colors = mysql_query($query_colors)) {
            while ($row_color = mysql_fetch_object($result_colors)) {
                return $row_color->name;
            }
        } else {
            return false;
        }
        /*
          $ids = explode(';', $color_id);

          if (isset($ids[1])) {
          $query_colors = 'SELECT CONCAT(c.name," ",pt.name) AS name FROM ' . TABLE_PREFIX . 'product_type AS pt JOIN ' . TABLE_PREFIX . 'product_color AS pc USING(product_color_id) JOIN ' . TABLE_PREFIX . 'color AS c USING(color_id) WHERE pt.product_type_id = ' . $ids[1];
          if ($result_colors = mysql_query($query_colors)) {
          while ($row_color = mysql_fetch_object($result_colors)) {
          return $row_color->name;
          }
          } else {
          return false;
          }
          } else {
          $query_colors = 'SELECT name FROM ' . TABLE_PREFIX . 'color WHERE color_id = ' . $ids[0];
          if ($result_colors = mysql_query($query_colors)) {
          while ($row_color = mysql_fetch_object($result_colors)) {
          return $row_color->name;
          }
          } else {
          return false;
          }
          }
         */
    }

    public function get_size_name($size_id) {

        $query = 'SELECT name FROM ' . TABLE_PREFIX . 'product_type
                  WHERE 1 AND product_type_id="' . $size_id . '"';
        if ($result = mysql_query($query)) {
            while ($row = mysql_fetch_object($result)) {
                return $row->name;
            }
        } else {
            return false;
        }
    }

    public function related_product() {
        $output = array();
        $query = 'SELECT product_id FROM ' . TABLE_PREFIX . 'product_related
                  LEFT JOIN ' . TABLE_PREFIX . 'product USING(product_id)
                  WHERE 1 AND related_product_id="' . $this->get_product_id() . '" AND available="1";';
        $result = mysql_query($query);
        if ($result) {
            while ($row = mysql_fetch_object($result)) {
                $output[] = $this->get_product($row->product_id);
            }
        }
        return $output;
    }

    public function availableProductsCount($product_id, $size_id) {
        if ($product_id == NULL OR $size_id == NULL)
            return false;

        $query = 'SELECT pocet AS amount FROM ' . TABLE_PREFIX . 'product_type
                  WHERE product_id="' . $product_id . '" AND product_type_id="' . $size_id . '"';
        if ($result = mysql_query($query)) {
            $row = mysql_fetch_object($result);
            return $row->amount;
        } else {
            return false;
        }
    }

}

/*
 * ******************************************************************************************************************************************
 * ******************************************************************************************************************************************
 * ******************************************************************************************************************************************
 */
/*
  Trieda Cart
  Trieda, ktora popisuje nakupny kosik a pracu s nim

  array 		items_array			-	pole, ktore drzi obsah nakupneho kosika a je serializovane do session

  string 		lang 				-	aktualna jazykova mutacia
 */

interface iCart {

    public function __construct();

    public function insert_item($product_id, $color, $size, $amount, $price_item); //	vlozi produkty do kosika ... ak sa v kosiku nachadza produkt s rovnakym id a farbou, tak iba zvysi pocet jeho kusov

    public function update_item($item_id, $amount);       //	nastavi novy pocet kusov polozky v kosiku

    public function delete_item($item_id);         //	vymaze polozku z kosika

    public function decrease_item($item_id);        //	pocet kusov produktu - 1

    public function increase_item($item_id);        //	pocet kusov produktu + 1

    public function flush_cart();           //	vyprazdni kosik

    public function get_items();   //	obsah kosika

    public function get_cart_value();  //	hodnota vsetkych predmetov v kosiku

    public function get_cart_quantity(); //	pocet predmetov v kosiku

    public function get_cart_count();  //	pocet produktov v kosiku

    public function get_cart_discount();

    public function get_cart_items();

    public function get_cart_related_product($limit);

    public function show_cart_detail();  //	vypis detailu kosika

    public function show_cart_detail_board();  //	vypis detailu kosika plošne

    public function get_delivery_types(); //	vybera z db sposoby dorucenia

    public function get_delivery_type($delivery_id); //	vybera z db sposoby dorucenia

    static function get_payment_types(); //	vybera z db sposoby platby

    static function get_payment_type($payment_id); //	vybera z db sposoby platby

    public function set_dph_price_visibility($value);

    public function get_dph_price_visibility();

    public function submit_step1();      //	ulozenie zakaznika a objednavky do databazy a odoslanie emailov

    public function submit_step2();      //	ulozenie zakaznika a objednavky do databazy a odoslanie emailov

    static function generate_order_preview($order_id, $dph_price_visibility); //	generovanie preview objednavky

    static function generate_invoice($order_id, $dph_price_visibility); //	generovanie faktúry v html

    static function generate_invoice_pdf($order_id, $dph_price_visibility); //	generovanie faktúry v pdf
}

class Cart implements iCart {

    private $items_array;
    private $_dph_price_visibility;
    static $lang;

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function __construct() {
        Installator::checkIfTableExist();

        $this->items_array = array();
        $this->lang = $_SESSION['lang'];
        $this->cart_discount;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function insert_item($product_id, $color, $size, $amount, $price_item) {
        if (!empty($_POST)) {
            if ($amount < 0) {
                $amount = 1;
            }

            $update_key = '';

            if (!empty($this->items_array)) {
                foreach ($this->items_array as $key => $value) {
                    if ($value['product_id'] == $product_id && $value['color'] == $color && $value['size'] == $size)
                        $update_key = $key;
                }
            }

            if (empty($update_key)) {
                $item_id = hash('crc32', time());

                $this->items_array[$item_id]['product_id'] = $product_id;
                $this->items_array[$item_id]['color'] = $color;
                $this->items_array[$item_id]['size'] = $size;
                $this->items_array[$item_id]['amount'] = $amount;
                $this->items_array[$item_id]['price_item'] = $price_item;
            } else {
                $this->items_array[$update_key]['amount'] += $amount;
            }
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function update_item($item_id, $amount) {
        if ($amount < 0) {
            $amount = 1;
        }

        $this->items_array[$item_id]['amount'] = $amount;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function delete_item($item_id) {
        unset($this->items_array[$item_id]);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function decrease_item($item_id) {
        if ($this->items_array[$item_id]['amount'] > 0) {
            $this->items_array[$item_id]['amount'] -= 1;
        }

// nie ELSE, ale IF, pretoze chcem aby sa tovar z kosika vymazal, ked dosiahne nulovy pocet kusov
        if ($this->items_array[$item_id]['amount'] == 0) {
            $this->delete_item($item_id);
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function increase_item($item_id) {
        $this->items_array[$item_id]['amount'] += 1;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function flush_cart() {
        $this->items_array = array();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_items() {
        return $this->items_array;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_cart_value() {
        $cart_value = 0;

        if (!empty($this->items_array)) {
            foreach ($this->items_array as $item) {
                $cart_value += $item['price_item'] * $item['amount'];
            }
        }

        return $cart_value;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_cart_quantity() {
        $cart_quantity = 0;

        if (!empty($this->items_array)) {
            foreach ($this->items_array as $item) {
                $cart_quantity += $item['amount'];
            }
        }

        return $cart_quantity;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_cart_count() {
        return count($this->items_array);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_cart_discount() {
        return $this->cart_discount;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_cart_items() {
        $cart_items = [];

        if (!empty($this->items_array)) {
            foreach ($this->items_array as $item) {
                $cart_items[] = array('product_id'=>$item['product_id'], 'amount'=>$item['amount'], 'stock'=>getNumberOfProductInStock($item['product_id']));
            }
        }

        return $cart_items;
    }

    public function get_cart_related_product($limit = 4) {
        $cart_related_products = [];
        $cart_items = [];
        if (!empty($this->items_array)) {
            foreach ($this->items_array as $item) {
                $cart_items[] = $item['product_id'];
            }
        }
        $query = 'SELECT product_id FROM ' . TABLE_PREFIX . 'product_related
                  LEFT JOIN ' . TABLE_PREFIX . 'product USING(product_id)
                  WHERE 1 AND related_product_id IN (' . implode(",", array_unique($cart_items)) . ') AND available = "1"
                  ORDER BY RAND()
                  LIMIT ' . $limit . ';';
        $result = mysql_query($query);
        if ($result) {
            while ($row = mysql_fetch_object($result)) {
            	$obj_product = new Product;
                $cart_related_products[] = $obj_product->get_product($row->product_id);
            }
        }
        return $cart_related_products;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function show_cart_detail() {
        global $cTranslator;
        global $navigateEnd;

        $output = '<table id="cart_table"><thead>';
        if ($this->_dph_price_visibility) {
            $output .= '<tr>';
            $output .= '<th></th>';
            $output .= '<th scope="col">' . $cTranslator->getTranslation('Produkt', 0) . '</th>';
            //$output .= '<th>Farba</th>';
            $output .= '<th>' . $cTranslator->getTranslation('Množstvo', 0) . '</th>';
            $output .= '<th>' . $cTranslator->getTranslation('Cena za kus bez DPH', 0) . '</th>';
            $output .= '<th>' . $cTranslator->getTranslation('Cena za kus s DPH', 0) . '</th>';
            $output .= '<th>' . $cTranslator->getTranslation('Celkom bez DPH', 0) . '</th>';
            $output .= '<th>' . $cTranslator->getTranslation('Celkom s DPH', 0) . '</th>';
            $output .= '<th></th>';
            $output .= '</tr>';
        } else {
            $output .= '<tr>';
            $output .= '<th></th>';
            $output .= '<th scope="col">' . $cTranslator->getTranslation('Produkt', 0) . '</th>';
            //$output .= '<th>Farba</th>';
            $output .= '<th>' . $cTranslator->getTranslation('Množstvo', 0) . '</th>';
            $output .= '<th>' . $cTranslator->getTranslation('Cena za kus', 0) . '</th>';
            $output .= '<th>' . $cTranslator->getTranslation('Celkom', 0) . '</th>';
            $output .= '<th></th>';
            $output .= '</tr></thead><tbody class="responsive">';
        }
        $obj_product = new Product();

        foreach ($this->items_array as $key => $item) {
        /*    $color_name = Product::get_color_name($item['color']);
            $size = $item['size'];*/

            $item_detail = $obj_product->get_product_name($item['product_id']);
            $item_photo_src = $obj_product->get_product_photo($item['product_id']);

            if (!empty($item_photo_src) AND file_exists('photos/original/' . $item_photo_src)) {
                $item_photo = '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/produkt/' . String::SEOFriendlyText($item_detail) . '/' . $item['product_id'] . '"><img src="photos/thumbnail/' . $item_photo_src . '" alt="' . $item_detail . '" width="18" /></a>';
            }

            $price_wo_vat = $item['price_item'];
            if ($this->_dph_price_visibility) {
                $output .= '<tr>';
                $output .= '<td class="item-photo">' . (!empty($item_photo) ? $item_photo : '') . '</td>';
                $output .= '<td><a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/produkt/' . String::SEOFriendlyText($item_detail) . '/' . $item['product_id'] . '">' . $item_detail;
            	/*    if ($color_name || $size != '0') {
                    $output .= ' <small>(';
                    if ($color_name) {
                        $output .= $cTranslator->getTranslation("Farba", 0) . ': ' . $color_name;
                    }
                    if ($size != '0') {
                        if ($color_name) {
                            $output .= ', ';
                        }
                        $output .= $cTranslator->getTranslation("Veľkosť", 0) . ': ' . Product::get_size_name($size);
                    }
                    $output .= ')</small>';
                }*/
                $output .= '</a></td>';
                //$output .= '<td>' . $color_name . '</td>';
                $output .= '<td>' . $item['amount'] . 'x</td>';
                $output .= '<td>' . number_format(($price_wo_vat / VAT_COEFFICIENT), 2, '.', ' ') . ' &euro;</td>';
                $output .= '<td>' . number_format($price_wo_vat, 2, '.', ' ') . ' &euro;</td>';
                $output .= '<td>' . number_format(($item['amount'] * ($price_wo_vat / VAT_COEFFICIENT)), 2, '.', ' ') . ' &euro;</td>';
                $output .= '<td><strong>' . number_format(($item['amount'] * $price_wo_vat), 2, '.', ' ') . ' &euro;</strong></td>';
                $output .= '<td class="actions">[<a title="' . $cTranslator->getTranslation('Pridať jeden kus produktu', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/increase_item/' . $key . '">' . $cTranslator->getTranslation('+', 0) . '</a>]&nbsp;';
                $output .= '[<a title="' . $cTranslator->getTranslation('Odobrať jeden kus produktu', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/decrease_item/' . $key . '">' . $cTranslator->getTranslation('-', 0) . '</a>]&nbsp;';
                $output .= '[<a title="' . $cTranslator->getTranslation('Odobrať produkt', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/delete_item/' . $key . '">' . $cTranslator->getTranslation('X', 0) . '</a>]</td>';
                $output .= '</tr>';
            } else {
                $output .= '<tr data-pid="' . $item['product_id'] . '">';
                $output .= '<td class="item-photo">' . (!empty($item_photo) ? $item_photo : '') . '</td>';
                $output .= '<td data-label="' . $cTranslator->getTranslation('Produkt', 0) . '"><a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/produkt/' . String::SEOFriendlyText($item_detail) . '/' . $item['product_id'] . '">' . $item_detail;
                /*if ($color_name || $size != '0') {
                    $output .= ' <small>(';
                    if ($color_name) {
                        $output .= $cTranslator->getTranslation("Farba", 0) . ': ' . $color_name;
                    }
                    if ($size != '0') {
                        if ($color_name) {
                            $output .= ', ';
                        }
                        $output .= $cTranslator->getTranslation("Veľkosť", 0) . ': ' . $size;
                    }
                    $output .= ')</small>';
                }*/
                $output .= '</a></td>';
                //$output .= '<td>' . $color_name . '</td>';
                $output .= '<td data-label="' . $cTranslator->getTranslation('Množstvo', 0) . '">' . $item['amount'] . 'x</td>';
                $output .= '<td data-label="' . $cTranslator->getTranslation('Cena za kus', 0) . '">' . number_format($price_wo_vat, 2, '.', ' ') . ' &euro;</td>';
                $output .= '<td data-label="' . $cTranslator->getTranslation('Celkom', 0) . '"><strong>' . number_format(($item['amount'] * $price_wo_vat), 2, '.', ' ') . ' &euro;</strong></td>';
                $output .= '<td class="actions">[<a title="' . $cTranslator->getTranslation('Pridať jeden kus produktu', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/increase_item/' . $key . '">' . $cTranslator->getTranslation('+', 0) . '</a>]&nbsp;';
                $output .= '[<a title="' . $cTranslator->getTranslation('Odobrať jeden kus produktu', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/decrease_item/' . $key . '">' . $cTranslator->getTranslation('-', 0) . '</a>]&nbsp;';
                $output .= '[<a title="' . $cTranslator->getTranslation('Odobrať produkt', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/delete_item/' . $key . '">' . $cTranslator->getTranslation('X', 0) . '</a>]</td>';
                $output .= '</tr>';
            }
        }
        unset($obj_product);

        $output .= '</tbody><tbody><tr>';
        $output .= '<td colspan="' . ($this->_dph_price_visibility ? '8' : '6') . '" class="price">';
        if ($this->_dph_price_visibility) {
            $output .= '<span>' . $cTranslator->getTranslation('Celková cena objednávky bez DPH:', 0) . ' <strong>' . number_format(($this->get_cart_value() / VAT_COEFFICIENT), 2, '.', ' ') . ' &euro;</strong></span>';
            if (User::hasRegistrationDiscount()) {
                $output .= '<span>' . $cTranslator->getTranslation('Celková cena objednávky s DPH:', 0) . ' <strong>' . number_format($this->get_cart_value(), 2, '.', ' ') . ' &euro;</strong></span>';
                $output .= '<span>' . $cTranslator->getTranslation('Zľava za registráciu:', 0) . ' <strong>' . REGISTRATION_DISCOUNT . '%</strong></span>';
                $output .= '<span>' . $cTranslator->getTranslation('Celková cena objednávky s DPH po zľave:', 0) . ' <strong>' . number_format(($this->get_cart_value() * ((100 - REGISTRATION_DISCOUNT) / 100)), 2, '.', ' ') . ' &euro;</strong></span>';
            } else {
                $output .= '<span>' . $cTranslator->getTranslation('Celková cena objednávky s DPH:', 0) . ' <strong>' . number_format($this->get_cart_value(), 2, '.', ' ') . ' &euro;</strong></span>';
            }
        } else {
            if (User::hasRegistrationDiscount()) {
                $output .= '<span>' . $cTranslator->getTranslation('Celková cena objednávky:', 0) . ' <strong>' . number_format(($this->get_cart_value()), 2, '.', ' ') . ' &euro;</strong></span>';
                $output .= '<span>' . $cTranslator->getTranslation('Zľava za registráciu:', 0) . ' <strong>' . REGISTRATION_DISCOUNT . '%</strong></span>';
                $output .= '<span>' . $cTranslator->getTranslation('Celková cena objednávky po zľave:', 0) . ' <strong>' . number_format(($this->get_cart_value() * ((100 - REGISTRATION_DISCOUNT) / 100)), 2, '.', ' ') . ' &euro;</strong></span>';
            } else {
                $output .= '<span>' . $cTranslator->getTranslation('Celková cena objednávky:', 0) . ' <strong>' . number_format($this->get_cart_value(), 2, '.', ' ') . ' &euro;</strong></span>';
            }
        }
        $output .= '</td>';
        $output .= '</tr>';


        if ($navigateEnd == 'step2') {
            $query_delivery = 'SELECT * FROM ' . TABLE_PREFIX . 'delivery_type WHERE delivery_type_id = "' . mysql_real_escape_string($_SESSION['doprava']) . '"';
            if ($result_delivery = mysql_query($query_delivery))
                $row_delivery = mysql_fetch_object($result_delivery);

            $query_payment = 'SELECT * FROM ' . TABLE_PREFIX . 'payment_type WHERE payment_type_id = "' . mysql_real_escape_string($_SESSION['platba']) . '"';
            if ($result_payment = mysql_query($query_payment))
                $row_payment = mysql_fetch_object($result_payment);

            if ($this->_dph_price_visibility) {
                $output .= '
					<tr>
						<td colspan="9" class="price"><span>' . $cTranslator->getTranslation("Spôsob doručenia", 0) . ' <small>(' . $row_delivery->name . ')</small><strong>' . number_format($row_delivery->price_eur, 2, '.', ' ') . ' &euro;</strong></span></td>
					</tr>
					<tr>
						<td colspan="9" class="price"><span>' . $cTranslator->getTranslation("Spôsob platby", 0) . ' <small>(' . $row_payment->name . ')</small><strong>' . number_format($row_payment->price_eur, 2, '.', ' ') . ' &euro;</strong></span></td>
					</tr>
                                        ';
                if (User::hasRegistrationDiscount()) {
                    $output .= '
					<tr>
						<td colspan="9" class="price big"><span>' . $cTranslator->getTranslation("Celkom", 0) . ' <strong>' . number_format((($this->get_cart_value() * ((100 - REGISTRATION_DISCOUNT) / 100)) + $row_delivery->price_eur + $row_payment->price_eur), 2, '.', ' ') . ' &euro;</strong></span></td>
					</tr>
					';
                } else {
                    $output .= '
					<tr>
						<td colspan="9" class="price big"><span>' . $cTranslator->getTranslation("Celkom", 0) . ' <strong>' . number_format(($this->get_cart_value() + $row_delivery->price_eur + $row_payment->price_eur), 2, '.', ' ') . ' &euro;</strong></span></td>
					</tr>
					';
                }
            } else {
                $output .= '
					<tr>
						<td colspan="6" class="price"><span>' . $cTranslator->getTranslation("Spôsob doručenia", 0) . ' <small>(' . $row_delivery->name . ')</small><strong>' . number_format($row_delivery->price_eur, 2, '.', ' ') . ' &euro;</strong></span></td>
					</tr>
					<tr>
						<td colspan="6" class="price"><span>' . $cTranslator->getTranslation("Spôsob platby", 0) . ' <small>(' . $row_payment->name . ')</small><strong>' . number_format($row_payment->price_eur, 2, '.', ' ') . ' &euro;</strong></span></td>
					</tr>
                                        ';
                if (User::hasRegistrationDiscount()) {
                    $output .= '
					<tr>
						<td colspan="6" class="price big"><span>' . $cTranslator->getTranslation("Celkom", 0) . ' <strong>' . number_format((($this->get_cart_value() * ((100 - REGISTRATION_DISCOUNT) / 100)) + $row_delivery->price_eur + $row_payment->price_eur), 2, '.', ' ') . ' &euro;</strong></span></td>
					</tr>
					';
                } else {
                    $output .= '
					<tr>
						<td colspan="6" class="price big"><span>' . $cTranslator->getTranslation("Celkom", 0) . ' <strong>' . number_format(($this->get_cart_value() + $row_delivery->price_eur + $row_payment->price_eur), 2, '.', ' ') . ' &euro;</strong></span></td>
					</tr>
					';
                }
            }
        }


        $output .= '</tbody></table>';
        return $output;
    }

    public function show_cart_detail_board($slim = false) {
        global $cTranslator;
        global $navigateEnd;

        $obj_product = new Product();

        $output = '<div class="row justify-content-center cart-contents' . ($slim ? ' slim' : '') . '">';

        foreach ($this->items_array as $key => $item) {
        	$item_name = $obj_product->get_product_name($item['product_id']);
            $item_photo_src = $obj_product->get_product_photo($item['product_id']);

        	$output .= '<div class="' . ($slim ? 'col-6 col-md-3 col-xl-2' : 'col-12 col-md-6 col-xl-4 mt-2 mt-md-3') . ' mb-2 mb-md-3">';
        	$output .= '<div class="cart-item" data-pid="' . $item['product_id'] . '">';
        	/* $color_name = Product::get_color_name($item['color']);
            $size = $item['size']; */            

            if (!empty($item_photo_src) AND file_exists('photos/original/' . $item_photo_src)) {
                $item_photo = '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/produkt/' . String::SEOFriendlyText($item_name) . '/' . $item['product_id'] . '" class="image"><img src="photos/thumbnail/' . $item_photo_src . '" alt="' . $item_detail . '"/></a>';
            }

            $price_wo_vat = $item['price_item'];
            if ($this->_dph_price_visibility) {
            } else {
            }
            $output .= $item_photo;
            // info
            $output .= '<div class="info">';
            $output .= '<div>';
	            $output .= '<div class="name">' . $item_name . '</div>';
	            $output .= '<div class="price">' . number_format($price_wo_vat, 2, '.', ' ') . ' &euro;</div>';
            $output .= '</div>';

            $output .= '<div>';
            if($slim) {
            	$output .= '<div class="amount">x&nbsp;' . $item['amount'] . '</div>';
            }
            else {
	            $output .= '<div class="changer">';
		            $output .= '<a title="' . $cTranslator->getTranslation('Odobrať jeden kus produktu', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/decrease_item/' . $key . '" class="minus"></a>';
		            $output .= '<input type="text" data-key="' . $key . '" name="update_item" value="' . $item['amount'] . '">';
		            $output .= '<a title="' . $cTranslator->getTranslation('Pridať jeden kus produktu', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/increase_item/' . $key . '" class="plus"></a>';
	            $output .= '</div>';

	            $output .= '<a title="' . $cTranslator->getTranslation('Odobrať produkt', 0) . '" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/kosik/delete_item/' . $key . '" class="delete_item">' . $cTranslator->getTranslation('Odstrániť', 0) . '</a>';
	        }
	        $output .= '</div>';

            $output .= '</div>';

            $output .= '</div>';
            $output .= '</div>';
        }
        if($slim && $navigateEnd == 'step1') {
            $output .= '<div class="summary row justify-content-center">';
        	$output .= '<div class="col-12 col-md-7 col-xl-5">';
        	$output .= '<div class="subtotal d-flex justify-content-between align-items-end">
                    <div>' . $cTranslator->getTranslation('Medzisúčet', 0) . '</div>
                    <div><strong>' . number_format($this->get_cart_value(), 2, '.', ' ') . '&nbsp;&euro;</strong></div></div>';
            $output .= '</div>';
            $output .= '</div>';
        }
        
        unset($obj_product);

        
        $output .= '</div>';


        if ($navigateEnd == 'step2') {
            $query_delivery = 'SELECT * FROM ' . TABLE_PREFIX . 'delivery_type WHERE delivery_type_id = "' . mysql_real_escape_string($_SESSION['doprava']) . '"';
            if ($result_delivery = mysql_query($query_delivery))
                $row_delivery = mysql_fetch_object($result_delivery);

            $query_payment = 'SELECT * FROM ' . TABLE_PREFIX . 'payment_type WHERE payment_type_id = "' . mysql_real_escape_string($_SESSION['platba']) . '"';
            if ($result_payment = mysql_query($query_payment))
                $row_payment = mysql_fetch_object($result_payment);

            $output .= '<div class="summary row justify-content-center">';

        	$output .= '<div class="col-12 col-md-7 col-xl-5">';
        	$output .= '<div class="subtotal d-flex justify-content-between align-items-end">
                    <div>' . $cTranslator->getTranslation('Medzisúčet', 0) . '</div>
                    <div><strong>' . number_format($this->get_cart_value(), 2, '.', ' ') . '&nbsp;&euro;</strong></div></div>';
            //if (User::hasRegistrationDiscount()) {
            //    number_format((($this->get_cart_value() * ((100 - REGISTRATION_DISCOUNT) / 100)) + $row_delivery->price_eur + $row_payment->price_eur), 2, '.', ' ')
            //}
            
        	$output .= '<div class="delivery d-flex justify-content-between align-items-end">
                    <div>' . $cTranslator->getTranslation('Doručenie', 0) . '<br /><em>'.$row_delivery->name.'</em></div>
                    <div class="text-end">';
                    if($row_delivery->price_eur == 0) {
                    	$output .= '<small>' . $cTranslator->getTranslation('zdarma', 0) . '</small>';
                    }
                    else {
                    	$output .= '<strong>' . number_format($row_delivery->price_eur, 2, '.', ' ') . '&nbsp;&euro;</strong>';
                    }
                    $output .= '</div></div>';
            if (!empty($_SESSION['packeta_place'])) {
                $output .= '<div class="place">'.$_SESSION['packeta_place'].'<br>'.$_SESSION['packeta_street'].'<br>'.$_SESSION['packeta_zip'].' '.$_SESSION['packeta_city'].'</div>';
            }
            if (!empty($_SESSION['dpd_name'])) {
                $output .= '<div class="place">'.$_SESSION['dpd_name'].'<br>'.$_SESSION['dpd_address'].'</div>';
            }
        	$output .= '<div class="payment d-flex justify-content-between align-items-end">
                    <div>' . $cTranslator->getTranslation('Platba', 0) . '<br /><em>'.$row_payment->name.'</em></div>
                    <div class="text-end">';
                    if($row_payment->price_eur == 0) {
                    	$output .= '<small>' . $cTranslator->getTranslation('zdarma', 0) . '</small>';
                    }
                    else {
                    	$output .= '<strong>' . number_format($row_payment->price_eur, 2, '.', ' ') . '&nbsp;&euro;</strong>';
                    }
                    $output .= '</div></div>';
        	$output .= '<div class="total d-flex justify-content-between align-items-end">
                    <div>' . $cTranslator->getTranslation('Celkom', 0) . '</div>
                    <div class="text-end"><strong>' . number_format(($this->get_cart_value() + $row_delivery->price_eur + $row_payment->price_eur), 2, '.', ' ') . '&nbsp;&euro;</strong></div></div>';
            if ($this->_dph_price_visibility) {
        		$output .= '<div class="vat text-end">(' . $cTranslator->getTranslation('zahŕňa', 0) . ' ' . number_format($this->get_cart_value() - ($this->get_cart_value() / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro; ' . $cTranslator->getTranslation('DPH', 0) . ' '. (VAT_COEFFICIENT - 1) * 100 .'%)</div>';
        	}
            $output .= '</div>';
            $output .= '</div>';
        }

        return $output;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_delivery_types() {
        $cart_value = VAT_VISIBILITY == FALSE ? $this->get_cart_value() : ($this->get_cart_value() / VAT_COEFFICIENT);
        $query_delivery = 'SELECT * FROM ' . TABLE_PREFIX . 'delivery_type
                            WHERE 1 AND min_price<="' . $cart_value . '" AND max_price>="' . $cart_value . '"
                            ORDER BY name ASC';

        if ($result_delivery = mysql_query($query_delivery)) {
            $i = 0;
            while ($row_delivery = mysql_fetch_assoc($result_delivery)) {
                $query_payment = 'SELECT payment_type_id FROM ' . TABLE_PREFIX . 'delivery_payment_rel WHERE 1 AND delivery_type_id="' . $row_delivery['delivery_type_id'] . '"';
                if ($result_payment = mysql_query($query_payment)) {
                    while ($row_payment = mysql_fetch_assoc($result_payment)) {
                        $payment .= $row_payment['payment_type_id'] . ',';
                    }
                }
//array_push($row_delivery), $payment);
                $output[$i] = $row_delivery;
                $output[$i]['payment'] = rtrim($payment, ',');
                unset($payment);
                $i++;
            }
            return $output;
        } else
            return false;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_delivery_type($delivery_id) {
        if ($delivery_id == NULL)
            return false;

        $query = 'SELECT * FROM ' . TABLE_PREFIX . 'delivery_type
                  WHERE 1 AND delivery_type_id="' . $delivery_id . '"';

        if ($result = mysql_query($query)) {
            $row = mysql_fetch_object($result);
            $output = array(
                'name' => $row->name,
                'price' => $row->price_eur
            );

            return $output;
        } else {
            return false;
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function get_payment_types() {
        $query_payment = 'SELECT * FROM ' . TABLE_PREFIX . 'payment_type WHERE 1 ORDER BY name ASC';

        if ($result_payment = mysql_query($query_payment)) {
            while ($row_payment = mysql_fetch_object($result_payment))
                $output[] = $row_payment;

            return $output;
        } else
            return false;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function get_payment_type($payment_id) {
        if ($payment_id == NULL)
            return false;

        $query = 'SELECT name, price_eur FROM ' . TABLE_PREFIX . 'payment_type
                  WHERE 1 AND payment_type_id="' . $payment_id . '"';

        if ($result = mysql_query($query)) {
            $row = mysql_fetch_object($result);
            $output = array(
                'name' => $row->name,
                'price' => $row->price_eur
            );

            return $output;
        } else {
            return false;
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function set_dph_price_visibility($value) {
        if (is_bool($value)) {
            $this->_dph_price_visibility = $value;
        } else {
            $this->_dph_price_visibility = false;
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_dph_price_visibility() {
        return $this->_dph_price_visibility;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function generate_var_symbol($id) {
        return str_pad($id, 7, "0", STR_PAD_LEFT);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function submit_step1() {
        global $navigateArrayUrlWithoutBase;

        foreach ($_POST as $key => $value) {
            $_SESSION[$key] = $value;
        }

        header('Location: ' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '/step2');
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function submit_step2() {
        global $user;
        global $navigateArrayUrlWithoutBase;
        global $emailAddress;
        global $mail_addresses;
        global $fromAddress;
        global $fromName;
        global $lang;
        global $cTranslator;

        $continue = false;

        if (User::hasRegistrationDiscount()) {
            $this->cart_discount = REGISTRATION_DISCOUNT;
        } else {
            $this->cart_discount = 0;
        }
// SPRACOVANIE KONTAKTNYCH A ADRESNYCH UDAJOV ZAKAZNIKA
        if (!$user->isAuthenticated()) {
//	ULOZENIE NOVEHO ZAKAZNIKA
            $data_array = array(
                'username' => $_SESSION['mail'],
                'fullname' => $_SESSION['fname'] . ' ' . $_SESSION['lname'],
                'meno' => $_SESSION['fname'],
                'priezvisko' => $_SESSION['lname'],
                'ulica_cislo' => $_SESSION['address1'],
                'psc' => $_SESSION['psc1'],
                'mesto' => $_SESSION['city1'],
                'newsletter' => (($_SESSION['newsletter'] == 'on') ? '1' : '0')
            );

            foreach ($data_array as $df => $value) {
                $ins_cols[] = $df;
                $ins_vals[] = (($value != 'NULL') ? '"' . $value . '"' : $value);
            }

            $query_user = 'INSERT INTO ' . TABLE_PREFIX . 'user (' . implode(',', $ins_cols) . ') VALUES (' . implode(',', $ins_vals) . ')';
            unset($data_array, $ins_cols, $ins_vals);
            if (mysql_query($query_user)) {
                $continue = true;
                $new_user_id = mysql_insert_id();

                Cart::sendNewUserMail();
            } else {
                $continue = false;

                Message::setMessage('Zadaný e-mail je už spojený s iným účtom. Prosím zadajte iný.', 2);
                header('Location: ' . ROOTDIR);
                exit;
            }

// ULOZENIE NOVEJ ADDRESS BOOK
            if ($continue === true) {
                $data_array = array(
                    'user_id' => $new_user_id,
                    'city1' => $_SESSION['city1'],
                    'city2' => $_SESSION['city2'],
                    'fname' => $_SESSION['fname'],
                    'lname' => $_SESSION['lname'],
                    'address1' => $_SESSION['address1'],
                    'address2' => $_SESSION['address2'],
                    'state1' => $_SESSION['state1'],
                    'state2' => $_SESSION['state2'],
                    'psc1' => $_SESSION['psc1'],
                    'psc2' => $_SESSION['psc2'],
                    'phone' => $_SESSION['phone'],
                    'ico' => $_SESSION['ico'],
                    'dic' => $_SESSION['dic'],
                    'icdph' => $_SESSION['icdph'],
                    'cname' => $_SESSION['cname']
                );

                foreach ($data_array as $df => $value) {
                    $ins_cols[] = $df;
                    $ins_vals[] = (($value != 'NULL') ? '"' . $value . '"' : $value);
                }

                $query_book = 'INSERT INTO ' . TABLE_PREFIX . 'user_address_book (' . implode(',', $ins_cols) . ') VALUES (' . implode(',', $ins_vals) . ')';
                unset($data_array, $ins_cols, $ins_vals);

                if (mysql_query($query_book))
                    $continue = true;
                else
                    $continue = false;
            }
        } else {
//	UPDATE STAREHO ZAKAZNIKA
            $data_array = array(
                'mail' => $_SESSION['mail'],
                'fullname' => $_SESSION['fname'] . ' ' . $_SESSION['lname'],
                'meno' => $_SESSION['fname'],
                'priezvisko' => $_SESSION['lname'],
                'ulica_cislo' => $_SESSION['address1'],
                'psc' => $_SESSION['psc1'],
                'mesto' => $_SESSION['state1'],
                'registration_discount' => '0',
                'newsletter' => (($_SESSION['newsletter'] == 'on') ? '1' : '0'),
            );
            foreach ($data_array as $df => $value) {
                $ins_vals[] = $df . ' = "' . $value . '"';
            }

            $query_user = 'UPDATE ' . TABLE_PREFIX . 'user SET ' . implode(',', $ins_vals) . ' WHERE user_id="' . $_SESSION['user_id'] . '"';
            unset($data_array, $ins_vals);
//echo $query_user;
            if (mysql_query($query_user))
                $continue = true;

// UPDATE JEHO ADDRESS BOOK
            if ($continue === true) {
                $data_array = array(
                    'city1' => $_SESSION['city1'],
                    'city2' => $_SESSION['city2'],
                    'fname' => $_SESSION['fname'],
                    'lname' => $_SESSION['lname'],
                    'address1' => $_SESSION['address1'],
                    'address2' => $_SESSION['address2'],
                    'state1' => $_SESSION['state1'],
                    'state2' => $_SESSION['state2'],
                    'psc1' => $_SESSION['psc1'],
                    'psc2' => $_SESSION['psc2'],
                    'phone' => $_SESSION['phone'],
                    'ico' => $_SESSION['ico'],
                    'dic' => $_SESSION['dic'],
                    'icdph' => $_SESSION['icdph'],
                    'cname' => $_SESSION['cname']
                );
                foreach ($data_array as $df => $value) {
                    $ins_vals[] = $df . ' = "' . $value . '"';
                }

                $query_book = 'UPDATE ' . TABLE_PREFIX . 'user_address_book SET ' . implode(',', $ins_vals) . ' WHERE user_id="' . $_SESSION['user_id'] . '"';
                unset($data_array, $ins_vals);
//echo $query_book;
                if (mysql_query($query_book)) {
                    $select_book = mysql_query('SELECT COUNT(user_address_book_id) AS total FROM ' . TABLE_PREFIX . 'user_address_book WHERE user_id = ' . $_SESSION['user_id']);
                    $su = mysql_fetch_assoc($select_book);
// ZISTUJEME CI SME NIECO UPDATLI = ci nechyba uzivatelovi zaznam v user_address_book
                    if ($su['total'] < 1) {
                        $data_array = array(
                            'user_id' => $_SESSION['user_id'],
                            'city1' => $_SESSION['city1'],
                            'city2' => $_SESSION['city2'],
                            'fname' => $_SESSION['fname'],
                            'lname' => $_SESSION['lname'],
                            'address1' => $_SESSION['address1'],
                            'address2' => $_SESSION['address2'],
                            'state1' => $_SESSION['state1'],
                            'state2' => $_SESSION['state2'],
                            'psc1' => $_SESSION['psc1'],
                            'psc2' => $_SESSION['psc2'],
                            'phone' => $_SESSION['phone'],
                            'ico' => $_SESSION['ico'],
                            'dic' => $_SESSION['dic'],
                            'icdph' => $_SESSION['icdph'],
                            'cname' => $_SESSION['cname']
                        );

                        foreach ($data_array as $df => $value) {
                            $ins_cols[] = $df;
                            $ins_vals[] = (($value != 'NULL') ? '"' . $value . '"' : $value);
                        }

                        $query_book = 'INSERT INTO ' . TABLE_PREFIX . 'user_address_book (' . implode(',', $ins_cols) . ') VALUES (' . implode(',', $ins_vals) . ')';
                        unset($data_array, $ins_cols, $ins_vals);
//echo $query_book;
                        if (mysql_query($query_book))
                            $continue = true;
                        else
                            $continue = false;
                    } else
                        $continue = true;
                } else
                    $continue = false;
            }
        }

        if ($continue === true) {
            // ULOZENIE OBJEDNAVKY
            $data_array = array(
                'amount' => $this->get_cart_quantity(),
                'user_id' => ((isset($new_user_id)) ? $new_user_id : $_SESSION['user_id']),
                'to_name' => $_SESSION['fname'] . ' ' . $_SESSION['lname'],
                'delivery_address' => $_SESSION['address1'] . ';' . $_SESSION['city1'] . ';' . $_SESSION['psc1'] . ';' . $_SESSION['state1'],
                'invoice_address' => ((!empty($_SESSION['address2']) AND ! empty($_SESSION['city2']) AND ! empty($_SESSION['psc2']) AND ! empty($_SESSION['state2'])) ? $_SESSION['address2'] . ';' . $_SESSION['city2'] . ';' . $_SESSION['psc2'] . ';' . $_SESSION['state2'] : 'NULL'),
                'company_data' => ((!empty($_SESSION['cname']) OR ! empty($_SESSION['ico']) OR ! empty($_SESSION['dic'])) ? $_SESSION['cname'] . ';' . $_SESSION['ico'] . ';' . $_SESSION['dic'] . ';' . $_SESSION['icdph'] : 'NULL'),
                'delivery_type' => $_SESSION['doprava'],
                'payment_type' => $_SESSION['platba'],
                'comment' => $_SESSION['comment'],
                'order_state_id' => '1',
                'fullname' => $_SESSION['fname'] . ' ' . $_SESSION['lname'],
                'order_discount' => $this->get_cart_discount(),
                'price_total' => number_format($this->get_cart_value(), 2, '.', ''),
                'date_o' => date('Y-m-d H:i:s'),
                'packeta' => !empty($_SESSION['packeta_place']) ? $_SESSION['packeta_place'].';'.$_SESSION['packeta_street'].';'.$_SESSION['packeta_zip'].';'.$_SESSION['packeta_city'] : '',
                'dpd' => !empty($_SESSION['dpd_address']) ? $_SESSION['dpd_name'].';'.$_SESSION['dpd_address'] : '',
            );

            foreach ($data_array as $df => $value) {
                $ins_cols[] = $df;
                $ins_vals[] = (($value != 'NULL') ? '"' . $value . '"' : $value);
            }

            $query_order = 'INSERT INTO ' . TABLE_PREFIX . 'order (' . implode(',', $ins_cols) . ') VALUES (' . implode(',', $ins_vals) . ')';
            unset($data_array, $ins_cols, $ins_vals);

            if (mysql_query($query_order)) {
                $continue = true;
                $new_order_id = mysql_insert_id();

                $query_order_update = 'UPDATE ' . TABLE_PREFIX . 'order SET var_symbol = "' . $this->generate_var_symbol($new_order_id) . '" WHERE order_id = ' . $new_order_id;
                mysql_query($query_order_update);
            } else {
                Message::setMessage('Chyba r.2243: Chyba uloženia odjednávky!', 2);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        } else {
            Message::setMessage('Chyba r.2248: Chyba uloženia odjednávky!', 2);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        if ($continue === true) {
		  $products=array();
            $scr_produc='';
            foreach ($this->items_array as $item) {
                $color_name = Product::get_color_name($item['color']);
                $size = $item['size'];

                $product_name = Product::get_product_name($item['product_id']);
				$products[]=$item['product_id'];
                /*
                if ($color_name || $size != '0') {
                    $product_name .= ' <small>(';
                    if ($color_name) {
                        $product_name .= $cTranslator->getTranslation("Farba", 0) . ': ' . $color_name;
                    }
                    if ($size != '0') {
                        if ($color_name) {
                            $product_name .= ', ';
                        }
                        $product_name .= $cTranslator->getTranslation("Veľkosť", 0) . ': ' . Product::get_size_name($size);
                    }
                    $product_name .= ')</small>';
                }
                */
                $_price=(VAT_VISIBILITY ? number_format($item['price_item'], 2, '.', '') : number_format($item['price_item'], 2, '.', ''));
                $_amount=User::hasRegistrationDiscount() ? $item['amount'] * ((100 - REGISTRATION_DISCOUNT) / 100) : $item['amount'];
                $scr_produc.=" _hrq.push(['addProduct', '".$product_name."', '".$_price."', '".$_price."']);";

                $data_array = array(
                    'order_id' => $new_order_id,
                    'user_id' => ((isset($new_user_id)) ? $new_user_id : $_SESSION['user_id']),
                    'product_id' => $item['product_id'],
                    'product_name' => $product_name,
                    'amount' => $_amount,
                    'price' =>$_price ,
                    'color' => $item['color'],
                    'size' => $item['size'],
                );

                foreach ($data_array as $df => $value) {
                    $ins_cols[] = $df;
                    $ins_vals[] = (($value != 'NULL') ? '"' . $value . '"' : $value);
                }

                $query_order_product = 'INSERT INTO ' . TABLE_PREFIX . 'order_product (' . implode(',', $ins_cols) . ') VALUES (' . implode(',', $ins_vals) . ')';
                unset($data_array, $ins_cols, $ins_vals);

                if (mysql_query($query_order_product)) {
                    $update_product_type = 'UPDATE ' . TABLE_PREFIX . 'product_type SET pocet=(pocet - ' . $item['amount'] . ') WHERE 1 AND product_type_id="' . $item['size'] . '" AND product_id="' . $item['product_id'] . '";';
                    mysql_query($update_product_type);
                    $continue = true;
                } else {
                    $continue = false;
                }
            }
			if(!empty($products))
               self::heurekaOverenie($products,$new_order_id);
        }
        echo getContentByLabel('Číslo objednávky: ') . '#' . str_pad($new_order_id, 8, '0', STR_PAD_LEFT);
        if ($continue === true) {
// GENERUJ A VLOZ PREVIEW
            $order_preview = '<div>' . getContentByLabel('podakovanie za objednavku') . '</div>';
//            $order_preview.= "            <script type=\"text/javascript\">
//                var _hrq = _hrq || [];
//                _hrq.push(['setKey', 'DCD2E704DDA2B5934D553B2E3DDDBAF1']);
//                _hrq.push(['setOrderId', '".$new_order_id."']);
//                 ".$scr_produc."
//
//                _hrq.push(['trackOrder']);
//
//                (function() {
//                    var ho = document.createElement('script'); ho.type = 'text/javascript'; ho.async = true;
//                    ho.src = 'https://im9.cz/sk/js/ext/2-roi-async.js';
//                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ho, s);
//                })();
//            </script>";
            $order_preview .= Cart::generate_order_preview($new_order_id, $this->_dph_price_visibility);
            $query_order_update = 'UPDATE ' . TABLE_PREFIX . 'order SET order_vs = "' . $new_order_id . '", order_preview = "' . addslashes($order_preview) . '" WHERE order_id = ' . $new_order_id;
            mysql_query($query_order_update);

//            echo '
//                    <!-- Google Code for ireseller prehlad konverzii Conversion Page -->
//                    <script type="text/javascript">
//                        /* <![CDATA[ */
//                        var google_conversion_id = 862599488;
//                        var google_conversion_language = "en";
//                        var google_conversion_format = "3";
//                        var google_conversion_color = "ffffff";
//                        var google_conversion_label = "P9pOCI2kxG0QwPKomwM";
//                        var google_remarketing_only = false;
//                        /* ]]> */
//                    </script>
//                    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
//                    </script>
//                    <noscript>
//                        <div style="display:inline;">
//                            <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/862599488/?label=P9pOCI2kxG0QwPKomwM&amp;guid=ON&amp;script=0" />
//                        </div>
//                    </noscript>
//            	';

            echo "<!-- Event snippet for Zobrazenie stránky conversion page -->";
            echo "<script data-cookiecategory=\"analytics\"> gtag('event', 'conversion', { 'send_to': 'AW-16661642529/i8FRCMir3NMZEKH68Yg-', 'value': ".$this->get_cart_value().", 'currency': 'EUR' }); </script>";

            // BEGIN GCR Opt-in Module Code
            echo "<!-- BEGIN GCR Opt-in Module Code -->";
            echo "<script src=\"https://apis.google.com/js/platform.js?onload=renderOptIn\" async defer></script>";
            echo "<script>window.renderOptIn = function() {window.gapi.load('surveyoptin', function() {window.gapi.surveyoptin.render({".
                                    // REQUIRED
                                    "'merchant_id': '" . MERCHANT_ID . "',".
                                    "'order_id': '" . $new_order_id . "',".
                                    "'email': '" . $_SESSION['mail'] . "',".
                                    "'delivery_country': 'SK',".
                                    "'estimated_delivery_date': '" . Date('Y-m-d', strtotime('+3 days')) . "',".
                                    // OPTIONAL
                                    //"'products':[{'gtin':'GTIN1'}, {'gtin':'GTIN2'>}],".
                                    "'opt_in_style': 'BOTTOM_RIGHT_DIALOG'".
                                "});});}</script>";
            echo "<!-- END GCR Opt-in Module Code -->";
            // END GCR Opt-in Module Code

            //Cart::generate_invoice_pdf($new_order_id);

			// POSLI PREVIEW NA MAIL
            if(PUBLIC_RELEASE)
            	Cart::sendNewOrderMail($order_preview);

            unset($_SESSION['serialized_cart']);
//header('Location: '.ROOTDIR.'/'.Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY).'/'.$navigateArrayUrlWithoutBase[0].'/step2');
        }
    }
	public static function heurekaOverenie($products,$order){
        $items='&';
        foreach($products as $product){
            $items.='itemId[]='.$product.'&';
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.heureka.sk/direct/dotaznik/objednavka.php?id=".HEUREKA_API_KEY."&email=".$_SESSION['mail'].$items."orderid=".$order);


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        $output = curl_exec($ch);


        curl_close($ch);
        return $output;
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function sendNewOrderMail($order_preview) {
        global $user;
        global $navigateArrayUrlWithoutBase;
        global $emailAddress;
        global $mail_addresses;
        global $fromAddress;
        global $fromName;
        global $lang;

        require_once("shared/classes/class.mail.php");

        $email_message = new email_message_class;
        $email_message->SetEncodedEmailHeader("From", $fromAddress, $fromName);
        $email_message->SetEncodedEmailHeader("Reply-To", $fromAddress, $fromName);
        $email_message->SetHeader("Sender", $fromAddress);
        $email_message->SetHeader("Subject", $fromName . " - objednavka");
        $email_message->AddHTMLPart($order_preview, "utf-8");
//	email administratorovi

        foreach ($emailAddress as $k => $v) {
            $email_message->SetEncodedEmailHeader("To", $v, "");
            $email_message->Send();
        }
// email klientovi
//echo $_SESSION['mail'];
        $email_message->SetEncodedEmailHeader("To", $_SESSION['mail'], $_SESSION['lname'] . " " . $_SESSION['fname']);
        $email_message->Send();
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function sendNewUserMail() {
        global $user;
        global $navigateArrayUrlWithoutBase;
        global $emailAddress;
        global $mail_addresses;
        global $fromAddress;
        global $fromName;
        global $lang;

        if (isset($_POST['pwd'])) {

            require_once("shared/classes/class.mail.php");

            $email_message = new email_message_class;
            $email_message->SetEncodedEmailHeader("From", $fromAddress, $fromName);
            $email_message->SetEncodedEmailHeader("Reply-To", $fromAddress, $fromName);
            $email_message->SetHeader("Sender", $fromAddress);
            $email_message->SetHeader("Subject", $fromName . " - registracia noveho uzivatela");
            $email_message->AddHTMLPart("Gratulujeme a ďakujeme za zaregistrovanie sa na " . PROJECT_NAME . ".<br /><br />
		                             Vasa prihlasovacia adresa: " . $_POST['mail'] . "<br />
		                             Vase heslo: " . $_POST['pwd'] . "", "utf-8");
            /*
              $email_message->AddPlainTextPart("Gratulujeme a ďakujeme za zaregistrovanie sa na " . PROJECT_NAME . ".\r\n\r\n
              Vasa prihlasovacia adresa: " . $_POST['mail'] . "\r\n
              Vase heslo: " . $_POST['pwd'] . "", "utf-8");
             *
             */
//	email administratorovi
            foreach ($emailAddress as $k => $v) {
                $email_message->SetEncodedEmailHeader("To", $v, "");
                $email_message->Send();
            }
// email klientovi
            $email_message->SetEncodedEmailHeader("To", $_POST['mail'], $_POST['lname'] . " " . $_POST['fname']);
            $email_message->Send();
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function generate_order_preview($order_id, $dph_price_visibility) {
        global $cTranslator;

        $query = 'SELECT * FROM ' . TABLE_PREFIX . 'order WHERE 1 AND order_id="' . mysql_real_escape_string($order_id) . '"';
        $result = @mysql_query($query);
        if ($result) {
            $row = @mysql_fetch_object($result);
        } else {
            print mysql_error();
        }

        mysql_free_result($result);
        $delivery = Cart::get_delivery_type($row->delivery_type);
        $payment = Cart::get_payment_type($row->payment_type);
        $reference = str_pad($row->order_id, 8, '0', STR_PAD_LEFT);

        $output = '
        <table border="0" cellspacing="0" cellpadding="0" style="font-family: Arial, Helvetica, sans-serif;width: 980px;font-size: 13px;margin: 0 auto;">
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="7" align="right" style="padding: 4px 8px;">Objednávka číslo: #' . $reference . '</td>
            </tr>
            <tr>
                <td class="black" colspan="3" style="padding: 4px 8px;background-color: #ccc;"><strong>Dodávateľ</strong></td>
                <td class="black" colspan="4" style="padding: 4px 8px;background-color: #ccc;"><strong>Odberateľ</strong></td>
            </tr>
            <tr>
                <td class="extra-padding" colspan="3" rowspan="5" valign="top" style="padding: 10px 8px;">
                    ' . getContentByLabel('Údaje o dodávateľovi do faktúry', 0) . '
                </td>
                <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;"><strong>' . $row->to_name . '</strong></td>
            </tr>
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Fakturačná adresa: </strong></td>
            </tr>
            <tr>
                <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">' . $row->to_name . '<br />
                    ';
        $delivery_address = explode(';', $row->delivery_address);
        foreach ($delivery_address as $value) {
            $output .= $value . '<br />';
        }
        if (!empty($row->company_data)) {
        	$output .= '<br />';
            $company_data = explode(';', $row->company_data);
            $company_label = ['', 'IČO: ', 'DIČ: '];
            foreach ($company_data as $key=>$value) {
            	if($key == 0)
                	$output .= $company_label[$key] . '<strong>' . $value . '</strong><br />';
                else
                	$output .= $company_label[$key] . $value . '<br />';
            }
        }
        $output .= '<br />' . $_SESSION['mail'] . '<br />' . $_SESSION['phone'] . '<br />
                </td>
            </tr>
            ';
        if (!empty($row->invoice_address)) {
            $output .= '
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Adresa doručenia: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ';
            $invoice_address = explode(';', $row->invoice_address);
            foreach ($invoice_address as $value) {
                $output .= $value . '<br />';
            }

            $output .= '
                    </td>
                </tr>
                ';
        }
        if (!empty($row->packeta)) {
            $output .= '
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Packeta: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ';
            $packeta = explode(';', $row->packeta);
            foreach ($packeta as $value) {
                $output .= $value . '<br />';
            }

            $output .= '
                    </td>
                </tr>
                ';
        }
        if (!empty($row->dpd)) {
            $output .= '
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>DPD: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ';
            $dpd = explode(';', $row->dpd);
            foreach ($dpd as $value) {
                $output .= $value . '<br />';
            }

            $output .= '
                    </td>
                </tr>
                ';
        }
        $output .= '
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td class="black" colspan="7" style="padding: 4px 8px;background-color: #ccc;"><strong>Objednaný tovar</strong></td>
            </tr>
            ';
        if (VAT_VISIBILITY === TRUE) {
            $output .= '
            <tr class="smaller-font black" style="font-size: 11px;padding: 4px 8px;background-color: #ccc;">
                <td width="2%" style="padding: 4px 8px;">Množstvo</td>
                <td width="10%" style="padding: 4px 8px;">Kód tovaru</td>
                <td style="padding: 4px 8px;">Názov tovaru</td>
                <td width="10%" style="padding: 4px 8px;">Cena za kus bez DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena za kus s DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena spolu bez DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena spolu s DPH</td>
            </tr>
            ';
        } else {
            $output .= '
            <tr class="smaller-font black" style="font-size: 11px;padding: 4px 8px;background-color: #ccc;">
                <td width="2%" style="padding: 4px 8px;">Množstvo</td>
                <td width="10%" style="padding: 4px 8px;">Kód tovaru</td>
                <td style="padding: 4px 8px;">Názov tovaru</td>
                <td width="20%" colspan="2" style="padding: 4px 8px; text-align: right;">Cena za kus</td>
                <td width="20%" colspan="2" style="padding: 4px 8px; text-align: right;">Cena spolu</td>
            </tr>
            ';
        }
        $query1 = 'SELECT o.order_product_id, o.order_id, o.user_id, o.product_id, o.product_name, o.amount, o.price, o.color, o.size, p.code_1 AS ean FROM ' . TABLE_PREFIX . 'order_product AS o
                    LEFT JOIN ' . TABLE_PREFIX . 'product AS p USING(product_id)
                    WHERE 1 AND order_id = "' . mysql_real_escape_string($order_id) . '"';
        $result1 = mysql_query($query1);
        $i = 0;
        while ($row1 = mysql_fetch_object($result1)) {
            $i++;
            if (VAT_VISIBILITY === TRUE) {
                $output .= '
            <tr' . (($i % 2 == 0) ? ' class = "grey" style = "background: #EAEAEA;"' : '') . '>
                <td style="padding: 4px 0;text-align:center;">' . $row1->amount . 'x</td>
                <td style="padding: 4px 8px;font-size:10px;">' . $row1->ean . '</td>
                <td style="padding: 4px 8px;">' . $row1->product_name . '</td>
                <td style="padding: 4px 8px;">' . number_format(($row1->price / VAT_COEFFICIENT), 2, '.', ' ') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format($row1->price, 2, '.', '') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format((($row1->price / VAT_COEFFICIENT) * $row1->amount), 2, '.', ' ') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format(($row1->price * $row1->amount), 2, '.', '') . ' €</td>
            </tr>
            ';
            } else {
                $output .= '
            <tr' . (($i % 2 == 0) ? ' class = "grey" style = "background: #EAEAEA;"' : '') . '>
                <td style="padding: 4px 0;text-align:center;">' . $row1->amount . 'x</td>
                <td style="padding: 4px 8px;font-size:10px;">' . $row1->ean . '</td>
                <td style="padding: 4px 8px;">' . $row1->product_name . '</td>
                <td colspan="2" style="padding: 4px 8px; text-align: right;">' . number_format($row1->price, 2, ',', '&nbsp;') . '&nbsp;&euro;</td>
                <td colspan="2" style="padding: 4px 8px; text-align: right;">' . number_format(($row1->price * $row1->amount), 2, ',', ' ') . '&nbsp;&euro;</td>
            </tr>
            ';
            }
        }
        $output .= '
            <tr class="black" style="background-color: #ccc;">
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celková cena nákupu</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . number_format($row->price_total, 2, ',', '&nbsp;') . '&nbsp;&euro;</strong></td>
            </tr>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;">Spôsob doručenia <small>(' . $delivery['name'] . ')</small></td>
                <td colspan="1" align="right" style="padding: 4px 8px;">' . $delivery['price'] . '&nbsp;&euro;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;">Spôsob platby <small>(' . $payment['name'] . ')</small></td>
                <td colspan="1" align="right" style="padding: 4px 8px;">' . $payment['price'] . '&nbsp;&euro;</td>
            </tr>
            ';
        if (VAT_VISIBILITY === TRUE) {
            $output .= '
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celková cena nákupu s DPH</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . number_format($row->price_total, 2, '.', '') . '&nbsp;&euro;</strong></td>
            </tr>
            ';
            if ($row->order_discount != '0') {
                $output .= '
                <tr style="background: #EAEAEA;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Zľava za registráciu</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . REGISTRATION_DISCOUNT . '%</strong></td>
                </tr>
                ';
            }
            $output .= '
            <tr>
                 <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr style="font-size: 19px;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celkom</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>'; // . number_format((($row->price_total * VAT_COEFFICIENT) + $delivery['price'] + $payment['price']), 2, '.', '') .
            if ($row->order_discount != '0') {
                $output .= number_format(((($row->price_total / VAT_COEFFICIENT) * ((100 - REGISTRATION_DISCOUNT) / 100)) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
            } else {
                $output .= number_format((($row->price_total / VAT_COEFFICIENT) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
            }
            $output .= '&nbsp;&euro;</strong></td>
            </tr>
            ';
        } else {
            if ($row->order_discount != '0') {
                $output .= '
                <tr style="background: #EAEAEA;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Zľava za registráciu</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . REGISTRATION_DISCOUNT . '%</strong></td>
                </tr>
                ';
            }
            $output .= '
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr style="font-size: 19px;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celkom</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>'; //. number_format(($row->price_total + $delivery['price'] + $payment['price']), 2, '.', '') .
            if ($row->order_discount != '0') {
            	$total_price = ($row->price_total * ((100 - REGISTRATION_DISCOUNT) / 100)) + $delivery['price'] + $payment['price'];
                $output .= number_format($total_price, 2, ',', '&nbsp;');
            } else {
            	$total_price = $row->price_total + $delivery['price'] + $payment['price'];
                $output .= number_format($total_price, 2, ',', '&nbsp;');
            }
            $output .='&nbsp;&euro;</strong></td>
            </tr>
                ';
        }
        if($row->comment) {
        	$output .= '
	            <tr>
	                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
	            </tr>
	            <tr>
	                <td colspan="7" style="padding: 4px 8px;"><strong>poznámka: </strong><p>' . $row->comment . '</p></td>
	            </tr>
	            ';
        }
        $output .= '
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
        </table>
            ';

        $_SESSION['order']['order_id'] = $order_id;
        $_SESSION['order']['total_price'] = $total_price;
        $_SESSION['order']['reference'] = $reference;

        header('Content-Type: text/html; charset=utf-8');
        return $output;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function generate_invoice($order_id, $dph_price_visibility = TRUE) {
        global $cTranslator;

        $query = 'SELECT * FROM ' . TABLE_PREFIX . 'order WHERE 1 AND order_id="' . mysql_real_escape_string($order_id) . '"';
        $result = @mysql_query($query);
        if ($result) {
            $row = @mysql_fetch_object($result);
        } else {
            print mysql_error();
        }

        mysql_free_result($result);
        $delivery = Cart::get_delivery_type($row->delivery_type);
        $payment = Cart::get_payment_type($row->payment_type);

        $output = '
        <table border="0" cellspacing="0" cellpadding="0" style="font-family: Arial, Helvetica, sans-serif;width: 980px;font-size: 13px;margin: 0 auto;">
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="3" align="left" style="padding: 4px 8px;">' . $cTranslator->getTranslation('Daňový doklad', 0) . '</td>
                <td colspan="4" align="right" style="padding: 4px 8px;">Faktúra číslo: ' . str_pad(nextInvoiceID(), 6, '0', STR_PAD_LEFT) . '</td>
            </tr>
            <tr>
                <td class="black" colspan="3" style="padding: 4px 8px;background-color: #ccc;"><strong>Dodávateľ</strong></td>
                <td class="black" colspan="4" style="padding: 4px 8px;background-color: #ccc;"><strong>Odberateľ</strong></td>
            </tr>
            <tr>
                <td class="extra-padding" colspan="3" rowspan="5" valign="top" style="padding: 10px 8px;">
                    ' . getContentByLabel('Údaje o dodávateľovi do faktúry', 0) . '
                </td>
                <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;"><strong>' . $row->to_name . '</strong></td>
            </tr>
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Fakturačné údaje: </strong></td>
            </tr>
            <tr>
                <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                    ';
			        $delivery_address = explode(';', $row->delivery_address);
			        foreach ($delivery_address as $value) {
			            $output .= $value . '<br />';
			        }
			        if (!empty($row->company_data)) {
                        echo '<br />';
                        $company_data = explode(';', $row->company_data);
                        $company_label = ['', 'IČO: ', 'DIČ: ', 'IČ DPH: '];
                        foreach ($company_data as $key=>$value) {
                            if($key == 0)
                                $output .= $company_label[$key] . '<br /><strong>' . $value . '</strong><br /><br />';
                            else
                                $output .= $company_label[$key] . $value . '<br />';
                        }
                    }
			        $output .= '<br />' . User::returnUserLogin($row->user_id) . '<br />' . User::returnUserPhone($row->user_id) . '<br />
                </td>
            </tr>
            ';
        if (!empty($row->invoice_address) OR !empty($row->company_data)) {
            $output .= '
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Adresa doručenia: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ';
            $invoice_address = explode(';', $row->invoice_address);
            foreach ($invoice_address as $value) {
                $output .= $value . '<br />';
            }
            $output .= '<br />';

	        $output .= '</td>
	            </tr>
	            ';
        }

        if (!empty($row->packeta)) {
            $output .= '
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Packeta: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ';
            $packeta = explode(';', $row->packeta);
            foreach ($packeta as $value) {
                $output .= $value . '<br />';
            }
            $output .= '<br />';

	        $output .= '</td>
	            </tr>
	            ';
        }

        if (!empty($row->dpd)) {
            $output .= '
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>DPD: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ';
            $dpd = explode(';', $row->dpd);
            foreach ($dpd as $value) {
                $output .= $value . '<br />';
            }
            $output .= '<br />';

	        $output .= '</td>
	            </tr>
	            ';
        }
        $output .= '
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td class="black" colspan="7" style="padding: 4px 8px;background-color: #ccc;"><strong>Objednaný tovar</strong> <em>(Objednávka číslo: #' . str_pad($row->order_id, 8, '0', STR_PAD_LEFT) . ')</em></td>
            </tr>
            ';
        if ($dph_price_visibility === TRUE) {
            $output .= '
            <tr class="smaller-font black" style="font-size: 11px;padding: 4px 8px;background-color: #ccc;">
                <td width="2%" style="padding: 4px 8px;">Množstvo</td>
                <td width="10%" style="padding: 4px 8px;">Kód tovaru</td>
                <td style="padding: 4px 8px;">Názov tovaru</td>
                <td width="10%" style="padding: 4px 8px;">Cena za kus bez DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena za kus s DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena spolu bez DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena spolu s DPH</td>
            </tr>
            ';
        } else {
            $output .= '
            <tr class="smaller-font black" style="font-size: 11px;padding: 4px 8px;background-color: #ccc;">
                <td width="2%" style="padding: 4px 8px;">Množstvo</td>
                <td width="10%" style="padding: 4px 8px;">Kód tovaru</td>
                <td style="padding: 4px 8px;">Názov tovaru</td>
                <td width="20%" colspan="2" style="padding: 4px 8px; text-align: right;">Cena za kus</td>
                <td width="20%" colspan="2" style="padding: 4px 8px; text-align: right;">Cena spolu</td>
            </tr>
            ';
        }
        $query1 = 'SELECT o.order_product_id, o.order_id, o.user_id, o.product_id, o.product_name, o.amount, o.price, o.color, o.size, p.code_1 AS ean FROM ' . TABLE_PREFIX . 'order_product AS o
                    LEFT JOIN ' . TABLE_PREFIX . 'product AS p USING(product_id)
                    WHERE 1 AND order_id = "' . mysql_real_escape_string($order_id) . '"';
        $result1 = mysql_query($query1);
        $i = 0;
        while ($row1 = mysql_fetch_object($result1)) {
            $i++;
            if ($dph_price_visibility === TRUE) {
                $output .= '
            <tr' . (($i % 2 == 0) ? ' class = "grey" style = "background: #EAEAEA;"' : '') . '>
                <td style="padding: 4px 0;text-align:center;">' . $row1->amount . 'x</td>
                <td style="padding: 4px 8px;font-size:10px;">' . $row1->ean . '</td>
                <td style="padding: 4px 8px;">' . $row1->product_name . '</td>
                <td style="padding: 4px 8px;">' . number_format(($row1->price / VAT_COEFFICIENT), 2, '.', ' ') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format($row1->price, 2, '.', '') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format((($row1->price / VAT_COEFFICIENT) * $row1->amount), 2, '.', ' ') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format(($row1->price * $row1->amount), 2, '.', '') . ' €</td>
            </tr>
            ';
            } else {
                $output .= '
            <tr' . (($i % 2 == 0) ? ' class = "grey" style = "background: #EAEAEA;"' : '') . '>
                <td style="padding: 4px 0;text-align:center;">' . $row1->amount . 'x</td>
                <td style="padding: 4px 8px;font-size:10px;">' . $row1->ean . '</td>
                <td style="padding: 4px 8px;">' . $row1->product_name . '</td>
                <td colspan="2" style="padding: 4px 8px; text-align: right;">' . number_format($row1->price, 2, ',', '&nbsp;') . '&nbsp;&euro;</td>
                <td colspan="2" style="padding: 4px 8px; text-align: right;">' . number_format(($row1->price * $row1->amount), 2, ',', ' ') . '&nbsp;&euro;</td>
            </tr>
            ';
            }
        }
        $output .= '
            <tr class="black" style="background-color: #ccc;">
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celková cena nákupu</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . number_format($row->price_total, 2, ',', '&nbsp;') . '&nbsp;&euro;</strong></td>
            </tr>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;">Spôsob doručenia <small>(' . $delivery['name'] . ')</small></td>
                <td colspan="1" align="right" style="padding: 4px 8px;">' . $delivery['price'] . '&nbsp;&euro;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;">Spôsob platby <small>(' . $payment['name'] . ')</small></td>
                <td colspan="1" align="right" style="padding: 4px 8px;">' . $payment['price'] . '&nbsp;&euro;</td>
            </tr>
            ';
        if ($dph_price_visibility === TRUE) {
            $output .= '
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celková cena nákupu s DPH</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . number_format($row->price_total, 2, '.', '') . '&nbsp;&euro;</strong></td>
            </tr>
            ';
            if ($row->order_discount != '0') {
                $output .= '
                <tr style="background: #EAEAEA;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Zľava za registráciu</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . REGISTRATION_DISCOUNT . '%</strong></td>
                </tr>
                ';
            }
            $output .= '
            <tr>
                 <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr style="font-size: 19px;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celkom</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>'; // . number_format((($row->price_total * VAT_COEFFICIENT) + $delivery['price'] + $payment['price']), 2, '.', '') .
            if ($row->order_discount != '0') {
                $output .= number_format(((($row->price_total / VAT_COEFFICIENT) * ((100 - REGISTRATION_DISCOUNT) / 100)) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
            } else {
                $output .= number_format((($row->price_total / VAT_COEFFICIENT) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
            }
            $output .= '&nbsp;&euro;</strong></td>
            </tr>
            ';
        } else {
            if ($row->order_discount != '0') {
                $output .= '
                <tr style="background: #EAEAEA;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Zľava za registráciu</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . REGISTRATION_DISCOUNT . '%</strong></td>
                </tr>
                ';
            }
            $output .= '
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr style="font-size: 19px;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celkom</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>'; //. number_format(($row->price_total + $delivery['price'] + $payment['price']), 2, '.', '') .
            if ($row->order_discount != '0') {
                $output .= number_format((($row->price_total * ((100 - REGISTRATION_DISCOUNT) / 100)) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
            } else {
                $output .= number_format(($row->price_total + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
            }
            $output .='&nbsp;&euro;</strong></td>
            </tr>
                ';
        }
        $output .= '
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
        </table>
            ';
        header('Content-Type: text/html; charset=utf-8');
        return $output;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static function generate_invoice_pdf($order_id, $dph_price_visibility = TRUE) {
        global $cTranslator;

        include('shared/mpdf57/mpdf.php');

        $query = 'SELECT * FROM ' . TABLE_PREFIX . 'order WHERE 1 AND order_id="' . mysql_real_escape_string($order_id) . '"';
        $result = @mysql_query($query);
        if ($result) {
            $row = @mysql_fetch_object($result);
        } else {
            print mysql_error();
        }

        mysql_free_result($result);
        $delivery = Cart::get_delivery_type($row->delivery_type);
        $payment = Cart::get_payment_type($row->payment_type);

        $mpdf = new mPDF();

        $mpdf->WriteHTML('<h1>' . $row->order_id . '</h1>');

        $mpdf->WriteHTML('
        <table border="0" cellspacing="0" cellpadding="0" style="font-family: Arial, Helvetica, sans-serif;width: 980px;font-size: 13px;margin: 0 auto;">
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="7" align="right" style="padding: 4px 8px;">Objednávka číslo: #' . str_pad($row->order_id, 8, '0', STR_PAD_LEFT) . '</td>
            </tr>
            <tr>
                <td class="black" colspan="3" style="padding: 4px 8px;background-color: #ccc;"><strong>Dodávateľ</strong></td>
                <td class="black" colspan="4" style="padding: 4px 8px;background-color: #ccc;"><strong>Odberateľ</strong></td>
            </tr>
            <tr>
                <td class="extra-padding" colspan="3" rowspan="5" valign="top" style="padding: 10px 8px;">
                    ' . getContentByLabel('Údaje o dodávateľovi do faktúry', 0) . '
                </td>
                <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;"><strong>' . $row->to_name . '</strong></td>
            </tr>
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Adresa doručenia: </strong></td>
            </tr>
            <tr>
                <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                    ');
			        $delivery_address = explode(';', $row->delivery_address);
			        foreach ($delivery_address as $value) {
			            $mpdf->WriteHTML($value . '<br />');
			        }
			        $mpdf->WriteHTML('<br />' . $_SESSION['mail'] . '<br />' . $_SESSION['phone'] . '<br />
                </td>
            </tr>
            ');
        if (!empty($row->invoice_address) OR !empty($row->company_data)) {
            $mpdf->WriteHTML('
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Fakturačná adresa: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ');
            $invoice_address = explode(';', $row->invoice_address);
            foreach ($invoice_address as $value) {
                $mpdf->WriteHTML($value . '<br />');
            }
            $mpdf->WriteHTML('<br />');
            $company_data = explode(';', $row->company_data);
            foreach ($company_data as $value) {
                $mpdf->WriteHTML($value . '<br />');
            }
	        $mpdf->WriteHTML('</td>
	            </tr>
	            ');
        }
        if (!empty($row->packeta)) {
            $mpdf->WriteHTML('
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Packeta: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ');
            $packeta = explode(';', $row->packeta);
            foreach ($packeta as $value) {
                $mpdf->WriteHTML($value . '<br />');
            }
	        $mpdf->WriteHTML('</td>
	            </tr>
	            ');
        }

        if (!empty($row->dpd)) {
            $mpdf->WriteHTML('
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>DPD: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        ');
            $dpd = explode(';', $row->dpd);
            foreach ($dpd as $value) {
                $mpdf->WriteHTML($value . '<br />');
            }
	        $mpdf->WriteHTML('</td>
	            </tr>
	            ');
        }
        $mpdf->WriteHTML('
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td class="black" colspan="7" style="padding: 4px 8px;background-color: #ccc;"><strong>Objednaný tovar</strong></td>
            </tr>
            ');
        if (VAT_VISIBILITY === TRUE) {
            $mpdf->WriteHTML('
            <tr class="smaller-font black" style="font-size: 11px;padding: 4px 8px;background-color: #ccc;">
                <td width="2%" style="padding: 4px 8px;">Množstvo</td>
                <td width="10%" style="padding: 4px 8px;">Kód tovaru</td>
                <td style="padding: 4px 8px;">Názov tovaru</td>
                <td width="10%" style="padding: 4px 8px;">Cena za kus bez DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena za kus s DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena spolu bez DPH</td>
                <td width="10%" style="padding: 4px 8px;">Cena spolu s DPH</td>
            </tr>
            ');
        } else {
            $mpdf->WriteHTML('
            <tr class="smaller-font black" style="font-size: 11px;padding: 4px 8px;background-color: #ccc;">
                <td width="2%" style="padding: 4px 8px;">Množstvo</td>
                <td width="10%" style="padding: 4px 8px;">Kód tovaru</td>
                <td style="padding: 4px 8px;">Názov tovaru</td>
                <td width="20%" colspan="2" style="padding: 4px 8px; text-align: right;">Cena za kus</td>
                <td width="20%" colspan="2" style="padding: 4px 8px; text-align: right;">Cena spolu</td>
            </tr>
            ');
        }
        $query1 = 'SELECT o.order_product_id, o.order_id, o.user_id, o.product_id, o.product_name, o.amount, o.price, o.color, o.size, p.code_1 AS ean FROM ' . TABLE_PREFIX . 'order_product AS o
                    LEFT JOIN ' . TABLE_PREFIX . 'product AS p USING(product_id)
                    WHERE 1 AND order_id = "' . mysql_real_escape_string($order_id) . '"';
        $result1 = mysql_query($query1);
        $i = 0;
        while ($row1 = mysql_fetch_object($result1)) {
            $i++;
            if (VAT_VISIBILITY === TRUE) {
                $mpdf->WriteHTML('
            <tr' . (($i % 2 == 0) ? ' class = "grey" style = "background: #EAEAEA;"' : '') . '>
                <td style="padding: 4px 0;text-align:center;">' . $row1->amount . 'x</td>
                <td style="padding: 4px 8px;font-size:10px;">' . $row1->ean . '</td>
                <td style="padding: 4px 8px;">' . $row1->product_name . '</td>
                <td style="padding: 4px 8px;">' . number_format(($row1->price / VAT_COEFFICIENT), 2, '.', ' ') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format($row1->price, 2, '.', '') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format((($row1->price / VAT_COEFFICIENT) * $row1->amount), 2, '.', ' ') . ' €</td>
                <td style="padding: 4px 8px;">' . number_format(($row1->price * $row1->amount), 2, '.', '') . ' €</td>
            </tr>
            ');
            } else {
                $mpdf->WriteHTML('
            <tr' . (($i % 2 == 0) ? ' class = "grey" style = "background: #EAEAEA;"' : '') . '>
                <td style="padding: 4px 0;text-align:center;">' . $row1->amount . 'x</td>
                <td style="padding: 4px 8px;font-size:10px;">' . $row1->ean . '</td>
                <td style="padding: 4px 8px;">' . $row1->product_name . '</td>
                <td colspan="2" style="padding: 4px 8px; text-align: right;">' . number_format($row1->price, 2, ',', '&nbsp;') . '&nbsp;&euro;</td>
                <td colspan="2" style="padding: 4px 8px; text-align: right;">' . number_format(($row1->price * $row1->amount), 2, ',', ' ') . '&nbsp;&euro;</td>
            </tr>
            ');
            }
        }
        $mpdf->WriteHTML('
            <tr class="black" style="background-color: #ccc;">
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celková cena nákupu</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . number_format($row->price_total, 2, ',', '&nbsp;') . '&nbsp;&euro;</strong></td>
            </tr>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;">Spôsob doručenia <small>(' . $delivery['name'] . ')</small></td>
                <td colspan="1" align="right" style="padding: 4px 8px;">' . $delivery['price'] . '&nbsp;&euro;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;">Spôsob platby <small>(' . $payment['name'] . ')</small></td>
                <td colspan="1" align="right" style="padding: 4px 8px;">' . $payment['price'] . '&nbsp;&euro;</td>
            </tr>
            ');
        if (VAT_VISIBILITY === TRUE) {
            $mpdf->WriteHTML('
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celková cena nákupu s DPH</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . number_format($row->price_total, 2, '.', '') . '&nbsp;&euro;</strong></td>
            </tr>
            ');
            if ($row->order_discount != '0') {
                $mpdf->WriteHTML('
                <tr style="background: #EAEAEA;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Zľava za registráciu</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . REGISTRATION_DISCOUNT . '%</strong></td>
                </tr>
                ');
            }
            $mpdf->WriteHTML('
            <tr>
                 <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr style="font-size: 19px;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celkom</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>'); // . number_format((($row->price_total * VAT_COEFFICIENT) + $delivery['price'] + $payment['price']), 2, '.', '') .
            if ($row->order_discount != '0') {
                $mpdf->WriteHTML(number_format(((($row->price_total / VAT_COEFFICIENT) * ((100 - REGISTRATION_DISCOUNT) / 100)) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;'));
            } else {
                $mpdf->WriteHTML(number_format((($row->price_total / VAT_COEFFICIENT) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;'));
            }
            $mpdf->WriteHTML('&nbsp;&euro;</strong></td>
            </tr>
            ');
        } else {
            if ($row->order_discount != '0') {
                $mpdf->WriteHTML('
                <tr style="background: #EAEAEA;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Zľava za registráciu</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;"><strong>' . REGISTRATION_DISCOUNT . '%</strong></td>
                </tr>
                ');
            }
            $mpdf->WriteHTML('
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr style="font-size: 19px;">
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celkom</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong>'); //. number_format(($row->price_total + $delivery['price'] + $payment['price']), 2, '.', '') .
            if ($row->order_discount != '0') {
                $mpdf->WriteHTML(number_format((($row->price_total * ((100 - REGISTRATION_DISCOUNT) / 100)) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;'));
            } else {
                $mpdf->WriteHTML(number_format(($row->price_total + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;'));
            }
            $mpdf->WriteHTML('&nbsp;&euro;</strong></td>
            </tr>
                ');
        }
        $mpdf->WriteHTML('
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
        </table>
            ');

        $mpdf->Output('docs/faktura-'. $row->order_id .'.pdf', 'F');

        //$content = $mpdf->Output('', 'S');
        //$content = chunk_split(base64_encode($content));
        /*
        I: send the file inline to the browser. The plug-in is used if available. The name given by $filename is used when one selects the “Save as” option on the link generating the PDF.
		D: send to the browser and force a file download with the name given by $filename.
		F: save to a local file with the name given by $filename (may include a path).
		S: return the document as a string. $filename is ignored.
		*/
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////
}

/*
 * ******************************************************************************************************************************************
 * ******************************************************************************************************************************************
 * ******************************************************************************************************************************************
 */

/* * **********************************
 * 	POUZITIE:
 *
 * 	$obj_paginator = new Paginator;

  $obj_paginator->set_items_per_page(24);									//	pocet zobrazenych poloziek na 1 stranke
  $obj_paginator->set_items_count(480);									//	pocet poloziek v databaze

  $obj_paginator->set_params_base(Menu::getHyperLinkById($navigateId));	//	base... to ani netreba menit
  $obj_paginator->set_params($navigateArrayUrlWithoutBase);				//	ani toto nie je treba menit... maximalne k tomu pripojit dalsie parametre, ak treba

  echo $obj_paginator->get_paginator();									//	vypise paginator

  $obj_paginator->debug();												//	vypise zadane a vypocitane nastavenia paginatora

  ...

  a v query, ktore taha samotne polozky sa nastavi LIMIT $obj_paginator->get_page_start(), 24;
 */

interface iPaginator {

    public function set_items_per_page($value);

    public function set_items_count($value);

    public function set_params_base($value);

    public function set_params($value);

    public function get_page_start();

    public function get_page_end();

    public function get_paginator();

    public function debug();
}

/* * ************************************************************************* */

class Paginator implements iPaginator {

    private $_items_per_page;
    private $_items_count;
    private $_pages;
    private $_params_base;
    private $_params;
    private $_params_count;
    private $_actual_page;
    private $_page_start;
    private $_page_end;

    /*     * ************************************************************************* */
    /*     * ************************************************************************* */
    /*     * ************************************************************************* */

    public function __construct() {
        $this->_items_per_page = 0;
        $this->_items_count = 0;
        $this->_pages = 1;

        $this->_page_start = 0;
    }

    /*     * ************************************************************************* */

    public function set_items_per_page($value) {
        $this->_items_per_page = (int) $value;
    }

    /*     * ************************************************************************* */

    public function set_items_count($value) {
        $this->_items_count = (int) $value;
    }

    /*     * ************************************************************************* */

    public function set_params_base($value) {
        $this->_params_base = $value;
    }

    /*     * ************************************************************************* */

    public function set_params($value) {
        $this->_params = $value;

        $this->set_params_count();
    }

    /*     * ************************************************************************* */

    public function get_page_start() {
        $this->set_page_start();

        return $this->_page_start;
    }

    /*     * ************************************************************************* */

    public function get_page_end() {
        $this->set_page_end();

        return $this->_page_end;
    }

    /*     * ************************************************************************* */

    public function get_paginator() {
        global $cTranslator;
        $this->set_pages();
        $this->set_actual_page();

        if ($this->_pages == '1')
            return false;

        //$this->_pages = 10;

        $end = $_GET['catalogue_keyword'] != NULL ? '?catalogue_keyword=' . $_GET['catalogue_keyword'] : '';
        $address = (isset($_GET['manufacturer']) ? '?manufacturer=' . $_GET['manufacturer'] : '') . (isset($_GET['q']) ? '?q=' . $_GET['q'] : '') . (isset($_GET['type']) ? '&type=' . $_GET['type'] : '') . (isset($_GET['ff']) ? '&ff=' . (is_array($_GET['ff']) ? implode(',', $_GET['ff']) : $_GET['ff']) : '');

        echo '<div class="paginator">';
        echo '<ul class="pagination">';
        if ($this->_params[$this->_params_count - 2] == 'strana' && is_numeric($this->_params[$this->_params_count - 1])) {
            // odkazy na predoslu a prvu stranku
            if (intval($this->_actual_page) != 1) {
                echo '<li><a href="' . $this->_params_base . '/' . $end . $address . '">' . $cTranslator->getTranslation('<< prvá', 0) . '</a></li>';
                if ((intval($this->_actual_page) - 1) != 1) {
                	if($this->_pages >= $this->_actual_page) {
                    	echo '<li><a href="' . $this->_params_base . '/' . $this->_params[$this->_params_count - 2] . '/' . ($this->_params[$this->_params_count - 1] - 1) . $end . $address . '">' . $cTranslator->getTranslation('<< predošlá', 0) . '</a></li>';
                	}
                } else {
                    echo '<li><a href="' . $this->_params_base . '/' . $end . $address . '">' . $cTranslator->getTranslation('<< predošlá', 0) . '</a></li>';
                }
            }
            echo '<li><a href="' . $this->_params_base . '/' . $end . $address . '">1</a></li>';

            /*             * ********************** */

            // zvysne tri odkazy
            if ($this->_actual_page > 3) {
                echo '<li><span>...</span></li>';
            }

            if ($this->_actual_page - 1 > 1 && ($this->_actual_page) <= $this->_pages) {
                echo '<li><a href="' . $this->_params_base . '/' . $this->_params[$this->_params_count - 2] . '/' . ($this->_actual_page - 1) . $end . $address . '">' . ($this->_actual_page - 1) . '</a></li>';
            }

            /*             * ********************** */
            if ($this->_actual_page <= $this->_pages) {
	            echo '<li class="selected"><span>' . $this->_actual_page . '</span></li>';
	        }

            /*             * ********************** */

            if ($this->_actual_page + 1 < $this->_pages) {
                echo '<li><a href="' . $this->_params_base . '/' . $this->_params[$this->_params_count - 2] . '/' . ($this->_actual_page + 1) . $end . $address . '">' . ($this->_actual_page + 1) . '</a></li>';
            }

            if ($this->_actual_page < ($this->_pages - 2)) {
                echo '<li><span>...</span></li>';
            }

            /*             * ********************** */

            // odkazy na nasledujucu a poslednu stranku
            if ($this->_pages > $this->_actual_page) {
                echo '<li><a href="' . $this->_params_base . '/' . $this->_params[$this->_params_count - 2] . '/' . $this->_pages . $end . $address . '"> ' . $this->_pages . '</a></li>';
            }
            if (intval($this->_actual_page) != $this->_pages && $this->_actual_page < $this->_pages) {
                echo '<li><a href="' . $this->_params_base . '/' . $this->_params[$this->_params_count - 2] . '/' . ($this->_params[$this->_params_count - 1] + 1) . $end . $address . '">' . $cTranslator->getTranslation('ďalšia >>', 0) . '</a></li>';
            }
        } else {
            if ($this->_pages > 1) {
                echo '<li class="selected"><span>1</span></li>';

                for ($i = 2; $i < 5; ++$i) {
                    if ($i < $this->_pages) {
                        echo '<li><a href="' . $this->_params_base . '/strana/' . $i . $end . $address . '">' . $i . '</a></li>';
                        $last_i = $i;
                    }
                }

                if ($last_i < $this->_pages) {
                    if ($this->_pages - $last_i > 1 AND $this->_pages != '2') {
                        echo '<li><span>..|.</span></li><li><a href="' . $this->_params_base . '/strana/' . $this->_pages . $end . $address . '">' . $this->_pages . '</a></li>';
                    } else {
                        echo '<li><a href="' . $this->_params_base . '/strana/' . $this->_pages . $end . $address . '">' . $this->_pages . '</a></li>';
                    }
                }
            }
        }
        if (intval($this->_actual_page) == '1') {
            echo '<li><a href="' . $this->_params_base . '/strana/2' . $end . $address . '">' . $cTranslator->getTranslation('ďalšia >>', 0) . '</a></li>';
        }
        if (intval($this->_actual_page) != $this->_pages) {
            echo '<li><a href="' . $this->_params_base . '/strana/' . $this->_pages . $address . '">' . $cTranslator->getTranslation('posledná >>', 0) . '</a></li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    /*     * ************************************************************************* */

    public function debug() {
        echo '[_items_per_page]: ' . $this->_items_per_page . '<br/>';
        echo '[_items_count]: ' . $this->_items_count . '<br/>';
        echo '[_pages]: ' . $this->_pages . '<br/>';

        echo '[_params_base]: ' . $this->_params_base . '<br/>';
        ;

        echo '[_params]: ';
        if (is_array($this->_params)) {
            foreach ($this->_params as $i => $v) {
                echo '[' . $i . '] => ' . $v . '; ';
            }
        }
        echo '<br/>';

        echo '[_params_count]: ' . $this->_params_count . '<br/>';

        echo '[_actual_page]: ' . $this->_actual_page . '<br/>';
        echo '[_page_start]: ' . $this->_page_start . '<br/>';
        echo '[_page_end]: ' . $this->_page_end . '<br/>';
    }

    /*     * ************************************************************************* */
    /*     * ************************************************************************* */
    /*     * ************************************************************************* */

    private function set_pages() {
        if ($this->_items_count > 0 && $this->_items_per_page > 0) {
            $this->_pages = ceil($this->_items_count / $this->_items_per_page);
        } else {
            $this->_pages = 1;
        }
    }

    /*     * ************************************************************************* */

    private function set_page_start() {
        $this->set_actual_page();

        $this->_page_start = ((($this->_actual_page * $this->_items_per_page) - 1) - ($this->_items_per_page - 1));
    }

    /*     * ************************************************************************* */

    private function set_page_end() {
        $this->set_actual_page();

        $this->_page_end = ((($this->_actual_page * $this->_items_per_page) - 1));
    }

    /*     * ************************************************************************* */

    private function set_params_count() {
        $this->_params_count = count($this->_params);
    }

    /*     * ************************************************************************* */

    private function set_actual_page() {
        if ($this->_params[$this->_params_count - 2] == 'strana') {
            $this->_actual_page = $this->_params[$this->_params_count - 1];
        } else {
            $this->_actual_page = 1;
        }
    }

}
