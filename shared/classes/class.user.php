<?php

class User {

    public function randomGenerator($length = 8) {
        $password = "";
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        $maxlength = strlen($possible);
        if ($length > $maxlength) {
            $length = $maxlength;
        }
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

    public function passGenerator($pass, $user_id) {
        //$salt = $this->randomGenerator($length = 4);
        //$salt = '1d5as1d5as1das51as';
        //$password = md5($pass . $salt) . ':' . $salt;

        if (is_numeric($user_id) and $user_id != 0) {
            $queryString = "update  " . TABLE_PREFIX . "user set `pwd` = '" . $pass . "' where 1 and user_id = '" . $user_id . "';";
            $ResultR = mysql_query($queryString);
            if ($ResultR) {

            } else {
                print(mysql_error());
            }
        }

        return $password;
    }

    public function isAuthenticated() {
        if (is_numeric($_SESSION['user_id'])):
            return true;
        else:
            return false;
        endif;
    }

    public function Logout() {
        session_destroy();
        session_start();
        header("Location:" . ROOTDIR . "/");
        Message::setMessage("Boli ste úspešne odhlásený.", 0);
        exit;
    }

    public function isAdmin() {
        if ($this->isAuthenticated() and $_SESSION['admin'] == 1) {
            return true;
        }
    }

    public function isEditor() {
        if ($this->isAuthenticated() and $_SESSION['editor'] == 1) {
            return true;
        }
    }

    public function isUser() {
        if ($this->isAuthenticated() and $_SESSION['admin'] == 0) {
            return true;
        }
    }

    public function returnUserLogin($user_id) {
        if ($user_id == 0)
            return "Anonymný uživateľ";
        $queryString = "select username from " . TABLE_PREFIX . "user where 1 and user_id = '" . $user_id . "' limit 1;";
        $ResultQ = mysql_query($queryString);
        if ($ResultQ) {
            if (mysql_num_rows($ResultQ) == 1) {
                $RowQ = mysql_fetch_assoc($ResultQ);
                return $RowQ['username'];
            } else
                return false;
        }
    }

    public function returnUserMail($user_id) {
        if ($user_id == 0)
            return "Anonymný uživateľ";
        $queryString = "select mail, username from " . TABLE_PREFIX . "user where 1 and user_id = '" . $user_id . "' limit 1;";
        $ResultQ = mysql_query($queryString);
        if ($ResultQ) {
            if (mysql_num_rows($ResultQ) == 1) {
                $RowQ = mysql_fetch_assoc($ResultQ);
                return is_null($RowQ['mail']) ? $RowQ['username'] : $RowQ['mail'];
            } else
                return false;
        }
    }

    public function returnUserPhone($user_id) {
        if ($user_id == 0)
            return "Anonymný uživateľ";
        $queryString = "select phone from " . TABLE_PREFIX . "user_address_book where 1 and user_id = '" . $user_id . "' limit 1;";
        $ResultQ = mysql_query($queryString);
        if ($ResultQ) {
            if (mysql_num_rows($ResultQ) == 1) {
                $RowQ = mysql_fetch_assoc($ResultQ);
                return $RowQ['phone'];
            } else
                return false;
        }
    }

    public function Authenticate($mail = NULL, $passphrase = NULL) {
        $queryString = "select user_id from " . TABLE_PREFIX . "user where 1 and mail = '" . mysql_real_escape_string($mail) . "' and active = '1';";
        $ResultQ = mysql_query($queryString);
        if ($ResultQ):
            if (mysql_num_rows($ResultQ) == 1):
                $RowQ = mysql_fetch_assoc($ResultQ);

                //	overime si heslo
                //Pred overenim hesla sa z neho vypocita md5 hash, ktory sa porovna
                $passHash=  md5($passphrase);

                $queryString = "SELECT * FROM " . TABLE_PREFIX . "user WHERE 1 AND user_id = '" . $RowQ['user_id'] . "' AND pwd = '" . mysql_real_escape_string($passHash) . "' LIMIT 1;";
                //$queryString = "select * from " . TABLE_PREFIX . "user where 1 and user_id = '" . $RowQ['user_id'] . "' and pwd = '" . mysql_real_escape_string($passphrase) . "' limit 1;";
                $ResultR = mysql_query($queryString);
                if ($ResultR):
                    if (mysql_num_rows($ResultR) == 1):
                        $RowR = mysql_fetch_assoc($ResultR);

                        if ($_POST['redirecttosite'] == 1) {
                            setcookie("redirecttosite", 1, time() + (3600 * 24 * 7));
                        } else {
                            setcookie("redirecttosite", 0, time() + (3600 * 24 * 7));
                        }

                        //	nastavime session
                        $_SESSION['user_id'] = $RowR['user_id'];
                        $_SESSION['fullname'] = $RowR['fullname'];
                        $_SESSION['admin'] = $RowR['admin'];
                        $_SESSION['editor'] = $RowR['editor'];
                        $_SESSION['username'] = $RowR['username'];
                        $_SESSION['mail'] = $RowR['mail'];

                        if ($_SESSION['admin'] == 1) {
                            if ($_POST['redirecttosite'] == 1) {
                                header("Location:" . ROOTDIR . "/");
                            } else {
                                header("Location:" . ROOTDIR . "/setup/index.php?module=menu");
                            }
                            exit;
                        } elseif ($_SESSION['admin'] == 0) {
                            header("Location:" . ROOTDIR . "/");
                            Message::setMessage("Boli ste úspešne prihlásený.", 0);
                            exit;
                        } else {
                            Message::setMessage("Chyba v prihlasovaní.", 2);
                        }

                    else:
                        Message::setMessage("Bolo zadané nesprávne heslo.", 2);
                    endif;
                else:
                    print(mysql_error());
                endif;
            else:
                Message::setMessage("Zadaný užívateľ sa nenachádza v databáze užívateľov alebo ma zakázaný prístup.", 2);
            endif;
        else:
            print(mysql_error());
        endif;
    }

    public function hasRegistrationDiscount() {
        if (!is_numeric($_SESSION['user_id']))
            return false;

        $query = "SELECT registration_discount FROM " . TABLE_PREFIX . "user WHERE 1 AND user_id = '" . $_SESSION['user_id'] . "' LIMIT 1;";
        $result = mysql_query($query);
        if ($result) {
            $row = mysql_fetch_object($result);
            if ($row->registration_discount == '1') {
                return true;
            } else {
                return false;
            }
        }
    }

    
    public function insertUser($data_array) {
        if (is_array($data_array)) {
            if (!empty($data_array['mail']) AND $this->checkUser_email($data_array['mail']) === false) {
                if (!empty($data_array)) {
                    foreach ($data_array as $df => $value) {
                        $ins_cols[] = $df;
                        $ins_vals[] = '"' . $value . '"';
                    }

                    $query = 'INSERT INTO ' . TABLE_PREFIX . 'user (' . implode(',', $ins_cols) . ') VALUES (' . implode(',', $ins_vals) . ')';
                    if (mysql_query($query)) {
                        $new_user_id = mysql_insert_id();
                        return $new_user_id;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return 'email-exist';
            }
        } else {
            return false;
        }
    }
    public function checkUser_email($email) {
        $query = 'SELECT user_id FROM ' . TABLE_PREFIX . 'user WHERE mail="' . mysql_real_escape_string($email) . '"';

        if ($result = mysql_query($query)) {
            if (mysql_num_rows($result) != 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

?>
