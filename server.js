const fs = require('fs')
const express = require('express')
const app = express()
const port = 3000

const database = require('./src/database')
const Entity = require('./src/Entity')
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
          if (err) {
            res.status(500).send('Server Error')
            return console.error(err)
          }

          if (!result) {
            res.status(404).send('Entity not found')
            return
          }

          if (req.headers['content-type']) {
            res.setHeader('Content-Type', 'application/json');
            res.send(JSON.stringify(action.entity.data, null, 2))
          } else {
            res.render('index', result)
          }
        }
      )
    }
  )
})

app.get('/data/:id', (req, res) => {
  Entity.get(req.params.id,
    (err, entity) => {
      if (err) {
        res.status(500).send('Server Error')
        return console.error(err)
      }

      if (!entity) {
        res.status(404).send('Entity not found')
        return
      }

      const file = entity.getFile(req.query,
        (err, result) => {
          if (!result) {
            res.status(404).send('File not found')
            return
          }

          res.setHeader('Content-Type', result.mime)

          const stream = fs.createReadStream(config.data_dir + '/' + result.path + '/' + result.filename)
          stream.pipe(res)
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
