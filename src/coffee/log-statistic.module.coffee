Polymer
  is: 'log-statistic'

  properties:
    data:
      type: String
      observer: 'dataChanged'

  dataChanged: (value)->
    if value?
      @$$('iron-ajax').url = value

  handleResponse: ->
    data = @$$('iron-ajax').lastResponse
    for row,i in data
      if i > 0
        row[0] = moment(row[0]*1000).format('D MMM HH:mm')

    @$$('google-chart').data = data