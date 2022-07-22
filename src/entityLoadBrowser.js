module.exports = {
  get (id, callback) {
    fetch('/view/' + id,
      {
        headers: {
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => callback(null, data))
  }
}
