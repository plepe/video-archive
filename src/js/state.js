const queryString = require('qs')
const EventEmitter = require('events')

class State extends EventEmitter {
  parse (str) {
    return queryString.parse(str)
  }

  init () {
    let newState = {}
    this.data = {}

    if (location.search && location.search.length > 1) {
      newState = this.parse(location.search.substr(1))
    }

    for (let k in newState) {
      this.data[k] = newState[k]
    }

    window.addEventListener('popstate', e => {
      this.apply(e.state, true)
    })
  }

  apply (param, noPushState = false) {
    for (let k in this.data) {
      delete this.data[k]
    }
    for (let k in param) {
      this.data[k] = param[k]
    }

    this.indicate_loading()

    if (!noPushState) {
      history.pushState(this.data, '', '?' + queryString.stringify(this.data))
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
    let newState = JSON.parse(JSON.stringify(this.data))

    for (let k in param) {
      newState[k] = param[k]
    }

    this.apply(newState, noPushState)
  }

  apply_from_form (dom) {
    let data = data_from_form(dom)

    return this.apply(data)
  }
}

let state = new State()

module.exports = state
