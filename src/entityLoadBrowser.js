const queryString = require('qs')

module.exports = {
  get (id, callback) {
    fetch('/view/' + id,
      {
        headers: {
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => callback(null, data))
      .catch(reason => global.setTimeout(() => callback(reason), 0))
  },

  list (options, callback) {
    fetch('/ids?' + queryString.stringify(options),
      {
        headers: {
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => callback(null, data))
      .catch(reason => global.setTimeout(() => callback(reason), 0))
  }
}
