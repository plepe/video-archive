const async = require('async')

const entityLoad = require('./entityLoad')

const cache = {}

class Entity {
  constructor (id, data) {
    this.id = id
    this.data = data
  }

  save (data, callback) {
    entityLoad.save(this.id, this.data.class, data,
      (err, data) => {
        if (err) { return callback(err) }

        this.data = data

        callback(null)
      }
    )
  }

  reload (callback) {
    entityLoad.get(this.id, (err, data) => {
      if (err) { return callback(err) }

      this.data = data

      callback(null)
    })
  }
}

Entity.classes = {}

Entity.get = function (id, callback) {
  if (id in cache) {
    return callback(null, cache[id])
  }

  entityLoad.get(id, (err, data) => {
    if (!data) {
      return callback(err, null)
    }

    if (!(data.class in Entity.classes)) {
      return callback(new Error('Invalid entity class ' + data.class))
    }

    const entity = new Entity.classes[data.class](id, data)

    cache[id] = entity

    callback(null, entity)
  })
}

Entity.list = function (options, callback) {
  entityLoad.list(options, (err, ids) => {
    if (err) { return callback(err) }

    async.map(ids,
      (id, done) => Entity.get(id, done),
      (err, result) => callback(err, result)
    )
  })
}

module.exports = Entity
