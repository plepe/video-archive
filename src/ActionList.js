const Action = require('./Action')
const Entity = require('./Entity')

class ActionList extends Action {
  load (callback) {
    Entity.list(this.params,
      (err, entities) => {
        this.entities = entities
        callback(err)
      }
    )
  }

  show_html (res, callback) {
    const content = this.entities.map(entity => entity.showTeaser(this.params)).join('')
    callback(null, { content })
  }

  show_json (res, callback) {
    const result = this.entities.map(entity => entity.data)
    callback(null, result)
  }
}

Action.classes.list = ActionList
