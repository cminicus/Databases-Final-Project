'use strict';

/**
 * @ngdoc function
 * @name deckApp.controller:NavbarCtrl
 * @description
 * # NavbarCtrl
 * Controller of the deckApp
 */
angular.module('hearthstoneApp')
  .controller('NavbarCtrl', function ($scope, $location) {
    $scope.menu = [{
      'title': 'Home',
      'link': '/'
    }, {
      'title': 'About',
      'link': '/about'
    }, {
      'title': 'Login',
      'link': '/login'
    }];

    $scope.isCollapsed = true;

    $scope.isActive = function(route) {
      return route === $location.path();
    };
  });
