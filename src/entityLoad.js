const database = require('./database')

module.exports = function entityLoad (id, callback) {
  database.query('select * from entity where id=?', [ id ],
  (err, result) => {
    if (err) { return console.error(err) }

    callback(null, result[0])
  })
}
