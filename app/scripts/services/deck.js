'use strict';

/**
 * @ngdoc service
 * @name deckApp.Deck
 * @description
 * # Deck
 * Service in the deckApp.
 */
angular.module('hearthstoneApp')
  .service('Deck', function Deck($q, $http) {
    // AngularJS will instantiate a singleton by calling "new" on this function
    var currentDeck = {};

    return {
      getUserDecks: function(userID, callback) {
        var cb = callback || angular.noop;
        var deferred = $q.defer();

        $http.get('/api/getuserdecks/' + userID)
        .success(function(data) {
          deferred.resolve(data);
          return cb();
        })
        .error(function(error) {
          deferred.reject(error);
          return cb(error);
        }.bind(this));

        return deferred.promise;
      },

      createNewDeck: function(userID, deckName, heroID, callback) {
        var cb = callback || angular.noop;
        var deferred = $q.defer();

        $http.get('/api/createdeck/' + userID + '/' + deckName + '/' + heroID)
        .success(function(data) {
          console.log(data);
          deferred.resolve(data);
          return cb();
        })
        .error(function(error) {
          console.log(error);
          deferred.reject(error);
          return cb(error);
        }.bind(this));

        return deferred.promise;
      },

      getCurrentDeck: function() {
        return currentDeck;
      },

      // maybe have this so we don't allow us to go to "deck" pages
      hasCurrentDeckAsync: function(callback) {
        if (currentDeck.hasOwnProperty('deckID')) {
          callback(true);
        } else {
          callback(false);
        }
      }
    }
  });
