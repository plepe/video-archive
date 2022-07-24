const queryString = require('qs')
const EventEmitter = require('events')

class State extends EventEmitter {
  parse (str) {
    return queryString.parse(str)
  }

  init () {
    let newState = {}
    this.data = {}

    if (global.location.search && global.location.search.length > 1) {
      newState = this.parse(global.location.search.substr(1))
    }

    for (const k in newState) {
      this.data[k] = newState[k]
    }

    window.addEventListener('popstate', e => {
      this.apply(e.state, true)
    })
  }

  apply (param, noPushState = false) {
    for (const k in this.data) {
      delete this.data[k]
    }
    for (const k in param) {
      this.data[k] = param[k]
    }

    this.indicate_loading()

    if (!noPushState) {
      global.history.pushState(this.data, '', '?' + queryString.stringify(this.data))
    }

    this.emit('apply', this.data)

    if (this.onchange) {
      return this.onchange(this.data)
    }
  }

  indicate_loading () {
    document.body.classList.add('loading')
  }

  abort () {
    document.body.classList.remove('loading')
  }

  change (param, noPushState = false) {
    const newState = JSON.parse(JSON.stringify(this.data))

    for (const k in param) {
      newState[k] = param[k]
    }

    this.apply(newState, noPushState)
  }
}

const state = new State()

module.exports = state
