const express = require('express')
const app = express()
const port = 3000

app.set('views', __dirname + '/views')
app.set('view engine', 'twig')

app.get('/', (req, res) => {
  res.render('index', {
    message: 'Hello World!'
  })
})

app.get('/view/:id', (req, res) => {
  res.render('index', {
    message: 'params: ' + JSON.stringify(req.params) + ' query: ' + JSON.stringify(req.query)
  })
})

app.use('/static', express.static('static'))
app.use('/node_modules', express.static('node_modules'))
app.use('/dist', express.static('dist'))

app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})
