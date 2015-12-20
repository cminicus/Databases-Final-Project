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
    var currentUser = {};

    return {
      login: function(user, callback) {
        var cb = callback || angular.noop;
        var deferred = $q.defer();

        $http.get('/api/login/' + user.username + '/' + user.password)
        .success(function(data) {
          currentUser = data;
          deferred.resolve(data);
          return cb();
        })
        .error(function(error) {
          this.logOut();
          deferred.reject(error);
          return cb(error);
        }.bind(this));

        return deferred.promise;
      },

      signUp: function(user, callback) {
        var cb = callback || angular.noop;
        var deferred = $q.defer();

        $http.get('/api/createuser/' + user.username + '/' + user.password)
        .success(function(data) {
          currentUser = data;
          deferred.resolve(data);
          return cb();
        })
        .error(function(error) {
          this.logOut();
          deferred.reject(error);
          return cb(error);
        }.bind(this));

        return deferred.promise;
      },

      getCurrentUser: function() {
        return currentUser;
      },

      logOut: function() {{}
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
