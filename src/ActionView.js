const Action = require('./Action')
const Entity = require('./Entity')

class ActionView extends Action {
  show_html (res, callback) {
    if (!this.entity) {
      return callback(null, null)
    }

    callback(null, {
      content: this.entity.showFull(this.params)
    })
  }
}

Action.classes.view = ActionView
