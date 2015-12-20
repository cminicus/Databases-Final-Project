'use strict';

/**
 * @ngdoc function
 * @name deckApp.controller:LoginCtrl
 * @description
 * # LoginCtrl
 * Controller of the deckApp
 */
angular.module('hearthstoneApp')
  .controller('LoginCtrl', function ($scope, Auth, $location) {
    $scope.stuff = ['Hey'];

    $scope.user = {};
    $scope.sError = {};
    $scope.uError = {};

    $scope.login = function(form) {
      $scope.submitted = true;

      if (form.$valid) {
        if ($scope.user.username == undefined ||
            $scope.user.password == undefined ||
            $scope.user.username.length == 0 ||
            $scope.user.password.length == 0) {
          $scope.uError = 'Please enter a username and password';
          $scope.userError = true;
          return;
        } else if ($scope.user.username.length > 15 || $scope.user.password.length > 20) {
          $scope.uError = 'Username must be 15 characters or less and password must 20 characters or less';
          $scope.userError = true;
          return;
        } else {
          $scope.userError = false;
        }

        Auth.login({
          username: $scope.user.username,
          password: $scope.user.password
        })
        .then(function(data) {
          $location.path('/');
        })
        .catch(function(error) {
          $scope.serverError = true;
          $scope.sError = error;
        });
      }
    };

    $scope.signUp = function(form) {
      $scope.submitted = true;

      if ($scope.user.username == undefined ||
          $scope.user.password == undefined ||
          $scope.user.username.length == 0 ||
          $scope.user.password.length == 0) {
        $scope.uError = 'Please enter a username and password';
        $scope.userError = true;
        return;
      } else if ($scope.user.username.length > 15 || $scope.user.password.length > 20) {
        $scope.uError = 'Username must be 15 characters or less and password must 20 characters or less';
        $scope.userError = true;
        return;
      } else {
        $scope.userError = false;
      }

      if(form.$valid) {
        Auth.signUp({
          username: $scope.user.username,
          password: $scope.user.password
        })
        .then(function(data) {
          $location.path('/');
        })
        .catch(function(error) {
          $scope.serverError = true;
          $scope.sError = error;
        });
      }
    };
  });
