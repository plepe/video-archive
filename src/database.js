const Database = require('database-js').Connection

class DB {
  init (config) {
    if (config.db) {
      this.db = new Database(config.db)
    } else {
      this.db = new Database('sqlite:///' + config.data_dir + '/db.sqlite')
    }

    this.prepared = {}
  }

  query (qry, params, callback) {
    if (!(qry in this.prepared)) {
      this.prepared[qry] = this.db.prepareStatement(qry)
    }

    const stmt = this.prepared[qry]

    stmt.execute(...params)
      .then(results => callback(null, results))
      .catch(reason => global.setTimeout(() => callback(reason), 0))
  }
}

module.exports = new DB()
