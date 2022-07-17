const queryString = require('qs')

module.exports = function url (options, file = '') {
  if (typeof options === 'string') {
    return options
  }

  if (Object.keys(options).length === 0) {
    return '.'
  }

  return file + '?' + queryString.stringify(options)
}
