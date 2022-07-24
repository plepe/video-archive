const format = require('date-format')

module.exports = function now () {
  return format.asString('yyyy-MM-dd hh:mm:ss.SSS')
}
