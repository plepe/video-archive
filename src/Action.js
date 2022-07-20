const Entity = require('./Entity')

class Action {
  constructor (params) {
    this.params = params
    if (params.id) {
      this.id = params.id
      //this.entity = Entity.get(this.id)
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

  show () {
  }
}

Action.get = function (id, params, callback) {
  const action = new Action.classes[id](params)
  action.load((err) => callback(err, action))
}

Action.classes = {}
module.exports = Action
