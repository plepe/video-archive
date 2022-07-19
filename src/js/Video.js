const Entity = require('./Entity')
const url = require('./url')

class Video extends Entity {
  showFull (options = {}) {
    let result = '<div id="' + this.id + '">'

    let href = options.additionalUrlParameters || {}
    href.id = this.id
    href.file = 'video'

    href = url(href, 'download.php')

    result += "<div class=\"videoContainer\"><video class='video-js' data-setup='{}' controls><source type=\"video/mp4\" src=\"" + href + "\"></video></div>\n"
    result += "<div class=\"title\">" + this.data.title + "</div>\n"

    return result
  }
}

Entity.classes.Video = Video
