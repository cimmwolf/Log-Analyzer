Polymer
  is: 'log-flow-message'
  properties:
    message: String
  ready: ->
    @$.message.innerHTML = @message
    .replace(
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
      /"?((\/[a-z0-9_.-]*?){3,}\/([a-z0-9_.-]+\/?))"?(:?)/gi,
      '<span class="text-nowrap" title="$1">(..)/$3</span>$4')
