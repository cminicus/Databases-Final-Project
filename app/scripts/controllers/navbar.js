'use strict';

/**
 * @ngdoc function
 * @name deckApp.controller:NavbarCtrl
 * @description
 * # NavbarCtrl
 * Controller of the deckApp
 */
angular.module('hearthstoneApp')
  .controller('NavbarCtrl', function ($scope, $location, Auth) {
    $scope.menu = [{
      'title': 'Home',
      'link': '/'
    }, {
      'title': 'About',
      'link': '/about'
    }];

    $scope.isCollapsed = true;
    $scope.isLoggedIn = Auth.isLoggedIn;
    $scope.getCurrentUser = Auth.getCurrentUser;

    $scope.isActive = function(route) {
      return route === $location.path();
    };

    $scope.logOut = function() {
      Auth.logOut();
      $location.path('/login');
    };
  });
