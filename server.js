const path = require('path')
const fs = require('fs')
const express = require('express')
const bodyParser = require('body-parser')
const multer = require('multer')
const app = express()
const port = 3000

const database = require('./src/database')
const Entity = require('./src/Entity')
const entityLoad = require('./src/entityLoad')
const handleAction = require('./src/handleAction')
require('./src/entities')

const config = JSON.parse(fs.readFileSync('conf.json'))
database.init(config)

// parse application/json
app.use(express.json())
// To support URL-encoded bodies
app.use(bodyParser.urlencoded({ extended: true }))

app.set('views', path.join(__dirname, '/views'))
app.set('view engine', 'twig')

app.get('/', (req, res) => {
  handleAction('get', req, res)
})

const upload = multer({ dest: config.data_dir + '/tmp' })
app.post('/', upload.fields([{ name: 'upload' }]), (req, res) => {
  handleAction('post', req, res)
})

app.get('/ids', (req, res) => {
  if (req.headers['content-type'] && req.headers['content-type'] === 'application/json') {
    res.setHeader('Content-Type', 'application/json')
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
})

app.get('/view/:id', (req, res) => {
  req.query.id = req.params.id
  req.query.action = 'view'
  handleAction('get', req, res)
})

app.get('/api/:id', (req, res) => {
  res.setHeader('Content-Type', 'application/json')
  Entity.get(req.params.id, (err, entity) => {
    if (err) {
      res.status(500).send('Server Error')
      return console.error(err)
    }

    res.send(JSON.stringify(entity.data, null, 2))
  })
})

app.post('/api/:id', (req, res) => {
  res.setHeader('Content-Type', 'application/json')
  Entity.get(req.params.id, (err, entity) => {
    if (err) {
      res.status(500).send('Server Error')
      return console.error(err)
    }

    console.log(req.body)
    entity.save(req.body, (err) => {
      if (err) {
        res.status(500).send('Server Error')
        return console.error(err)
      }

      res.send(JSON.stringify(entity.data, null, 2))
    })
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

      entity.getFile(req.query,
        (err, result) => {
          if (err) {
            res.status(500).send('Server Error')
            return console.error(err)
          }

          if (!result) {
            res.status(404).send('File not found')
            return
          }

          res.setHeader('Content-Type', result.mime)
          const file = config.data_dir + '/' + result.path + '/' + result.filename
          fs.stat(file,
            (err, stat) => {
              if (err) {
                res.status(500).send('Server Error')
                return console.error(err)
              }

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
