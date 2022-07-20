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

app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})
