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

  fetch('api.php?id=' + id)
    .then(res => res.json())
    .then(data => {
      const entity = new Entity.classes[data.type](id, data)

      cache[id] = entity

      callback(null, entity)
    })
}

module.exports = Entity
