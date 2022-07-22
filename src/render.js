const Action = require('./Action')

module.exports = function render (params, callback) {
  Action.get(params.action, params,
    (err, action) => {
      if (err) { return callback(err) }

      action.show(params, callback)
    }
  )
}
