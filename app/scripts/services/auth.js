'use strict';

/**
 * @ngdoc service
 * @name deckApp.Auth
 * @description
 * # Auth
 * Service in the deckApp.
 */
angular.module('hearthstoneApp')
  .service('Auth', function Auth($q, $location) {
    // AngularJS will instantiate a singleton by calling "new" on this function
    var currentUser = {};

    return {
      login: function(user) {
        var deffered = $q.defer();
        // make call to php script and save currentUser
        console.log("login");
        currentUser = {
          'username': 'Clayton',
          'password': 'yo',
          'userID': 2
        }
        $location.path('/');
        return deffered.promise;
      },

      signUp: function(user) {
        var deffered = $q.defer();
        // again make call to php script and save currentUser
        console.log("signup");
        currentUser = {
          'username': 'Tyler',
          'password': 'yo',
          'userID': 1
        }
        $location.path('/');
        return deffered.promise;
      },

      getCurrentUser: function() {
        return currentUser;
      },

      logOut: function() {
        currentUser = {};
      },

      isLoggedIn: function() {
        return currentUser.hasOwnProperty('username');
      },

      isLoggedInAsync: function(callback) {
        if (currentUser.hasOwnProperty('username')) {
          callback(true);
        } else {
          callback(false);
        }
      }
    };
  });
