const Action = require('./Action')

module.exports = function handleAction (method, req, res) {
  const params = req.query
  if (!params.action) {
    if (params.id) {
      params.action = 'view'
    } else {
      params.action = 'list'
    }
  }

  action = Action.get(params.action, params,
    (err, action) => {
      if (!action['request_' + method]) {
        res.status(500).send('Server Error')
        return console.error('Method ' + method + ' not allowed for action ' + req.query.action)
      }

      action['request_' + method](req,
        (err, result) => {
          if (err) {
            res.status(500).send('Server Error')
            return console.error(err)
          }

          action.show(req.query,
            (err, result) => {
              if (err) {
                res.status(500).send('Server Error')
                return console.error(err)
              }

              res.render('index', result)
            }
          )
        }
      )
    }
  )
}
