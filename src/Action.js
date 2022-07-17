const Action = require('./Action')

module.exports = class Action {
  constructor (params) {
    this.params = params
    if (params.id) {
      this.id = params.id
      //this.entity = Entity.get(this.id)
    }
  }

  show () {
  }
}
