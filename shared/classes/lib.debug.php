<?

/*
 * 	VERZIA 1.5
 *
 * 	POUZITIE:
 * 	var_deb();									-	vypise v konzole "this is beacon"
 * 	var_deb($var);								-	debugovanie premennej
 * 	var_deb($var, 'text');						-	debugovanie premennej s poznamkou
 *
 * 	superg_deb();								-	vypise obsah superglobalnnych premennych
 * 	input_deb();								-	vypise obsah vybranych superglobalnnych premennych
 * 	stored_deb();								-	vypise obsah vybranych superglobalnnych premennych
 * 	server_deb();								-	vypise obsah vybranych superglobalnnych premennych
 *
 * 	unit_deb($function);						-	vypise unit test funkcie
 * 	unit_deb($function, $params);				-	vypise unit test funkcie s vlastnymi parametrami v 2D poli
 * 	unit_deb($function, $params, $expectation);	-	vypise unit test funkcie s vlastnymi parametrami v 2D poli a ocakavanym datovym typom vysledku
 *
 * 	CHANGELOG:
 * 	v1.1	-	hodnoty osetruje addslashes()
 * 	v1.2	-	opraveny vypis pri zalomeni textu a osetrene problemove znaky
 * 	v1.3 	-	pridane funkcie pre vypis superglobalnych premennych superg_deb(), input_deb(), stored_deb(), server_deb()
 * 	v1.4 	-	pridana funkcia unit_deb() pre unit testing
 * 	v1.41   -	do unit_deb() pridany aj ocakavany datovy typ vysledku a jeho porovnanie s datovym typom vysledku
 * 	v1.42	-	vypis je zavisly od bool konstanty DEVELOPMENT
 * 	v1.5	-   var_deb() uz nema povinny ani prvy argument. Pri zadani var_deb(); sa v konzole vypise this is beacon namiesto informacii o premennej
 */

/* * ********************************************************************************************************************************************************************************* */

/*
 * 	Funkcia, ktora vypisuje do konzoly informacie o zvolenej premennej
 *
 * 	@params 	string 		meno debugovanej premennej
 * 	@params 	string 		alternativny nazov premennej
 *
 * 	@return
 */

function var_deb($variable = null, $variable_name = null) {
    $bt = debug_backtrace();
    $caller = array_shift($bt);

    if (empty($variable)) {
        $output = '%c[' . date('H:i:s') . '] %cthis is %cbeacon %c(' . end(explode('/', $caller['file'])) . '@' . $caller['line'] . ') %c';
    } else {
        switch (gettype($variable)) {
            /*             * ********************************************************************************************************************************************************************************* */

            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
                $output = '%c[' . date('H:i:s') . '] %c' . gettype($variable) . '%c' . print_var_name($variable) . '' . (!empty($variable_name) ? ' \"' . addslashes($variable_name) . '\"' : '') . ' %c(' . end(explode('/', $caller['file'])) . '@' . $caller['line'] . '): %c' . addslashes(clean_value($variable));
                break;

            /*             * ********************************************************************************************************************************************************************************* */

            case 'array':
                $output = '%c[' . date('H:i:s') . '] %c' . gettype($variable) . '%c' . print_var_name($variable) . '' . (!empty($variable_name) ? ' \"' . addslashes($variable_name) . '\"' : '') . ' %c(' . end(explode('/', $caller['file'])) . '@' . $caller['line'] . '):\r\n%cArray\r\n(';

                foreach ($variable as $key => $value) {
                    $temp[] = '\r\n [' . $key . '] => ' . gettype($value) . ' ' . addslashes(clean_value($value));

                    if (is_array($value)) {
                        $temp[] = '\r\n  Array\r\n  (';
                        foreach ($value as $key2 => $value2) {
                            $temp2[] = '\r\n   [' . $key . '][' . $key2 . '] => ' . gettype($value2) . ' ' . addslashes(clean_value($value2));
                        }
                        $temp[] = implode('', $temp2) . '\r\n  )';
                    }
                }

                $output .= implode('', $temp) . '\r\n)';
                break;

            /*             * ********************************************************************************************************************************************************************************* */

            case 'object':
                $output = '%c[' . date('H:i:s') . '] %c' . gettype($variable) . '%c' . print_var_name($variable) . '' . (!empty($variable_name) ? ' \"' . addslashes($variable_name) . '\"' : '') . ' %c(' . end(explode('/', $caller['file'])) . '@' . $caller['line'] . '):\r\n%cObject\r\n(';

                foreach (get_object_vars($variable) as $key => $value) {
                    $temp[] = '\r\n [' . $key . '] => ' . gettype($value) . ' ' . addslashes(clean_value($value));

                    if (is_array($value)) {
                        $temp[] = '\r\n  Array\r\n  (';
                        foreach ($value as $key2 => $value2) {
                            $temp2[] = '\r\n   [' . $key . '][' . $key2 . '] => ' . gettype($value2) . ' ' . addslashes(clean_value($value2));
                        }
                        $temp[] = implode('', $temp2) . '\r\n  )';
                    }
                }

                $output .= implode('', $temp) . '\r\n)';
                break;

            /*             * ********************************************************************************************************************************************************************************* */

            case 'resource':
            case 'NULL':
            case 'unknown type':
            default:
                $output = '%c[' . date('H:i:s') . '] %c' . gettype($variable) . '%c' . print_var_name($variable) . '' . (!empty($variable_name) ? ' \"' . addslashes($variable_name) . '\"' : '') . ' %c(' . end(explode('/', $caller['file'])) . '@' . $caller['line'] . ') %c';
                break;

            /*             * ********************************************************************************************************************************************************************************* */
        }
    }

    if (DEVELOPMENT === true) {
        echo '<script>console.info("' . $output . '", "color:gray", "font-style: italic", "color:green", "color:gray", "color:red");</script>';
        echo '<script>console.log(' . json_encode(debug_backtrace()) . ')</script>';
    }
}

