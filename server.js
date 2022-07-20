const fs = require('fs')
const express = require('express')
const app = express()
const port = 3000

const database = require('./src/database')
const Entity = require('./src/Entity')
require('./src/Video')

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
  Entity.get(req.params.id,
    (err, entity) => {
      if (err) { return console.error(err) }

      if (req.headers['content-type']) {
        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify(entity.data, null, 2))
      } else {
        res.render('index', { message: JSON.stringify(entity.data) })
      }
    }
  )
})

app.use('/static', express.static('static'))
app.use('/node_modules', express.static('node_modules'))
app.use('/dist', express.static('dist'))

app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})
