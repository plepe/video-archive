const fs = require('fs')
const express = require('express')
const app = express()
const port = 3000

const database = require('./src/database')

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
  database.query('select * from entity where id=?', [ req.params.id ],
    (err, result) => {
      if (err) {
        console.error(err)
      }

      res.render('index', { message: JSON.stringify(result) })
    })

})

app.use('/static', express.static('static'))
app.use('/node_modules', express.static('node_modules'))
app.use('/dist', express.static('dist'))

app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})
