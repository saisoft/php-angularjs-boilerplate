<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$app->post('/changePassword', 'authenticateEmployee', function () use ($app) {
    $request = Slim::getInstance()->request();
    $changePass = json_decode($request->getBody());
    $resDto = new ResponseDto();
    try {
        $username = validateInput($changePass->username, 'username');
        $password = validateInput($changePass->password, 'password');
        $newpass = validateInput($changePass->newPassword, 'password');
        $confpass = validateInput($changePass->confirmPassword, 'password');

        $user = R::findOne('users', 'username=:username', array(':username' => $username));

        if ($user && (crypt($password, $user->password) == $user->password) && ($newpass == $confpass)) {

            $user->password = crypt($confpass);
            $userid = R::store($user);

            if ($user->enabled == 1) {
                $resDto->status = TRUE;
                $resDto->errorcode = 0;
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
    $json = json_encode((array) $resDto);
    $json = str_replace("\u0000ResponseDto\u0000", "", $json);

    echo $json;
});
?>
