Polymer
  is: 'log-flow'
  properties:
    data: Array
    source:
      type: String
      observer: 'sourceChanged'

  handleResponse: ->
    data = @$$('iron-ajax').lastResponse
    for row in data
      row.logdate = @smartDate(row.logdate * 1000)

    @data = data
    @lastUpdate = Date.now()
    @timeout()

  sourceChanged: (value)->
    if value?
      @$$('iron-ajax').url = value

  timeout: ->
    setTimeout =>
      @$$('iron-ajax').generateRequest()
    , 60000

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

  computeFilter: (string) ->
    if !string
      null
    else
      string = string.toLowerCase()
      return (row)->
        message = row.message.toLowerCase();
        message.indexOf(string) != -1