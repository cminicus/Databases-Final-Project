'use strict';

/**
 * @ngdoc function
 * @name hearthstoneApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the hearthstoneApp
 */
angular.module('hearthstoneApp')
  .controller('MainCtrl', function ($scope, Auth, Deck) {
    $scope.decks = [];
    $scope.loading = true;

    $scope.newDeck = {};
    $scope.newDeck.deckName = 'Default Deck';

    $scope.classOptions = [{
      'name': 'Neutral',
      'value': 'Neutral'
    }, {
      'name': 'Warrior',
      'value': 'HERO_01'
    }, {
      'name': 'Shaman',
      'value': 'HERO_02'
    }, {
      'name': 'Rogue',
      'value': 'HERO_03'
    }, {
      'name': 'Paladin',
      'value': 'HERO_04'
    }, {
      'name': 'Hunter',
      'value': 'HERO_05'
    }, {
      'name': 'Druid',
      'value': 'HERO_06'
    }, {
      'name': 'Warlock',
      'value': 'HERO_07'
    }, {
      'name': 'Mage',
      'value': 'HERO_08'
    }, {
      'name': 'Priest',
      'value': 'HERO_09'
    }];

    Deck.getUserDecks(Auth.getCurrentUser().userID)
    .then(function(data) {
      console.log(data);
      $scope.loading = false;
      $scope.decks = data;
    })
    .catch(function(error) {
      $scope.loading = false;
      console.log(error);
    });


    $scope.createNewDeck = function() {
      if (!$scope.newDeck.hasOwnProperty('deckClass')) {
        return;
      }
      Deck.createNewDeck(Auth.getCurrentUser().userID, $scope.newDeck.deckName, $scope.newDeck.deckClass)
      .then(function(data) {
        console.log(data);
      })
      .catch(function(error) {
        console.log(error);
      });
    };

    $scope.editDeck = function(deck) {
      console.log(deck);
    };

    $scope.deleteDeck = function(deck) {
      console.log(deck);
    }

  });
