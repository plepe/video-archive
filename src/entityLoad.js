const database = require('./database')
const now = require('./now')

const entityDataDef = {
  Entity: {
    table: 'entity',
    properties: ['author', 'tsUpdate']
  },
  Video: {
    table: 'video',
    properties: ['title', 'filesize', 'duration', 'originalFile']
  }
}

module.exports = {
  get (id, callback) {
    database.query('select * from entity where id=?', [id],
      (err, result) => {
        if (err) { return callback(err) }

        if (!result.length) {
          return callback(null, null)
        }

        loadEntityProperties(result[0], callback)
      }
    )
  },

  save (id, _class, data, callback) {
    data.tsUpdate = now()
    saveProperties(id, entityDataDef.Entity, data,
      (err) => {
        if (err) { return callback(err) }
        saveProperties(id, entityDataDef[_class], data, callback)
      }
    )
  },

  list (options, callback) {
    database.query('select * from entity', [],
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
    database.query('select * from ' + def.table + ' where id=?', [data.id],
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

function saveProperties (id, def, data, callback) {
  const properties = def.properties.filter(p => p in data)
  if (!properties.length) {
    return callback(null)
  }

  const param = properties.map(p => data[p])
  param.push(id)
  const qry = 'update ' + def.table + ' set ' + properties.map(p => p + '=?').join(', ') + ' where id=?'

  database.query(qry, param,
    (err, result) => {
      callback(err, result)
    }
  )
}
