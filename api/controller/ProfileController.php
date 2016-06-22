<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$app->get('/avtar', function () use ($app) {

    $im = imagecreatetruecolor(25, 25);
    $bg = imagecolorallocate($im, 0, 0, 0); //background color blue
    $fg = imagecolorallocate($im, 26, 187, 156); //text color white
    imagefill($im, 10, 10, $bg);
    imagestring($im, 5, 4, 4, strtoupper($_SESSION['FirstName'][0] . $_SESSION['LastName'][0]), $fg);
    header("Cache-Control: no-cache, must-revalidate");
    header('Content-type: image/png');
    imagepng($im);
    imagedestroy($im);
});

$app->get('/users/:page/:limit', 'authenticateEmployee', function ($page, $limit) use ($app) {
    $resDto = new ResponseDto();

    try {
        $username = $_SESSION['LoginID'];
        // query database for single Doctor
        $user = R::getRow('select id, username, language, firstname, lastname, contactno, address, city, pincode from users where username=:username', array(':username' => $username));

        if ($user) {
            // if found, return JSON response
            $app->response()->header('Content-Type', 'application/json');
            $json = json_encode($user);
            $json = underscoreToCamelCase($json, 1);
            echo $json;
        } else {
            // else throw exception
            throw new ResourceNotFoundException();
        }
    } catch (ResourceNotFoundException $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    } catch (Exception $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    }
});


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$app->get('/usersData', 'authenticateEmployer', function () use ($app) {
    $resDto = new ResponseDto();

    try {
        // query database for single Doctor
        $user = R::getAll('select username from users u join userroles ur on u.id = ur.userid join roles r on r.roleid = ur.roleid where 
                 r.role = \'employee\'');

        if ($user) {
            // if found, return JSON response
            $app->response()->header('Content-Type', 'application/json');
            $json = json_encode($user);
            $json = underscoreToCamelCase($json, 1);
            echo $json;
        } else {
            // else throw exception
            throw new ResourceNotFoundException();
        }
    } catch (ResourceNotFoundException $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    } catch (Exception $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    }
});


$app->put('/edit/:id', 'authenticateEmployee', function ($id) use ($app) {
    $resDto = new ResponseDto();
    try {
        $request = Slim::getInstance()->request();
        $userData = json_decode($request->getBody());
        $user = R::findOne('users', 'id=?', array($id));
        if ($user) {
            $user->contactno = validateInput($userData->contactno, 'contactno');
            $user->address = validateInput($userData->address, 'address');
            $user->city = validateInput($userData->city, 'ttcname');
            $user->pincode = validateInput($userData->pincode, 'pincode');

            R::store($user);
            $resDto->status = TRUE;

            echo upDateResponseDto($resDto);
            $resDto = null;
        } else {
            throw new ResourceNotFoundException();
        }
    } catch (ResourceNotFoundException $e) {
        // return 404 server error
        $app->response()->status(404);
    } catch (PDOException $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorcode = $e->getMessage();
    } catch (Exception $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorcode = $e->getMessage();
    }
});

$app->get('/allUsers/:page/:limit', 'authenticateAdmin', function ($page, $limit) use ($app) {
    $resDto = new ResponseDto();

    try {

        $user = R::getAll('SELECT u.id, u.username, u.enabled, u.language, u.firstname, u.lastname, u.gender, r.role FROM users u join userroles ur on u.id = ur.userid join roles r on ur.roleid = r.roleid where username!=:username', array(':username' => $_SESSION['LoginID']));

        if ($user) {
            // if found, return JSON response
            $app->response()->header('Content-Type', 'application/json');
            $json = json_encode($user);
            $json = underscoreToCamelCase($json, 1);
            echo $json;
        } else {
            // else throw exception
            throw new ResourceNotFoundException();
        }
    } catch (ResourceNotFoundException $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    } catch (Exception $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    }
});

$app->put('/deActivate/:id', 'authenticateAdmin', function ($id) use ($app) {
    $resDto = new ResponseDto();
    try {
        $request = Slim::getInstance()->request();
        $userData = json_decode($request->getBody());
        $user = R::findOne('users', 'id=?', array($id));

        if ($user) {
            $user->enabled = 0;
            $user->adminverified = 'N';
            R::store($user);
            $resDto->status = TRUE;

            echo upDateResponseDto($resDto);
            $resDto = null;
        } else {
            throw new ResourceNotFoundException();
        }
    } catch (ResourceNotFoundException $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    } catch (Exception $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    }
});

$app->put('/activate/:id', 'authenticateAdmin', function ($id) use ($app) {
    $resDto = new ResponseDto();
    try {
        $request = Slim::getInstance()->request();
        $userData = json_decode($request->getBody());
        $user = R::findOne('users', 'id=?', array($id));

        if ($user) {
            $user->enabled = 1;
            $user->adminverified = 'Y';
            R::store($user);
            $resDto->status = TRUE;

            echo upDateResponseDto($resDto);
            $resDto = null;
        } else {
            throw new ResourceNotFoundException();
        }
    } catch (ResourceNotFoundException $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    } catch (Exception $e) {
        $resDto->status = FALSE;
        $resDto->errorcode = 1;
        $resDto->errorMessage = $e->getMessage();
        echo upDateResponseDto($resDto);
        $resDto = null;
    }
});
?>
