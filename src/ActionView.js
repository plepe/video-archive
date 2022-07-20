const Action = require('./Action')

class ActionView extends Action {
  show () {
    return this.entity.showFull(this.params)
  }
}

Action.classes.view = ActionView
