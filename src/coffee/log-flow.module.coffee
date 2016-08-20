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
      row.logdate = @.domHost.smartDate(row.logdate * 1000)

    @data = data
    @lastUpdate = Date.now()

  sourceChanged: (value)->
    if value?
      @$$('iron-ajax').url = value

  update: ->
    @$$('iron-ajax').generateRequest()

  computeFilter: (string) ->
    if !string
      null
    else
      string = string.toLowerCase()
      return (row)->
        message = row.message.toLowerCase();
        message.indexOf(string) != -1