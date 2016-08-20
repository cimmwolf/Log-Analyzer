Polymer
  is: 'log-app'
  properties:
    logs: Array

  ready: ->
    setInterval =>
      @$.cronRequest.generateRequest()
    , 60000

  handleResponse: ->
    names = @$.logsRequest.lastResponse
    @logs = []
    for name in names
      @push 'logs', {name: name, chartSrc: '/statistic.php?source=' + name, flowSrc: '/data.php?source=' + name}

  cronComplete: ->
    for element in @querySelectorAll('log-statistic')
      element.update()

    for element in @querySelectorAll('log-flow')
      element.update()