/* * ********************************************************************************************************************************************************************************* */

/*
 * 	Funkcia, ktora vypisuje do konzoly informacie zo vsetkych superglobalnych premennych
 *
 * 	@return
 */

function superg_deb() {
    if (DEVELOPMENT === true) {
        print_http($_REQUEST, '$_REQUEST');
        print_http($_GET, '$_GET');
        print_http($_POST, '$_POST');
        print_http($_FILES, '$_FILES');
        print_http($_COOKIE, '$_COOKIE');
        print_http($_SESSION, '$_SESSION');
        print_http($_SERVER, '$_SERVER');
        print_http($_ENV, '$_ENV');
    }
}

/* * ********************************************************************************************************************************************************************************* */

/*
 * 	Funkcia, ktora vypisuje do konzoly informacie z $_REQUEST, $_GET, $_POST, $_FILES
 *
 * 	@return
 */

function input_deb() {
    if (DEVELOPMENT === true) {
        print_http($_REQUEST, '$_REQUEST');
        print_http($_GET, '$_GET');
        print_http($_POST, '$_POST');
        print_http($_FILES, '$_FILES');
    }
}

/* * ********************************************************************************************************************************************************************************* */

/*
 * 	Funkcia, ktora vypisuje do konzoly informacie z $_COOKIE, $_SESSION
 *
 * 	@return
 */

function stored_deb() {
    if (DEVELOPMENT === true) {
        print_http($_COOKIE, '$_COOKIE');
        print_http($_SESSION, '$_SESSION');
    }
}

/* * ********************************************************************************************************************************************************************************* */

/*
 * 	Funkcia, ktora vypisuje do konzoly informacie z $_SERVER, $_ENV
 *
 * 	@return
 */

function server_deb() {
    if (DEVELOPMENT === true) {
        print_http($_SERVER, '$_SERVER');
        print_http($_ENV, '$_ENV');
    }
}

/* * ********************************************************************************************************************************************************************************* */

/*
 * 	Funkcia, ktora jednotkovo testuje zvolenu funkciu
 *
 * 	@return
 */

