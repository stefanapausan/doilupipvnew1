function validateNumber(input) {
  var re = /^(\+4|)?(07[0-8]{1}[0-9]{1}|02[0-9]{2}|03[0-9]{2}){1}?(\s|\.|\-)?([0-9]{3}(\s|\.|\-|)){2}$/

  return re.test(input)
}

function validateForm(event) {
  var number = document.getElementById('edit-telefon-service').value
  if (!validateNumber(number)) {
    alert('Numarul de telefon nu este valid')
     event.preventDefault()
  } 
}

document.getElementById('service-form').addEventListener('submit', validateForm)