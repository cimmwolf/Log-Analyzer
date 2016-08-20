Polymer
  is: 'log-statistic'

  properties:
    source:
      type: String
      observer: 'sourceChanged'
    lastUpdate: Number
    uMark:
      type: String
      computed: 'computeUMark(lastUpdate)'

  ready: ->
    setInterval =>
      if @lastUpdate?
        @lastUpdate++
    , 50000

  sourceChanged: (value)->
    if value?
      @$$('iron-ajax').url = value

  handleResponse: ->
    data = @$$('iron-ajax').lastResponse
    for row,i in data
      if i > 0
        row[0] = new Date(row[0] * 1000)

    @$$('google-chart').data = data

    @lastUpdate = Date.now()

  update: ->
    @$$('iron-ajax').generateRequest()

  computeUMark: (lastUpdate)->
    if (Date.now() - lastUpdate) > 60 * 4 * 1000
      @smartDate(lastUpdate)
    else
      return ''

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