function unit_deb($function, $params = NULL, $expectation = NULL) {
    $args = discover_arguments($function);
    $argc = count($args);

    if ($params) {
        foreach ($params as $param) {
            $i = 0;
            foreach ($args as $arg) {
                $params_test[] = '$' . $arg . ' = ' . gettype($param[$i]) . ' ' . $param[$i];
                $args_names[] = '$' . $arg;
                $args_test[] = $param[$i];

                ++$i;
            }

            $result = call_user_func_array($function, $args_test);

            $output = unit_test_template($function, $args_names, $params_test, $args_test, $result, $expectation);

            if (DEVELOPMENT === true) {
                echo '<script>console.log("' . $output[0] . '", ' . $output[1] . ');</script>';
            }

            unset($output);
            unset($temp);
            unset($params_test);
            unset($args_names);
            unset($args_test);
        }
    } else {
        $params = prepare_params($args);

        foreach ($params as $param) {
            $params_test = $param[0];
            $args_names = $param[1];
            $args_test = $param[2];

            $result = call_user_func_array($function, $args_test);

            $output = unit_test_template($function, $args_names, $params_test, $args_test, $result, NULL);

            if (DEVELOPMENT === true) {
                echo '<script>console.log("' . $output[0] . '", "color:gray", "color:blue", "color:black", "color:blue", "color:blue");</script>';
            }

            unset($output);
            unset($temp);
            unset($params_test);
            unset($args_names);
            unset($args_test);
        }
    }
}

/* * ********************************************************************************************************************************************************************************* */
/* POMOCNE FUNKCIE A RUTINY ******************************************************************************************************************************************************** */
/* * ********************************************************************************************************************************************************************************* */

/* * ********************************************************************************* */
/*
 * 	Funkcia, ktora vypise meno premennej
 *
 * 	@params 	string 		premenna
 *
 * 	@return     string
 *   @return 	boolean
 */

function print_var_name($var) {
    foreach ($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return ' $' . $var_name;
        }
    }

    return false;
}

/* * ********************************************************************************* */
/*
 * 	Funkcia, ktora vycisti zalomenia a <>
 *
 * 	@params 	string 		premenna
 *
 * 	@return     string
 */

function clean_value($value) {
    $value = trim(preg_replace('/\s+/', ' ', $value));
    $value = str_replace('<', '&lt;', $value);
    $value = str_replace('>', '&gt;', $value);

    return $value;
}

/* * ********************************************************************************* */
/*
 * 	Metoda, ktora vypise obsah $_REQUEST
 *
 * 	@params 	string 		premenna
 * 	@params 	string 		nazov premennej
 *
 * 	@return
 */

function print_http($http, $name) {
    $output = '%c[' . date('H:i:s') . '] %c' . $name . ':\r\n%cArray\r\n(';

    foreach ($http as $key => $value) {
        $temp[] = '\r\n [' . $key . '] => ' . gettype($value) . ' ' . addslashes(clean_value($value));

        if (is_array($value)) {
            $temp[] = '\r\n  Array\r\n  (';
            foreach ($value as $key2 => $value2) {
                $temp2[] = '\r\n   [' . $key . '][' . $key2 . '] => ' . gettype($value2) . ' ' . addslashes(clean_value($value2));
            }
            $temp[] = implode('', $temp2) . '\r\n  )';
        }
    }

    $output .= implode('', $temp) . '\r\n)';

    echo '<script>console.log("' . $output . '", "color:gray", "color:green", "color:blue");</script>';
}

/* * ********************************************************************************* */
/*
 * 	Metoda, ktora zisti zoznam argumentov funkcie
 *
 * 	@params 	string 		funkcia
 *
 * 	@return 	array
 */

function discover_arguments($function) {
    $reflector = new ReflectionFunction($function);
    $params = array();
    foreach ($reflector->getParameters() as $param) {
        $params[] = $param->name;
    }

    return $params;
}

/* * ********************************************************************************* */
/*
 * 	Metoda, ktora vyplna sablonu pre unit test vystup
 *
 * 	@params 	string 		funkcia
 *
 * 	@return 	array
 */

