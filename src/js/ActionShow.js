const Action = require('./Action')

module.exports = class ActionShow extends Action {
  show () {
    return this.entity.showFull(this.params)
  }
}
