const VideoJS = require('video.js')
const state = require('./state')
const updateLinks = require('./updateLinks')
const Entity = require('./Entity')
require('./entities')

function newPage (data) {
  if (!('action' in data)) {
    if ('id' in data) {
      data.action = 'show'
    } else {
      data.action = 'list'
    }
  }

  if (data.action in Actions) {
    const action = new Actions[data.action](data)
    action.load((err) => {
      if (err) { return global.alert(err) }

      const text = action.show()

      const content = document.getElementById('content')
      content.innerHTML = text
    })

    return true
  }

  console.log(data)
}

window.onload = () => {
  state.init()
  state.onchange = newPage
  updateLinks()
}
