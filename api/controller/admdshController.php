<?php

$app->get('/dashboard', 'authenticateEmployee', function () use ($app) {
    $resDto = new ResponseDto();
    try {
        $empcount = R::getRow('SELECT count(role) as count FROM roles
                      JOIN userroles ON roles.roleid = userroles.roleid
                      JOIN users ON userroles.userid = users.id where role = \'employee\'');
        $emprcount = R::getRow('SELECT count(role) as count FROM roles
                      JOIN userroles ON roles.roleid = userroles.roleid
                      JOIN users ON userroles.userid = users.id where role = \'employer\'');
        $topiccount = R::getRow('SELECT count(id) as count FROM topic');

        $quescount = R::getRow('SELECT count(id) as count FROM question');

        $response = '{';
        if ($empcount) {
            $response = $response . '"empCount":' . json_encode($empcount['count']) . ',';
        } else {
            $response = $response . '"empCount": 0,';
        }

        if ($emprcount) {
            $response = $response . '"emprCount":' . json_encode($emprcount['count']) . ',';
        } else {
            $response = $response . '"emprCount": 0,';
        }

        if ($topiccount) {
            $response = $response . '"topicCount":' . json_encode($topiccount['count']) . ',';
        } else {
            $response = $response . '"topicCount": 0,';
        }



        if (isset($_SESSION['LOGIN_ADM'])) {
            // query database for single Doctor
            $catcount = R::getRow('SELECT count(id) as count FROM category');
            $testcount = R::getRow('SELECT count(id) as count FROM test');




            if ($catcount) {
                $response = $response . '"catCount":' . json_encode($catcount['count']) . ',';
            } else {
                $response = $response . '"catCount": 0,';
            }



            if ($testcount) {
                $response = $response . '"testCount":' . json_encode($testcount['count']) . ',';
            } else {
                $response = $response . '"testCount": 0,';
            }
        } else if (isset($_SESSION['LOGIN_EMPR'])) {

            $reqcount = R::getRow('SELECT count(id) as count FROM employeetest where active=\'Y\' and employerid=:empid', array(':empid' => $_SESSION['ids']));
            if ($reqcount) {
                $response = $response . '"reqCount":' . json_encode($reqcount['count']) . ',';
            } else {
                $response = $response . '"reqCount": 0,';
            }
            $completed = R::getRow('SELECT count(id) as count FROM employeetest where status=\'over\' and active=\'Y\' and employerid=:empid', array(':empid' => $_SESSION['ids']));
            if ($completed) {
                $response = $response . '"completed":' . json_encode($completed['count']) . ',';
            } else {
                $response = $response . '"completed": 0,';
            }
        } else if (isset($_SESSION['LOGIN_EMP'])) {
            $reqcount = R::getRow('SELECT count(id) as count FROM employeetest where active=\'Y\' and employeeid=:empid', array(':empid' => $_SESSION['ids']));
            if ($reqcount) {
                $response = $response . '"reqCount":' . json_encode($reqcount['count']) . ',';
            } else {
                $response = $response . '"reqCount": 0,';
            }
            $completed = R::getRow('SELECT count(id) as count FROM employeetest where status=\'over\' and active=\'Y\' and employeeid=:empid', array(':empid' => $_SESSION['ids']));
            if ($completed) {
                $response = $response . '"completed":' . json_encode($completed['count']) . ',';
            } else {
                $response = $response . '"completed": 0,';
            }
        }


        if ($quescount) {
            $response = $response . '"quesCount":' . json_encode($quescount['count']) . '}';
        } else {
            $response = $response . '"quesCount": 0}';
        }

        if ($response !== '') {
            // if found, return JSON response
            $app->response()->header('Content-Type', 'application/json');
            echo $response;
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
