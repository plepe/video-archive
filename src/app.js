const VideoJS = require('video.js')
const state = require('./state')
const updateLinks = require('./updateLinks')
const Entity = require('./Entity')
const Action = require('./Action')
const render = require('./render')
require('./entities')

function newPage (data) {
  if (!('action' in data)) {
    if ('id' in data) {
      data.action = 'view'
    } else {
      data.action = 'list'
    }
  }

  render(data, (err, result) => {
    if (err) { return global.alert(err) }

    const content = document.getElementById('content')
    content.innerHTML = result.content

    updateLinks()
  })

  return true
}

window.onload = () => {
  state.init()
  state.onchange = newPage
  updateLinks()
}
