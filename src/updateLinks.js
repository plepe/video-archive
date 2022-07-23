const queryString = require('qs')

const state = require('./state')

module.exports = function updateLinks () {
  let links = document.getElementsByTagName('a')

  Array.from(links).forEach(link => {
    link.onclick = () => {
      const href = link.href.substr(location.origin.length + 1)
      let [path, params] = href.split('?')
      path = path.split(/\//g)
      params = queryString.parse(params)

      if (path.length > 1 && path[1] !== '') {
        params.id = path[1]
      }
      if (path.length > 0 && path[0] !== '') {
        params.action = path[0]
      }

      if (state.apply(params)) {
        return false
      }
    }
  })
}
