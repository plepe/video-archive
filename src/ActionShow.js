const Action = require('./Action')

module.exports = class ActionShow extends Action {
  show () {
    return 'SHOW for ' + this.id + ': ' + this.entity.data.title
  }
}
