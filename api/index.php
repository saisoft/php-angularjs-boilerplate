<?php

error_reporting(0);
session_start();

require 'Slim/Slim.php';
require 'dao/dbconnect.php';
require 'dto/ResponceObject.php';

$app = new Slim(array(
    'debug' => true,
    'log.enabled' => true
        ));

require './controller/LoginController.php';
require './controller/ChangePasswordController.php';
require './controller/ProfileController.php';
require './controller/NotificationController.php';
require './controller/admdshController.php';

class ResourceNotFoundException extends Exception {
    
}

class ExamTimeoutException extends Exception {
    
}

function updateSessionTime() {
    $app = Slim::getInstance();
    if ($app->request()->getResourceUri() == '/notificationCount') {
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
            // last request was more than 60 minutes ago
            session_unset();     // unset $_SESSION variable for the run-time 
            session_destroy();   // destroy session data in storage
        }
    } else {
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}

function authenticateEmployee() {
    $app = Slim::getInstance();
    $uid = $app->getEncryptedCookie('projectnameId');
    $key = $app->getEncryptedCookie('projectnameKey');
    $headers = $app->request()->headers();
    if (isset($headers['x-xsrf-token'])) {
        $token = $headers['x-xsrf-token'];
        if (validateUserKey($uid, $key) == false || $token != $_SESSION['xsrf']) {
            $app->halt(401);
        }
    } else {
        $app->halt(401);
    }
    updateSessionTime();
}

function authenticateEmployer() {
    $app = Slim::getInstance();
    $uid = $app->getEncryptedCookie('projectnameId');
    $key = $app->getEncryptedCookie('projectnameKey');
    $headers = $app->request()->headers();
    if (isset($headers['x-xsrf-token'])) {
        $token = $headers['x-xsrf-token'];

        $admin = 0;
        $empr = 0;

        if (isset($_SESSION['LOGIN_EMPR'])) {
            $empr = 1;
        }
        if (isset($_SESSION['LOGIN_ADM'])) {
            $admin = 1;
        }
        if (($empr == 0 && $admin == 0) || validateUserKey($uid, $key) == false || $token != $_SESSION['xsrf']) {
            $app->halt(401);
        }
    } else {
        $app->halt(401);
    }
    updateSessionTime();
}

function authenticateAdmin() {
    $app = Slim::getInstance();
    $uid = $app->getEncryptedCookie('projectnameId');
    $key = $app->getEncryptedCookie('projectnameKey');
    $headers = $app->request()->headers();
    if (isset($headers['x-xsrf-token'])) {
        $token = $headers['x-xsrf-token'];

        if ($token != $_SESSION['xsrf'] || validateUserKey($uid, $key) == false || !isset($_SESSION['LOGIN_ADM'])) {
            $app->halt(401);
        }
    } else {
        $app->halt(401);
    }
    updateSessionTime();
}

function validateUserKey($uid, $key) {
    // insert your (hopefully more complex) validation routine here
    if (isset($_SESSION['projectnameId']) && isset($_SESSION['projectnameKey']) && $uid == $_SESSION['projectnameId'] && $key == $_SESSION['projectnameKey']) {
        return true;
    } else {
        return false;
    }
}

