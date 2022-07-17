const VideoJS = require('video.js')
const state = require('./state')
const updateLinks = require('./updateLinks')

function newPage (data) {
  console.log(data)
  return true
}

window.onload = () => {
  state.init()
  state.onchange = newPage
  updateLinks()
}
