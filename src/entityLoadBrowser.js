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
  },

  save (id, _class, data, callback) {
    fetch('/api/' + id, {
      method: 'post',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data, null, 2)
    })
      .then(res => res.json())
      .then(data => callback(null, data))
      .catch(reason => global.setTimeout(() => callback(reason), 0))
  }
}
