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

  smartDate: (timestamp) ->
    diff = Date.now() - timestamp
    diff /= 1000

    if diff < 60
      return Math.floor(diff) + ' s. ago'

    if diff < 60 * 60
      return Math.floor(diff / 60) + ' min. ago'

    today = moment().startOf('day')
    yesterday = moment().startOf('day').subtract(1, 'days')
    timestamp = moment(timestamp)

    if timestamp.isSame(today, 'day')
      if diff < 60 * 60 * 1.5
        return '1 h. ago'
      if diff < 60 * 60 * 2
        return '1.5 h. ago'
      if diff < 60 * 60 * 2.5
        return '2 h. ago'
      if diff < 60 * 60 * 3
        return '2.5 h. ago'
      if diff < 60 * 60 * 3.5
        return '3 h. ago'
      return 'today at ' + timestamp.format('HH:mm')

    if timestamp.isSame(yesterday, 'day')
      return 'yesterday at ' + timestamp.format('HH:mm')

    return timestamp.format('D MMM, HH:mm')