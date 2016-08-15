Polymer
  is: 'log-app'
  properties:
    logs: Array
  handleResponse: ->
    names = @$$('iron-ajax').lastResponse
    @logs = []
    for name in names
      @push 'logs', {name: name, chartSrc: '/statistic.php?source=' + name, flowSrc: '/data.php?source=' + name}