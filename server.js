const fs = require('fs')
const express = require('express')
const app = express()
const port = 3000

const database = require('./src/database')
const Entity = require('./src/Entity')
const Action = require('./src/Action')
const entityLoad = require('./src/entityLoad')
require('./src/entities')

const config = JSON.parse(fs.readFileSync('conf.json'))
database.init(config)

app.set('views', __dirname + '/views')
app.set('view engine', 'twig')

app.get('/', (req, res) => {
  if (req.headers['content-type'] && req.headers['content-type'] === 'application/json') {
    res.setHeader('Content-Type', 'application/json');
    Entity.list(req.query, (err, result) => {
      if (err) {
        res.status(500).send('Server Error')
        return console.error(err)
      }

      res.send(JSON.stringify(result, null, 2))
    })

    return
  }

  Action.get('list', req.params,
    (err, action) => {
      if (err) {
        res.status(500).send('Server Error')
        return console.error(err)
      }

      action.show(req.params, req.query,
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
})

app.get('/ids', (req, res) => {
  if (req.headers['content-type'] && req.headers['content-type'] === 'application/json') {
    res.setHeader('Content-Type', 'application/json');
    entityLoad.list(req.query, (err, result) => {
      if (err) {
        res.status(500).send('Server Error')
        return console.error(err)
      }

      res.send(JSON.stringify(result, null, 2))
    })

    return
  }

  res.status(500).send('Server Error')
  return console.error(err)
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
          const file = config.data_dir + '/' + result.path + '/' + result.filename
          fs.stat(file,
            (err, stat) => {
              res.setHeader('Content-Length', stat.size)
              res.setHeader('Last-Modified', stat.mtime)

              const stream = fs.createReadStream(file)
              stream.pipe(res)
            }
          )
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