function validateInput($text, $type) {
    $app = Slim::getInstance();
    $username = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/";
    $password = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,15}$/";

    $idRegex = "/^[0-9]{1,11}$/";
    $pincode = "/^[0-9]{6}$/";
    $contactno = "/^[789]\d{9}$/";
    $address = "/^[a-zA-Z0-9\s]{10,50}$/";
    $city = "/^[a-zA-Z\s]{4,20}$/";
    $email = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
    $date = "/^(0[1-9]|1\d|2\d|3[01])\/(0[1-9]|1[0-2])\/(19|20)\d{2}$/";
    $gender = "/^(m|f)$/";

    //add the server side regex here    


    switch ($type) {

        case 'username': if (!preg_match($username, $text)) {
                $app->halt(400);
            } else {
                return $text;
            }
            break;
        case 'password': if (!preg_match($password, $text)) {
                $app->halt(400);
            } else {
                return $text;
            }
            break;
        case 'ids': if (!preg_match($idRegex, $text)) {
                $app->halt(400);
            } else {
                return $text;
            }
            break;
        case 'pincode': if (!preg_match($pincode, $text)) {
                $app->halt(400);
            } else {
                return $text;
            }
            break;
        case 'contactno': if (!preg_match($contactno, $text)) {
                $app->halt(400);
            } else {
                return $text;
            }
            break;
        case 'address': if (!preg_match($address, $text)) {
                $app->halt(400);
            } else {
                return $text;
            }
            break;

        case 'city': if (!preg_match($city, $text)) {
                $app->halt(400);
            } else {
                return $text;
            }
            break;

        case 'date': if (!preg_match($date, $text)) {
                $app->halt(400);
            } else {
                return $text;
            }
            break;

        //add the regex cases here

        default:$app->halt(400);
            break;
    }
}

function underscoreToCamelCase($string, $first_char_caps = false) {
    if ($first_char_caps == true) {
        $string[0] = strtoupper($string[0]);
    }
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return preg_replace_callback('/_([a-z])/', $func, $string);
}

function upDateResponseDto($resDto) {
    $json = json_encode((array) $resDto);
    $json = str_replace("\u0000ResponseDto\u0000", "", $json);
    $json = str_replace("\u00a0", "", $json);

    return $json;
}

function array2json($arr) {
    if (function_exists('json_encode'))
        return json_encode($arr); //Lastest versions of PHP already has this functionality.
    $parts = array();
    $is_list = false;

    //Find out if the given array is a numerical array
    $keys = array_keys($arr);
    $max_length = count($arr) - 1;
    if (($keys[0] == 0) and ( $keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
        $is_list = true;
        for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
            if ($i != $keys[$i]) { //A key fails at position check.
                $is_list = false; //It is an associative array.
                break;
            }
        }
    }

    foreach ($arr as $key => $value) {
        if (is_array($value)) { //Custom handling for arrays
            if ($is_list)
                $parts[] = array2json($value); /* :RECURSION: */
            else
                $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
        } else {
            $str = '';
            if (!$is_list)
                $str = '"' . $key . '":';

            //Custom handling for multiple data types
            if (is_numeric($value))
                $str .= $value; //Numbers
            elseif ($value === false)
                $str .= 'false'; //The booleans
            elseif ($value === true)
                $str .= 'true';
            else
                $str .= '"' . addslashes($value) . '"'; //All other things


















                
// :TODO: Is there any more datatype we should be in the lookout for? (Object?)

            $parts[] = $str;
        }
    }
    $json = implode(',', $parts);

    if ($is_list)
        return '[' . $json . ']'; //Return numerical JSON
    return '{' . $json . '}'; //Return associative JSON
}

function get_rand_id($length) {
    $rand_id = "";
    $length = $length - 3;
    if ($length > 0) {

        for ($i = 1; $i <= $length; $i++) {
            $num = mt_rand(1, 26);
            $rand_id .= assign_rand_value($num);
        }

        $num = mt_rand(1, 10);
        $rand_id .= assign_rand_number($num);

        $num = mt_rand(1, 26);
        $rand_id .= assign_rand_caps($num);

        $num = mt_rand(1, 10);
        $rand_id .= assign_rand_symbol($num);
    }
    return $rand_id;
}

