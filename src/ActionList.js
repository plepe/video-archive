const Action = require('./Action')
const Entity = require('./Entity')

class ActionList extends Action {
  show (params, query, callback) {
    Entity.list(params,
      (err, entities) => {
        if (err) { return callback(err) }

        const content = entities.map(entity => entity.showTeaser(query)).join('')

        callback(null, { content })
      }
    )
  }
}

Action.classes.list = ActionList
