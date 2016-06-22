/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

'use strict';

'use strict';

var myApp = angular.module("myApp", ['pascalprecht.translate',
    'ui.bootstrap',
    'ngCookies',
    'chieffancypants.loadingBar',
    'ngRoute',
    'ngSlider',
    'angucomplete-ie8',
'angular-google-adsense']);

myApp.directive("sort", function () {
    return {
        restrict: 'A',
        transclude: true,
        template:
                '<a ng-click="onClick()">' +
                '<span ng-transclude></span>' +
                '<i class="fa" ng-class="{\'fa-arrow-down\' : order === by && !reverse,  \'fa-arrow-up\' : order===by && reverse}"></i>' +
                '</a>',
        scope: {
            order: '=',
            by: '=',
            reverse: '='
        },
        link: function (scope, element, attrs) {
            scope.onClick = function () {
                if (scope.order === scope.by) {
                    scope.reverse = !scope.reverse;
                } else {
                    scope.by = scope.order;
                    scope.reverse = false;
                }
            };
        }
    };
});

myApp.directive('toggleButton', function () {
    return {
        require: 'ngModel',
        scope: {
            activeText: '@activeText',
            inactiveText: '@inactiveText',
            lightState: '=ngModel'
        },
        replace: true,
        transclude: true,
        template: '<div>' +
                '<span ng-transclude></span> ' +
                '<input style="border-radius: 4px 0px 0px 4px;" type="button" class="btn " ng-class="{\'btn-primary\': state.value}" value="{{activeText}}" ng-click="state.toggle()"/>' +
                '<input style="border-radius: 0px 4px 4px 0px;" type="button" class="btn" ng-class="{\'btn-primary\': !state.value}" value="{{inactiveText}}" ng-click="state.toggle()">' +
                '</div>',
        link: function postLink(scope) {
            scope.lightState = scope.inactiveText;

            scope.state = {
                value: false,
                toggle: function () {
                    this.value = !this.value;
                    scope.lightState = this.value ? scope.activeText : scope.inactiveText;
                }
            };
        }
    };
});

myApp.directive('jqdatepicker', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
            var updateModel = function (dateText) {
                scope.$apply(function () {
                    ngModelCtrl.$setViewValue(dateText);
                });
            };
            element.datepicker({
                changeYear: true,
                changeMonth: true,
                maxDate: new Date(),
                yearRange: '1920:2012',
                dateFormat: "yy-mm-dd",
                onSelect: function (dateText) {
                    updateModel(dateText);
                },
                beforeShow: function () {
                    setTimeout(function () {
                        $('.ui-datepicker').css({'z-index': 99999999999999, 'width': '222px'});
                    }, 0);
                }
            });
        }
    };
});

