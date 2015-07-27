logsApp = angular.module('logsApp', [])
logsApp.controller 'logsCtrl', ($scope, $http) ->
  $http.get('get-data.php').success (data) ->
    $scope.logs = data
    return
  return