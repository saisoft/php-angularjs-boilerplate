<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$app->get('/test/:id', 'authenticate', function ($id) use ($app) {
    echo 'I am working man' + $id;
});

$app->get('/captcha', function () use ($app) {
    $captchanumber = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; // Initializing PHP variable with string
    $imgnumber = substr(str_shuffle($captchanumber), 0, 9); // Getting first 6 word after shuffle.

    $_SESSION["code"] = $imgnumber; // Initializing session variable with above generated sub-string

    $im = imagecreatetruecolor(160, 35);
    $bg = imagecolorallocate($im, 115, 135, 157); //background color blue
    $fg = imagecolorallocate($im, 255, 255, 255); //text color white
    imagefill($im, 0, 0, $bg);
    imagestring($im, 15, 15, 15, $_SESSION["code"], $fg);
    header("Cache-Control: no-cache, must-revalidate");
    header('Content-type: image/png');
    imagepng($im);
    imagedestroy($im);
});


$app->post('/creds', function () use ($app) {
    $request = Slim::getInstance()->request();
    $forgotCreds = json_decode($request->getBody());
    $resDto = new ResponseDto();
    if ($forgotCreds->captcha && $forgotCreds->captcha != "" && $_SESSION["code"] == $forgotCreds->captcha) {
        try {
            $username = $forgotCreds->username;
            $contact = $forgotCreds->contact;
            $user = R::findOne('users', 'username=:username and contactno=:contact', array(':username' => $username, ':contact' => $contact));
            if ($user->enabled == 1 && $user->email_verified == 'Y') {
                $resDto->status = TRUE;
                $resDto->errorcode = 0;
                $smsdata = R::dispense('smsendsms');
                $smsdata->toid = $user->id;
                $smsdata->code = get_rand_id(9);
                $user->password = crypt($smsdata->code);
                $smsdata->smstype = 'forgotcreds';
                $smsdata->status = 'N';
                R::store($user);
                R::store($smsdata);
            } else {
                $resDto->status = FALSE;
                $resDto->errorcode = 1;
            }
        } catch (PDOException $e) {
            $resDto->status = FALSE;
            $resDto->errorcode = 1;
            $resDto->errorcode = $e->getMessage();
        } catch (Exception $e) {
            $resDto->status = FALSE;
            $resDto->errorcode = 1;
            $resDto->errorMessage = $e->getMessage();
        } catch (Exception $e) {
            $app->response()->status(404);
        }
    } else {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = "captchaerror";
    }
    $json = json_encode((array) $resDto);
    $json = str_replace("\u0000ResponseDto\u0000", "", $json);
    echo $json;
});



$app->post('/login', function () use ($app) {
    $request = Slim::getInstance()->request();
    $login = json_decode($request->getBody());
    $resDto = new ResponseDto();
    try {
        $username = validateInput($login->username, 'username');
        $password = validateInput($login->password, 'password');
        //$password = $login->password;

        $user = R::findOne('users', 'username=:username', array(':username' => $username));


        if ($user && (crypt($password, $user->password) == $user->password)) {
            session_regenerate_id(true);

            $_SESSION['logged_in'] = true; //set you've logged in
            $_SESSION['LAST_ACTIVITY'] = time(); //your last activity was now, having logged in.

            $_SESSION['UserID'] = $user->username;
            $_SESSION['LoginID'] = $user->username;
            $_SESSION['ids'] = $user->id;
            $_SESSION['LOGIN_STATUS'] = 'loggedIn';
            $_SESSION['FirstName'] = $user->firstname;
            $_SESSION['LastName'] = $user->lastname;

            if ($user->adminverified == 'Y' and $user->enabled == 1 and $user->email_verified) {
                $resDto->status = TRUE;
                $resDto->errorcode = 0;
                $sql = "SELECT role FROM roles
                      JOIN userroles ON roles.roleid = userroles.roleid
                      JOIN users ON userroles.userid = users.id WHERE username = :username";

                $row = R::getCell($sql, array(':username' => $username));
                $resDto->firstName = $user->firstname;
                $resDto->lastName = $user->lastname;
                $resDto->gender = strtolower($user->gender);

                if ($row == 'employee') {
                    $resDto->target = "employeehome";
                    $_SESSION['LOGIN_EMP'] = 'employeeRole';
                    $resDto->role = "employee";
                } elseif ($row == 'employer') {
                    $resDto->target = "employerhome";
                    $_SESSION['LOGIN_EMPR'] = 'employerRole';
                    $resDto->role = "employer";
                } elseif ($row == 'admin') {
                    $resDto->target = "adminhome";
                    $resDto->role = "admin";
                    $_SESSION['LOGIN_ADM'] = 'adminRole';
                }

                $projectnameId = get_rand_id(25);
                $projectnameKey = get_rand_id(33);
                $xsrf = get_rand_id(27);
                $_SESSION['projectnameId'] = $projectnameId;
                $_SESSION['projectnameKey'] = $projectnameKey;
                $_SESSION['xsrf'] = $xsrf;


                Slim::getInstance()->setEncryptedCookie('projectnameId', $projectnameId, '60 minutes');
                Slim::getInstance()->setEncryptedCookie('projectnameKey', $projectnameKey, '60 minutes');
                Slim::getInstance()->setCookie('XSRF-TOKEN', $xsrf, '60 minutes');
                Slim::getInstance()->setCookie('homeroleuser', $resDto->target, '60 minutes');
            } else {
                $resDto->status = FALSE;
                $resDto->errorcode = 1;
                $resDto->errorMessage = "activate";
            }
        } else {
            $resDto->status = FALSE;
            $resDto->errorcode = 1;
        }
        $db = null;
    } catch (PDOException $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
    } catch (Exception $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorcode = $e->getMessage();
    } catch (Exception $e) {
        $app->response()->status(404);
    }
    $json = json_encode((array) $resDto);
    $json = str_replace("\u0000ResponseDto\u0000", "", $json);
    echo $json;
});



$app->get('/logout', function() use ($app) {

    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time() - 1000);
            setcookie($name, '', time() - 1000, '/');
        }
    }

    $resDto = new ResponseDto();
    unset($_SESSION['UserID']);
    unset($_SESSION['EmailAddress']);
    unset($_SESSION['LoginID']);
    unset($_SESSION['GoldMember']);
    unset($_SESSION['LOGIN_STATUS']);


    session_destroy();


    $resDto->status = TRUE;
    $resDto->errorcode = 0;
    $resDto->target = "login";
    $json = json_encode((array) $resDto);
    $json = str_replace("\u0000ResponseDto\u0000", "", $json);

    echo $json;
});

$app->POST('/user', 'authenticate', function () use ($app) {
    $userId = $_SESSION['UserID'];
    try {
        $user = R::findOne('users', 'UserID=:userId', array(':userId' => $userId));
        if ($user) {
            echo $user;
        } else {
            echo "error";
        }
        $db = null;
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});

$app->get('/isUserLoggedIn', 'authenticateEmployee', function () use ($app) {
    $resDto = new ResponseDto();
    if ((isset($_SESSION['ids'])) && isset($_SESSION['LOGIN_STATUS'])) {
        $resDto->status = true;
        $resDto->errorcode = 0;
        $resDto->target = "home";
    } else {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->target = "login";
    }
    $json = json_encode((array) $resDto);
    $json = str_replace("\u0000ResponseDto\u0000", "", $json);

    echo $json;
});
?>
