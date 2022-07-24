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
      if (err) {
        res.status(500).send('Server Error')
        return console.error(err)
      }

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

          let responseType = 'html'
          if (req.headers['content-type'] && req.headers['content-type'] === 'application/json') {
            responseType = 'json'
            res.setHeader('Content-Type', 'application/json');
          }

          action['show_' + responseType](res,
            (err, result) => {
              if (err) {
                res.status(500).send('Server Error')
                return console.error(err)
              }

              if (!result) {
                res.status(404).send('Entity not found')
                return
              }

              if (responseType === 'html') {
                res.render('index', result)
              } else if (responseType === 'json') {
                res.send(JSON.stringify(result, null, 2))
              } else {
                res.send(result)
              }
            }
          )
        }
      )
    }
  )
}
