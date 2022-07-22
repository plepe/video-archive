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
  }
}
