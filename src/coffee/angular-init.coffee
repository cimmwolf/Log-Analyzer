logsApp = angular.module('logsApp', ['ngSanitize'])
.filter 'pathCut', ->
  (message) ->
    message.replace(
      /(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/gi,
      (match) ->
        decodeURI(match)
    ).replace(
      /(GET )(.*?%.*?)( HTTP\/)/g,
      (match, p1, p2, p3) ->
        try
          decodedUri = decodeURIComponent(p2)
        catch e
          console.log(e.message + ':' + p2)
          decodedUri = p2

        p1 + decodedUri + p3
    ).replace(
      /([ "])((\/[a-z0-9_.-]*?){3,}\/([a-z0-9_.-]+))([ ":])/gi,
      '$1<span class="label label-info text-nowrap" title="$2"><span class="glyphicon glyphicon-file"></span> $4</span>$5')

logsApp.controller 'logsCtrl', ($scope, $http) ->
  $http.get('get-data.php').success (data) ->
    for log in data
      log.chartDataSource = '/statistic.php?source=' + log.name
      log.dataSource = '/data.php?source=' + log.name

    $scope.logs = data
    return
  return