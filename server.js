const fs = require('fs')
const express = require('express')
const app = express()
const port = 3000

const database = require('./src/database')
const Action = require('./src/Action')
require('./src/entities')

const config = JSON.parse(fs.readFileSync('conf.json'))
database.init(config)

app.set('views', __dirname + '/views')
app.set('view engine', 'twig')

app.get('/', (req, res) => {
  res.render('index', {
    message: 'Hello World!'
  })
})

app.get('/view/:id', (req, res) => {
  const params = req.query
  params.id = req.params.id

  Action.get('view', params,
    (err, action) => {
      action.show(req.params, req.query,
        (err, result) => {
          if (err) { return console.error(err) }

          if (req.headers['content-type']) {
            res.setHeader('Content-Type', 'application/json');
            res.send(JSON.stringify(entity.data, null, 2))
          } else {
            res.render('index', result)
          }
        }
      )
    }
  )
})

app.use('/static', express.static('static'))
app.use('/node_modules', express.static('node_modules'))
app.use('/dist', express.static('dist'))

app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})
