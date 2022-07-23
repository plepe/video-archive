const Action = require('./Action')
const Entity = require('./Entity')

class ActionEdit extends Action {
  show (params, callback) {
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

  post (params, data, callback) {
    const content = '<pre>' + JSON.stringify(data.body) + '\n' + JSON.stringify(data.files) + '</pre>'

    callback(null, { content })
  }
}

Action.classes.edit = ActionEdit
