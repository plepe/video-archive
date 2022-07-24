const Action = require('./Action')

class ActionView extends Action {
  show_html (res, callback) {
    if (!this.entity) {
      return callback(null, null)
    }

    callback(null, {
      content: this.entity.showFull(this.params)
    })
  }

  show_json (res, callback) {
    if (!this.entity) {
      return callback(null, null)
    }

    callback(null, this.entity.data)
  }
}

Action.classes.view = ActionView
