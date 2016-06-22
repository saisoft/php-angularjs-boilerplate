'use strict';
loginApp.factory("commonService", ["$http", "$q", "$rootScope", "constantValues", "$modal", "$translate", function ($http, $q, $rootScope, constantValues, $modal, $translate) {
        var userName = null;
        var selectedLang = 'en';
        var userRole = null;
        var screenName = null;
        var nwerrshown = 0;

        return {
            setLanguage: setLanguage,
            getLanguage: getLanguage,
            showAlert: showAlert,
            showNetworkErrorAlert: showNetworkErrorAlert,
            toggleProcessing: toggleProcessing
        };





        function setLanguage(language) {
            selectedLang = language;
        }
        ;

        function getLanguage() {
            return selectedLang;
        }
        ;



        function showAlert(msg) {
            toggleProcessing(false);
            var modalInstance = $modal.open({
                templateUrl: constantValues.HTML_LOC + 'angularviews/alertMessageModal.html',
                controller: 'alertMessageCtrl',
                resolve: {
                    items: function () {
                        return msg;
                    }
                }
            });
            modalInstance.result.then(function (selectedItem) {
                $scope.selected = selectedItem;
            }, function () {
                //$log.info('Modal dismissed at: ' + new Date());
            });
        }
        ;

        function showNetworkErrorAlert() {
            if (nwerrshown == 0) {
                var modalInstance = $modal.open({
                    templateUrl: constantValues.HTML_LOC + 'angularviews/alertMessageModal.html',
                    controller: 'alertMessageCtrl',
                    resolve: {
                        items: function () {
                            return $translate('NEWORK_ERROR');
                        }
                    }
                });
                nwerrshown = 1;
            }
            modalInstance.result.then(function (selectedItem) {
                $scope.selected = selectedItem;
            }, function () {
                //$log.info('Modal dismissed at: ' + new Date());
            });
        }
        ;



        function toggleProcessing(show) {
            if (show) {
                $(".overlay").show();
            }
            if (!show) {
                $(".overlay").hide();
            }
        }
        ;
    }]);
