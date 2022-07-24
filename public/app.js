const endpoint = '/api/v1/hello'
// let method = 'POST'
// let time_interval = 5


const results_container = document.querySelector('#result')

window.lastCall = 0;
window.setInterval(() => {

  let method = document.querySelector('.method-radio:checked').value
  let time_interval = document.querySelector('#interval').value
  const now = Date.now()
  let function_should_be_executed = now >= window.lastCall + (time_interval * 1000)
  if (function_should_be_executed) {
    fetch(endpoint, {method})
      .then(resp => resp.json())
      .then(data => {
        results_container.innerHTML += JSON.stringify(data) + '<br>'
      })
    window.lastCall = now
  } else {
    console.log('not yet')
  }
}, 1000)
