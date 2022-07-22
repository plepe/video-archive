//        async.mapSeries(result,
//          (data, done) => loadEntityProperties(data, done),
//          (err, result) => callback(err, result)
//        )
const async = require('async')

const database = require('./database')

const entityDataDef = {
  Video: {
    table: 'video',
    properties: [ 'title', 'filesize', 'duration', 'originalFile' ]
  }
}

module.exports = {
  get (id, callback) {
    database.query('select * from entity where id=?', [ id ],
      (err, result) => {
        if (err) { return callback(err) }

        if (!result.length) {
          return callback(null, null)
        }

        loadEntityProperties(result[0], callback)
      }
    )
  },

  list (options, callback) {
    database.query('select * from entity where class=\'Video\'', [],
      (err, result) => {
        if (err) { return callback(err) }

        result = result.map(data => data.id)

        callback(null, result)
      }
    )
  }
}

function loadEntityProperties (data, callback) {
  if (!(data.class in entityDataDef)) {
    return callback(null, data)
  }

  const def = entityDataDef[data.class]

  if (def.table) {
    database.query('select * from ' + def.table + ' where id=?', [ data.id ],
      (err, result) => {
        if (err) { return callback(err) }

        if (!result.length) {
          return callback(null, data)
        }

        def.properties.forEach(key => {
          data[key] = result[0][key]
        })

        callback(null, data)
      }
    )
  } else {
    callback(null, data)
  }
}