function unit_test_template($function, $args_names, $params_test, $args_test, $result, $expectation) {
    $temp[] = '%c[' . date('H:i:s') . '] %cUnit testing: %cfunction ' . $function . '(' . implode(', ', $args_names) . ')';
    $temp[] = '\r\n';
    $temp[] = '\r\n';
    $temp[] = '%cArgs {';
    $temp[] = '\r\n ';
    $temp[] = implode('\r\n ', $params_test) . '\r\n';
    $temp[] = '}';
    $temp[] = '\r\n';
    $temp[] = '\r\n';

    $temp[] = '%cResult: ' . gettype($result) . ' ' . $result;

    if ($expectation) {
        $temp[] = '\r\n';
        if (gettype($result) == $expectation) {
            $temp[] = '%cMatch: OK';
            $temp_format = '"color:gray", "color:blue", "color:black", "color:blue", "color:green", "color:green"';
        } else {
            $temp[] = '%cMatch: FAIL (' . $expectation . ' expected!)';
            $temp_format = '"color:gray", "color:blue", "color:black", "color:blue", "color:red", "color:red"';
        }
    } else {
        $temp_format = '"color:gray", "color:blue", "color:black", "color:blue", "color:blue"';
    }

    $output .= implode('', $temp) . '\r\n';

    return array($output, $temp_format);
}

/* * ********************************************************************************* */
/*
 * 	Metoda, ktora pripravi sadu parametrov pre unit testing
 *
 * 	@params 	array 		zoznam argumentov

 * 	@return 	array
 */

function prepare_params($args) {
    //	SCENARIO 1 - RANDOM NUMBERS > 0
    $rnd_val = rand(1, 10);
    foreach ($args as $arg) {
        $params_test[] = '$' . $arg . ' = ' . gettype($rnd_val) . ' ' . $rnd_val;
        $args_names[] = '$' . $arg;
        $args_test[] = $rnd_val;
    }
    $output[] = array($params_test, $args_names, $args_test);
    unset($params_test);
    unset($args_names);
    unset($args_test);

    //	SCENARIO 2 - RANDOM NUMBERS < 0
    $rnd_val = rand(1, 10) * -1;
    foreach ($args as $arg) {
        $params_test[] = '$' . $arg . ' = ' . gettype($rnd_val) . ' ' . $rnd_val;
        $args_names[] = '$' . $arg;
        $args_test[] = $rnd_val;
    }
    $output[] = array($params_test, $args_names, $args_test);
    unset($params_test);
    unset($args_names);
    unset($args_test);

    //	SCENARIO 3 - NULLs
    $rnd_val = NULL;
    foreach ($args as $arg) {
        $params_test[] = '$' . $arg . ' = ' . gettype($rnd_val) . ' ' . $rnd_val;
        $args_names[] = '$' . $arg;
        $args_test[] = $rnd_val;
    }
    $output[] = array($params_test, $args_names, $args_test);
    unset($params_test);
    unset($args_names);
    unset($args_test);

    //	SCENARIO 4 - TRUE
    $rnd_val = TRUE;
    foreach ($args as $arg) {
        $params_test[] = '$' . $arg . ' = ' . gettype($rnd_val) . ' ' . $rnd_val;
        $args_names[] = '$' . $arg;
        $args_test[] = $rnd_val;
    }
    $output[] = array($params_test, $args_names, $args_test);
    unset($params_test);
    unset($args_names);
    unset($args_test);

    //	SCENARIO 5 - FALSE
    $rnd_val = FALSE;
    foreach ($args as $arg) {
        $params_test[] = '$' . $arg . ' = ' . gettype($rnd_val) . ' ' . $rnd_val;
        $args_names[] = '$' . $arg;
        $args_test[] = $rnd_val;
    }
    $output[] = array($params_test, $args_names, $args_test);
    unset($params_test);
    unset($args_names);
    unset($args_test);

    //	SCENARIO 6 - STRING
    $rnd_val = 'autotest';
    foreach ($args as $arg) {
        $params_test[] = '$' . $arg . ' = ' . gettype($rnd_val) . ' ' . $rnd_val;
        $args_names[] = '$' . $arg;
        $args_test[] = $rnd_val;
    }
    $output[] = array($params_test, $args_names, $args_test);
    unset($params_test);
    unset($args_names);
    unset($args_test);

    return $output;
}

?>
