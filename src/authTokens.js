const crypto = require('crypto')

const database = require('./database')
const now = require('./now')

module.exports = {
  authorize (res, user, callback) {
    const authToken = generateAuthToken()
    res.cookie('AuthToken', authToken)

    database.query(
      'insert into auth_tokens (user, token, last_access) values (?, ?, ?)',
      [user, authToken, now()],
      callback
    )
  },

  check (req, res, next) {
    const authToken = req.cookies.AuthToken
    if (!authToken) {
      return next()
    }

    database.query(
      'select * from auth_tokens where token=?',
      [authToken],
      (err, result) => {
        if (err) {
          console.error('Error accessing auth tokens:', err)
        }

        if (result && result.length) {
          req.user = result[0].user
        }

        next()
      }
    )
  }
}

// Source: https://stackabuse.com/handling-authentication-in-express-js/
function generateAuthToken () {
  return crypto.randomBytes(30).toString('hex')
}
