const Action = require('./Action')
const Entity = require('./Entity')

class ActionView extends Action {
  show (params, query, callback) {
    Entity.get(params.id,
      (err, entity) => {
        if (err) { return callback(err) }

        if (!entity) {
          return callback(null, null)
        }

        callback(null, {
          content: this.entity.showFull(params)
        })
      }
    )
  }
}

Action.classes.view = ActionView
