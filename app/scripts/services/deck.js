'use strict';

/**
 * @ngdoc service
 * @name deckApp.Deck
 * @description
 * # Deck
 * Service in the deckApp.
 */
angular.module('hearthstoneApp')
  .service('Deck', function Deck() {
    // AngularJS will instantiate a singleton by calling "new" on this function
    var currentDeck = {};

    return {
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
