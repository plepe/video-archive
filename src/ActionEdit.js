const Action = require('./Action')

class ActionEdit extends Action {
  show_html (res, callback) {
    if (this.done) {
      const content = '<pre>' + JSON.stringify(this.done.body) + '\n' + JSON.stringify(this.done.files) + '</pre>'
      return callback(null, { content })
    }

    if (!this.entity) {
      return callback(null, null)
    }

    let content = '<form enctype="multipart/form-data" method="post">';
    content += 'Title: <input type="text" name="title"><br>'
    content += 'File: <input type="file" name="upload"><br>'
    content += '<input type="submit">'
    content += '</form>'

    callback(null, { content })
  }

  request_post (request, callback) {
    this.done = request

    this.entity.save(request.body, callback)
  }
}

Action.classes.edit = ActionEdit
