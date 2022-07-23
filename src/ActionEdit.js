const Action = require('./Action')
const Entity = require('./Entity')

class ActionEdit extends Action {
  show (params, callback) {
    if (this.done) {
      const content = '<pre>' + JSON.stringify(this.done.body) + '\n' + JSON.stringify(this.done.files) + '</pre>'
      return callback(null, { content })
    }

    Entity.get(params.id,
      (err, entity) => {
        if (err) { return callback(err) }

        if (!entity) {
          return callback(null, null)
        }

        let content = '<form enctype="multipart/form-data" method="post">';
        content += 'Title: <input type="text" name="title"><br>'
        content += 'File: <input type="file" name="upload"><br>'
        content += '<input type="submit">'
        content += '</form>'

        callback(null, { content })
      }
    )
  }

  request_post (data, callback) {
    this.done = data

    callback(null)
  }
}

Action.classes.edit = ActionEdit
