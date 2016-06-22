
<?php
session_start();

if (isset($_SESSION['LOGIN_STATUS']) && empty($_SESSION['LOGIN_ADM'])) {
    header('Location: login');
}
?>
<!DOCTYPE html>
<html ng-app="myApp">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <base href="/php-mysql-dynamic-website/" target="_blank">
        <title>{{'TITLE'|translate}}</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes"> 

        <!-- css dev ends -->
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link href="css/loading-bar.min.css" rel="stylesheet" type="text/css" />
        <link href="css/custom.css" rel="stylesheet" type="text/css" />
        <link href="css/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />

        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/fullcalendar.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/angular-slider.min.css" rel="stylesheet" type="text/css"/>
        <link href="fullcalendar.print.css" rel="stylesheet" type="text/css" media='print'/>
        <link href="css/angucomplete-ie8.css" rel="stylesheet" type="text/css"/>
        <link href="css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>

        <!-- css dev ends -->

        <!--css for prod
        <link href="dist/css/style.min.css" rel="stylesheet" type="text/css"/>
        <link href="fullcalendar.print.css" rel="stylesheet" type="text/css" media='print'/>
        -->
    </head>

    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <div ng-include src="'angularviews/navigation/primary-navigator.html'"></div>
                <!--div ng-include src="'dist/angularviews/navigation/admin-navigator.html'"></div-->
                <div class="right_col" role="main">
                    <div class="">
                        <div ng-view></div>
                    </div>
                </div>
                <!-- footer content -->
                <footer>
                    <div class="pull-right">
                        Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->

                <div class="overlay">
                    <div>
                    </div>
                </div>
            </div>
        </div>


        <!-- dev js starts -->
        <script src="js/jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>
        <script src="js/angular.min.js"></script>
        <script src="js/angular-route.min.js"></script>
        <script src="js/ui-boostrap-0.9.0.min.js"></script>
        <script src="js/angular-cookies.min.js"></script>
        <script src="js/fastclick.js"></script>
        <!-- NProgress -->
        <script src="js/nprogress.js"></script>

        <!-- Custom Theme Scripts -->

        <script src="js/angular-translate.min.js"></script>
        <script src="js/angular-translate-loader-static-files.min.js"></script>
        <script src="js/loading-bar.min.js"></script>
        <script src="js/jPushMenu.js"></script>
        <script src="js/moment.min.js"></script>
        <script src="js/fullcalendar.min.js"></script>
        <script src="js/angular-slider.min.js"></script>
        <script src="js/angucomplete-ie8.js" type="text/javascript"></script>
        <script src="js/angular-google-adsense.js" type="text/javascript"></script>
        

        <script src="js/myApp.js"></script>
        <script src="js/common/appconstants.js"></script>
        <script src="js/perfect-scrollbar.jquery.min.js"></script>

        <script src="js/services/commonServiceApp.js"></script>
        <script src="js/controllers/commonController.js"></script>
        <script src="js/controllers/homePageController.js"></script>
        <script src="js/services/homeService.js"></script>
        <script src="js/services/searchService.js"></script>

        <script src="js/controllers/userController.js"></script>
        <script src="js/services/userService.js"></script>

        <script src="js/bootselect/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="js/timepicker.js"></script>
        <script src="js/custom.js"></script>
        <script src="js/controllers/notificationsController.js"></script>
        <script src="js/services/notificationsService.js"></script>


        <!-- dev js ends -->

        <!-- prod js start
         <script src="dist/js/common.js"></script>
                 <script src="dist/js/apps.js"></script>
        <script src="dist/js/common/constants.js"></script>
        <script src="dist/js/controllers/controller.js"></script>
        <script src="dist/js/services/services.js"></script>
        
        -->
        <script>
$(window).bind('click', function (event) {
    if (event.target.href)
        $(window).unbind('beforeunload');
});
$(window).bind('beforeunload', function (event) {
    $.ajax({url: "?logout&leave=yes", async: false});
});
        </script>

    </body>
</html>
