'use strict';

/**
 * @ngdoc function
 * @name deckApp.controller:DeckCtrl
 * @description
 * # DeckCtrl
 * Controller of the deckApp
 */
angular.module('hearthstoneApp')
  .controller('DeckCtrl', function ($scope, $http) {

    $scope.search = {};

    $scope.deck = {
      'deckName': 'My Deck'
    }

    $scope.manaOptions = ['All', 0, 1, 2, 3, 4, 5, 6, 7, 8];
    $scope.cardTypeOptions = ['All', 'Minion', 'Spell', 'Weapon'];
    $scope.cardClassOptions = [{
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

    $scope.manaLabels = ['0 Mana', '1 Mana', '2 Mana', '3 Mana', '4 Mana', '5 Mana', '6 Mana', '7 Mana', '8 Mana'];
    $scope.manaData = [[6, 10, 2, 8, 9, 10, 8, 1, 4]];
    $scope.manaSeries = ['Mana'];

    $scope.doughnutLabels = ['Cards Used', 'Remaining Cards'];
    $scope.doughnutData = [20, 10];

    $scope.radarLabels = ['Minions', 'Spells', 'Weapons'];
    $scope.radarData = [[20, 10, 15]];

    $scope.search = function(form) {
      console.log($scope.search);
    };

  });
