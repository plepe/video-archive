const crypto = require('crypto')

const authTokens = {}

module.exports = {
  authorize (res, user) {
    const authToken = generateAuthToken()
    authTokens[authToken] = user
    res.cookie('AuthToken', authToken)
  },

  check (req, res, next) {
    const authToken = req.cookies['AuthToken']
    req.user = authTokens[authToken]
    next()
  }
}

// Source: https://stackabuse.com/handling-authentication-in-express-js/
function generateAuthToken () {
  return crypto.randomBytes(30).toString('hex');
}
