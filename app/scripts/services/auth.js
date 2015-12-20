'use strict';

/**
 * @ngdoc service
 * @name deckApp.Auth
 * @description
 * # Auth
 * Service in the deckApp.
 */
angular.module('hearthstoneApp')
  .service('Auth', function Auth($q, $location, $http) {
    // AngularJS will instantiate a singleton by calling "new" on this function
    var currentUser = {};

    return {
      login: function(user) {
        var deffered = $q.defer();
        // make call to php script and save currentUser
        console.log("login");
        currentUser = {
          'username': 'Clayton',
          'userID': 2
        }
        $location.path('/');
        return deffered.promise;
      },

      signUp: function(user, callback) {
        var cb = callback || angular.noop;
        var deferred = $q.defer();

        $http.get('/api/createuser/' + user.username + '/' + user.password)
        .success(function(data) {
          console.log(data);
          currentUser = data;
          deferred.resolve(data);
          $location.path('/');
          return cb();
        })
        .error(function(error) {
          console.log(error);
          this.logOut();
          deferred.reject(error);
          return cb(error);
        }.bind(this));

        return deferred.promise;
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
