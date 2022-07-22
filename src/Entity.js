const entityLoad = require('./entityLoad')
const cache = {}

class Entity {
  constructor (id, data) {
    this.id = id
    this.data = data
  }
}

Entity.classes = {}

Entity.get = function (id, callback) {
  if (id in cache) {
    return callback(null, cache[id])
  }

  entityLoad.get(id, (err, data) => {
    if (!data) {
      return callback(null, null)
    }

    if (!(data.class in Entity.classes)) {
      return callback(new Error('Invalid entity class ' + data.class))
    }

    const entity = new Entity.classes[data.class](id, data)

    cache[id] = entity

    callback(null, entity)
  })
}

module.exports = Entity
