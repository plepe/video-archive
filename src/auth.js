const fs = require('fs')
const htpasswd = require('htpasswd-authenticator')

module.exports = function (req, callback) {
  fs.readFile('.htpasswd', 'utf-8',
    (err, file) => {
      if (err) { return callback(err) }
      check(req, file, callback)
    }
  )
}

function check (req, file, callback) {
  htpasswd.authenticate(req.body.username, req.body.password, file)
    .then(auth => callback(null, auth ? req.body.username : false))
}
