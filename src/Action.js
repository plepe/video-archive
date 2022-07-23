const Entity = require('./Entity')

class Action {
  constructor (params) {
    this.params = params
    if (params.id) {
      this.id = params.id
    }
  }

  load (callback) {
    if (!this.id) {
      return callback(null)
    }

    Entity.get(this.id, (err, entity) => {
      if (err) { return callback(err) }
      this.entity = entity
      callback(null)
    })
  }

  request_get (req, callback) {
    callback(null)
  }

  show_html (res, callback) {
    callback(null)
  }
}

Action.get = function (id, params, callback) {
  if (!(params.action in Action.classes)) {
    return callback(new Error('No such action ' + params.action))
  }

  const action = new Action.classes[params.action](params)
  action.load((err) => callback(err, action))
}

Action.classes = {}
module.exports = Action
