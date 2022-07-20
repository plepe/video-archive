module.exports = function entityLoad (id, callback) {
  fetch('/view/' + id,
    {
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(res => res.json())
    .then(data => callback(null, data))
}
