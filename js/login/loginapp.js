/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

'use strict';

var loginApp = angular.module("loginApp", ['pascalprecht.translate',
    'ui.bootstrap',
    'ngCookies',
    'chieffancypants.loadingBar',
    'ngRoute',
'angular-google-adsense']);

loginApp.directive('ngBlur', function () {
    return function (scope, elem, attrs) {
        elem.bind('blur', function () {
            scope.$apply(attrs.ngBlur);
        });
    };
});

loginApp.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if (event.which === 13) {
                scope.$apply(function () {
                    scope.$eval(attrs.ngEnter, {'event': event});
                });

                event.preventDefault();
            }
        });
    };
});

loginApp.config(['$routeProvider', 'constantValues', function ($routeProvider, constantValues) {
        $routeProvider.when('/login', {
            templateUrl: constantValues.HTML_LOC + 'angularviews/login.html',
            controller: 'loginCtrl',
            pageContext: 'Login'
        });
        $routeProvider.when('/signup/:type', {
            templateUrl: constantValues.HTML_LOC + 'angularviews/signup.html',
            controller: 'signupCtrl',
            pageContext: 'Sign Up'
        });
        $routeProvider.when('/forgotcreds', {
            templateUrl: constantValues.HTML_LOC + 'angularviews/forgotCreds.html',
            controller: 'loginCtrl',
            pageContext: 'Forgot Credentials'
        });

        $routeProvider.otherwise({
            redirectTo: "/login"
        });
    }]);

loginApp.config(['$translateProvider', function ($translateProvider) {
        $translateProvider.useStaticFilesLoader({
            prefix: 'js/login/properties/Login_Messages_',
            //prefix: 'dist/js/login/properties/Login_Messages_',
            suffix: '.json'
        });
        $translateProvider.preferredLanguage('en');
    }]);

loginApp.run(['$rootScope', function ($root) {
        $root.$on('$routeChangeStart', function (e, curr, prev) {
            if (curr.$$route && curr.$$route.resolve) {
                // Show a loading message until promises are not resolved
                $(".overlay").show();
                $root.loadingView = true;
            }
        });
        $root.$on('$routeChangeSuccess', function (e, curr, prev) {
            // Hide loading message
            $(".overlay").hide();
            $root.loadingView = false;
        });
    }]);

