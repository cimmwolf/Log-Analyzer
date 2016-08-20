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
      @.domHost.smartDate(lastUpdate)
    else
      return ''