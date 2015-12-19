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
    $scope.errors = {};

    $scope.login = function(form) {
      $scope.submitted = true;

      if (form.$valid) {
        // check length for both
        Auth.login({
          username: $scope.user.username,
          password: $scope.user.password
        })
        .then( function() {
          // this does'nt actually work?? No promise being returned?
          console.log($location);
          $location.path('/');
        })
        .catch( function(err) {
          $scope.errors.other = err.message;
        });
      }
    };

    $scope.signUp = function(form) {
      $scope.submitted = true;

      if(form.$valid) {
        Auth.signUp({
          username: $scope.user.username,
          password: $scope.user.password
        })
        .then( function() {
          // Account created, redirect to home
          $location.path('/');
        })
        .catch( function(err) {
          err = err.data;
          $scope.errors = {};
        });
      }
    };
  });
