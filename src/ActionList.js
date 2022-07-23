const Action = require('./Action')
const Entity = require('./Entity')

class ActionList extends Action {
  show_html (res, callback) {
    Entity.list(this.params,
      (err, entities) => {
        if (err) { return callback(err) }

        const content = entities.map(entity => entity.showTeaser(this.params)).join('')


        callback(null, { content })
      }
    )
  }
}

Action.classes.list = ActionList
