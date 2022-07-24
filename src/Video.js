const escapeHTML = require('escape-html')
const Entity = require('./Entity')
const url = require('./url')

class Video extends Entity {
  showFull (options = {}) {
    let result = '<div id="' + this.id + '">'

    let href = options.additionalUrlParameters || {}
    href.id = this.id
    href.file = 'video'

    href = url(href, 'data')

    result += "<div class=\"videoContainer\"><video class='video-js' data-setup='{}' controls><source type=\"video/mp4\" src=\"" + escapeHTML(href) + '"></video></div>\n'
    result += '<div class="title">' + escapeHTML(this.data.title) + '</div>\n'

    return result
  }

  showTeaser (options = {}) {
    let result = '<div id="' + this.id + '" class="">\n'
    console.log(this.data)

    const _url = { ...(options.additionalUrlParameters || {}), ...{ id: this.id, action: 'view' } }

    result += '<div class="title"><a href="' + escapeHTML(url(_url)) + '">' + escapeHTML(this.data.title) + '</a></div>\n'

    result += '</div>'

    return result
  }

  getFile (options, callback) {
    callback(null, {
      filename: 'video.mp4',
      path: this.id,
      mime: 'video/mp4'
    })
  }
}

Entity.classes.Video = Video
