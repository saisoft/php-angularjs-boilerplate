<?php
session_start();

if (isset($_SESSION['LOGIN_STATUS'])) {
    if (isset($_SESSION['LOGIN_EMP'])) {
        header('Location: employeehome');
    } else if (isset($_SESSION['LOGIN_EMPR'])) {
        header('Location: employerhome');
    } else if (isset($_SESSION['LOGIN_ADM'])) {
        header('Location: adminhome');
    }
}
?>
<!DOCTYPE html>
<html ng-app="loginApp">


    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="utf-8">
        <title>projectname - Login</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes"> 
        <!-- css for dev -->
        <link href="css/loading-bar.min.css" rel="stylesheet" type="text/css" />
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/custom.css" rel="stylesheet" type="text/css"/>
        <!-- css dev ends -->

        <!--css for prod
        <link href="dist/css/style.min.css" rel="stylesheet" type="text/css"/>
        -->
    </head>
    <body style="background:#F7F7F7;">
        <div id="wrapper">



            <div ng-view></div>

            <div class="overlay">
                <div>
                </div>
            </div>
        </div>
        
        <!-- dev js start -->
        <script src="js/jquery.min.js"></script>
        <script src="js/angular.min.js"></script>
        <script src="js/angular-route.min.js"></script>
        <script src="js/ui-boostrap-0.9.0.min.js"></script>
        <script src="js/angular-cookies.min.js"></script>
        <script src="js/angular-locale_de-de.js"></script>
        <script src="js/angular-translate.min.js"></script>
        <script src="js/angular-translate-loader-static-files.min.js"></script>
        <script src="js/loading-bar.min.js"></script>
        <script src="js/login/loginapp.js"></script>
        <script src="js/login/common/loginconstants.js"></script>
        <script src="js/login/services/commonService.js"></script>
        <script src="js/login/controllers/loginController.js"></script>
        <script src="js/login/services/loginService.js"></script>
        <script src="js/angular-google-adsense.js" type="text/javascript"></script>
        <!--dev js end -->

        <!-- prod js start
        <script src="dist/js/common.js"></script>
         <script src="dist/js/login/app.js"></script>
        <script src="dist/js/login/common/constants.js"></script>
        <script src="dist/js/login/controllers/controller.js"></script>
        <script src="dist/js/login/services/services.js"></script>
        -->
    </body>
    
</html>
