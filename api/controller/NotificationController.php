<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$app->get('/notificationCount', 'authenticateEmployee', function () use ($app) {
    $resDto = new ResponseDto();
    try {
        $notifications = R::getCol('SELECT COUNT(*) AS count FROM notification where status=\'U\' and toid=:username', array(':username' => $_SESSION['ids']));

        if ($notifications) {
            // if found, return JSON response
            $app->response()->header('Content-Type', 'application/json');
            $json = json_encode($notifications);
            //$json = underscoreToCamelCase($json, 1);
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

$app->get('/notifications/:page/:limit', 'authenticateEmployee', function ($page, $limit) use ($app) {
    $resDto = new ResponseDto();
    try {
        // query database for single Doctor
        if (isset($_SESSION['LOGIN_EMP'])) {
            $notifications = R::getAll('select nt.status, nt.emptestid, nt.type, u.firstname, u.lastname, er.company, tp.topicname from notification nt join users u on u.id=nt.fromid join employer er on nt.fromid = er.employerid
join employeetest et on nt.emptestid = et.id join test tt on tt.id = et.testid join topic tp on tt.topicid = tp.id where et.active=\'Y\' and nt.toid=:username', array(':username' => $_SESSION['ids']));
        } else if (isset($_SESSION['LOGIN_EMPR']) || isset($_SESSION['LOGIN_ADM'])) {
            $notifications = R::getAll('select nt.status, nt.emptestid, nt.type, u.firstname, u.lastname, tp.topicname from notification nt join users u on u.id=nt.toid join employeetest et on nt.emptestid = et.id join test tt on tt.id = et.testid join topic tp on tt.topicid = tp.id where et.active=\'Y\' and nt.toid =:username', array(':username' => $_SESSION['ids']));
        }
        if ($notifications) {
            // if found, return JSON response
            $app->response()->header('Content-Type', 'application/json');
            $json = json_encode($notifications);
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

$app->get('/readNotification', 'authenticateEmployee', function () use ($app) {
    $resDto = new ResponseDto();
    try {
        // query database for single Doctor
        $notifications = R::exec('update notification set status=\'R\' where status=\'U\' and toid=:username', array(':username' => $_SESSION['ids']));

        if ($notifications) {
            // if found, return JSON response
            $app->response()->header('Content-Type', 'application/json');
            $json = json_encode($notifications);
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
?>
