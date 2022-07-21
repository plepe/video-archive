const queryString = require('qs')

module.exports = function url (options, file = '') {
  if (typeof options === 'string') {
    return options
  }

  if (Object.keys(options).length === 0) {
    return '/'
  }

  let result = '/'
  if (options.id) {
    result += (file || 'view') + '/' + options.id
    delete options.id
  }

  if (Object.keys(options).length > 0) {
    result += '?' + queryString.stringify(options)
  }

  return result
}