myApp.directive('ngEnter', function () {
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



myApp.directive('ngBlur', [function () {
        return function (scope, elem, attrs) {
            elem.bind('blur', function () {
                scope.$apply(attrs.ngBlur);
            });
        };
    }]);
/*
 This directive allows us to pass a function in on an enter key to do what we want.
 */
myApp.directive('ngEnter', [function () {
        return function (scope, element, attrs) {
            element.bind("keydown keypress", function (event) {
                if (event.which === 13) {
                    scope.$apply(function () {
                        scope.$eval(attrs.ngEnter);
                    });

                    event.preventDefault();
                }
            });
        };
    }]);

myApp.directive('autoComplete', ['$timeout', function ($timeout) {
        return function (scope, iElement, iAttrs) {
            iElement.autocomplete({
                source: scope[iAttrs.uiItems],
                select: function () {
                    $timeout(function () {
                        iElement.trigger('input');
                    }, 0);
                }
            });
        };
    }]);

myApp.config(['$routeProvider', 'constantValues', '$locationProvider', function ($routeProvider, constantValues, $locationProvider) {
        $routeProvider.when('/home', {
            templateUrl: constantValues.HTML_LOC + 'angularviews/home.html',
            controller: 'homePageCtrl',
            pageContext: 'Home',
            resolve: {
                dashboardData: ['homeService', 'commonService', function (homeService, commonService) {
                        commonService.toggleProcessing(true);
                        return homeService.userDashboard();
                    }]
            }
        });

        $routeProvider.when('/users', {
            templateUrl: constantValues.HTML_LOC + 'angularviews/manageUsers.html',
            controller: 'manageUsersCtrl',
            pageContext: 'ManageUsers',
            resolve: {
                allUsers: ['userService', function (userService) {
                        return userService.getAllUsers();
                    }]
            }
        });

        $routeProvider.when('/notifications', {
            templateUrl: constantValues.HTML_LOC + 'angularviews/notifications.html',
            controller: 'notificationCtrl',
            pageContext: 'ManageUsers',
            resolve: {
                notificationsData: ['notificationsService', 'commonService', function (notificationsService, commonService) {
                        commonService.toggleProcessing(true);
                        return notificationsService.getNotifications();
                    }]
            }
        });

        $routeProvider.when('/editProfile', {
            templateUrl: constantValues.HTML_LOC + 'angularviews/editProfile.html',
            controller: 'userCtrl',
            pageContext: 'EditProfile',
            resolve: {
                userData: ['userService', function (userService) {
                        return userService.getUserDetails();
                    }]
            }
        });

        $routeProvider.when('/changePassword', {
            templateUrl: constantValues.HTML_LOC + 'angularviews/changePassword.html',
            controller: 'changePasswordCtrl',
            pageContext: 'ChangePassword'
        });


        $routeProvider.otherwise({
            redirectTo: "/home"

        });

        // use the HTML5 History API
        $locationProvider.html5Mode(true);
    }]);

myApp.config(['$translateProvider', function ($translateProvider) {
        $translateProvider.useStaticFilesLoader({
            prefix: 'js/properties/locale-',
            //prefix: 'dist/js/properties/locale-',
            suffix: '.json'
        });
        $translateProvider.preferredLanguage('en');
    }]);

myApp.run(['$rootScope', '$location', function ($rootScope, $location) {
        $rootScope.$on('$routeChangeStart', function (e, curr, prev) {
            if (curr.$$route && curr.$$route.resolve) {
                // Show a loading message until promises are not resolved
                $(".overlay").show();
                $rootScope.loadingView = true;
            }
        });
        $rootScope.$on('$routeChangeSuccess', function (e, curr, prev) {
            // Hide loading message
            $(".overlay").hide();
            $rootScope.loadingView = false;

            setTimeout(function () {
                $('.current-page').parent().css('display', 'block').parent().addClass('active');
            }, 500);

            setTimeout(function () {

                $('.panel_toolbox .fa-expand').click(function () {
                    $('.maximus').parent().css({"position": "fixed", "padding": "4px", "background-color": "#F7F7F7", "top": 0, "left": 0, "z-index": 9999});
                    $('.panel_toolbox .fa-expand').hide();
                    $('.panel_toolbox .fa-compress').show();
                    $('.maximus').css('height', $(window).height());
                    //$('.ps-container').css({"overflow-x":"hidden", "overflow-y":"scroll"});

                });

                $('.panel_toolbox .fa-compress').click(function () {
                    $('.maximus').parent().css({"position": "relative", "height": "inherit", "top": 0, "left": 0, "z-index": 9});
                    $('.panel_toolbox .fa-expand').show();
                    $('.panel_toolbox .fa-compress').hide();
                    $('.maximus').css('height', $(window).height() - 100);
                    //$('.maximus').perfectScrollbar();
                });

                $(window).resize(function () {
                    $('.maximus').css('height', $(window).height() - 100);
                    //$('.x_panel').css({'max-height':$(window).height() - 190, 'overflow':'auto'});
                });

                $(window).trigger('resize');
                $('.maximus').perfectScrollbar({useBothWheelAxes: false});
                $('.bar_tabs li').removeClass('active');
                $("a:contains('" + $location.path().substring($location.path().lastIndexOf('/') + 2, $location.path().length) + "')").parent().
                        addClass('active');
                $rootScope.currentPath = $location.path().substring(0, $location.path().lastIndexOf('/') - 1);
            }, 544);
        });
    }]);
