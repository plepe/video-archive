const fs = require('fs')
const express = require('express')
const multer = require('multer')
const app = express()
const port = 3000

const database = require('./src/database')
const Entity = require('./src/Entity')
const Action = require('./src/Action')
const entityLoad = require('./src/entityLoad')
const render = require('./src/render')
const handleAction = require('./src/handleAction')
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

  const params = req.query
  if (!params.action) {
    if (params.id) {
      params.action = 'view'
    } else {
      params.action = 'list'
    }
  }

  render(params, (err, result) => {
    if (err) {
      res.status(500).send('Server Error')
      return console.error(err)
    }

    res.render('index', result)
  })
})

const upload = multer({ dest: config.data_dir + '/tmp' })
app.post('/',upload.fields([{ name: 'upload' }]), (req, res) => {
  handleAction('post', req, res)
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
  if (req.headers['content-type'] && req.headers['content-type'] === 'application/json') {
    res.setHeader('Content-Type', 'application/json');

    Entity.get(req.params.id, (err, result) => {
      if (err) {
        res.status(500).send('Server Error')
        return console.error(err)
      }

      res.send(JSON.stringify(result.data, null, 2))
    })

    return
  }

  const params = req.query
  params.id = req.params.id
  params.action = 'view'

  render(params, (err, result) => {
    if (err) {
      res.status(500).send('Server Error')
      return console.error(err)
    }

    if (!result) {
      res.status(404).send('Entity not found')
      return
    }

    res.render('index', result)
  })
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