function assign_rand_value($num) {
// accepts 1 - 36
    switch ($num) {
        case "1":
            $rand_value = "a";
            break;
        case "2":
            $rand_value = "b";
            break;
        case "3":
            $rand_value = "c";
            break;
        case "4":
            $rand_value = "d";
            break;
        case "5":
            $rand_value = "e";
            break;
        case "6":
            $rand_value = "f";
            break;
        case "7":
            $rand_value = "g";
            break;
        case "8":
            $rand_value = "h";
            break;
        case "9":
            $rand_value = "i";
            break;
        case "10":
            $rand_value = "j";
            break;
        case "11":
            $rand_value = "k";
            break;
        case "12":
            $rand_value = "l";
            break;
        case "13":
            $rand_value = "m";
            break;
        case "14":
            $rand_value = "n";
            break;
        case "15":
            $rand_value = "o";
            break;
        case "16":
            $rand_value = "p";
            break;
        case "17":
            $rand_value = "q";
            break;
        case "18":
            $rand_value = "r";
            break;
        case "19":
            $rand_value = "s";
            break;
        case "20":
            $rand_value = "t";
            break;
        case "21":
            $rand_value = "u";
            break;
        case "22":
            $rand_value = "v";
            break;
        case "23":
            $rand_value = "w";
            break;
        case "24":
            $rand_value = "x";
            break;
        case "25":
            $rand_value = "y";
            break;
        case "26":
            $rand_value = "z";
            break;
    }
    return $rand_value;
}

function assign_rand_number($num) {
// accepts 1 - 36
    switch ($num) {
        case "1":
            $rand_value = "0";
            break;
        case "2":
            $rand_value = "1";
            break;
        case "3":
            $rand_value = "2";
            break;
        case "4":
            $rand_value = "3";
            break;
        case "5":
            $rand_value = "4";
            break;
        case "6":
            $rand_value = "5";
            break;
        case "7":
            $rand_value = "6";
            break;
        case "8":
            $rand_value = "7";
            break;
        case "9":
            $rand_value = "8";
            break;
        case "10":
            $rand_value = "9";
            break;
    }
    return $rand_value;
}

function assign_rand_caps($num) {
// accepts 1 - 36
    switch ($num) {
        case "1":
            $rand_value = "A";
            break;
        case "2":
            $rand_value = "B";
            break;
        case "3":
            $rand_value = "C";
            break;
        case "4":
            $rand_value = "D";
            break;
        case "5":
            $rand_value = "E";
            break;
        case "6":
            $rand_value = "F";
            break;
        case "7":
            $rand_value = "G";
            break;
        case "8":
            $rand_value = "H";
            break;
        case "9":
            $rand_value = "I";
            break;
        case "10":
            $rand_value = "J";
            break;
        case "11":
            $rand_value = "K";
            break;
        case "12":
            $rand_value = "L";
            break;
        case "13":
            $rand_value = "M";
            break;
        case "14":
            $rand_value = "N";
            break;
        case "15":
            $rand_value = "O";
            break;
        case "16":
            $rand_value = "P";
            break;
        case "17":
            $rand_value = "Q";
            break;
        case "18":
            $rand_value = "R";
            break;
        case "19":
            $rand_value = "S";
            break;
        case "20":
            $rand_value = "T";
            break;
        case "21":
            $rand_value = "U";
            break;
        case "22":
            $rand_value = "V";
            break;
        case "23":
            $rand_value = "W";
            break;
        case "24":
            $rand_value = "X";
            break;
        case "25":
            $rand_value = "Y";
            break;
        case "26":
            $rand_value = "Z";
            break;
    }
    return $rand_value;
}

function assign_rand_symbol($num) {
// accepts 1 - 36
    switch ($num) {
        case "1":
            $rand_value = "!";
            break;
        case "2":
            $rand_value = "@";
            break;
        case "3":
            $rand_value = "#";
            break;
        case "4":
            $rand_value = "$";
            break;
        case "5":
            $rand_value = "%";
            break;
        case "6":
            $rand_value = "&";
            break;
        case "7":
            $rand_value = "*";
            break;
        case "8":
            $rand_value = "?";
            break;
        case "9":
            $rand_value = "~";
            break;
        case "10":
            $rand_value = "^";
            break;
    }
    return $rand_value;
}

$app->run();
?>
