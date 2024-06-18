function numberFormat(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^\d+\-Ee.]/g, '')
  let n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? ',' : dec_point
  let s = ''
  let toFixedFix = function (n, prec) {
    let k = Math.pow(10, prec)
    return '' + (Math.round(n * k) / k).toFixed(prec)
  }
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1).join('0')
  }
  return s.join(dec)
}

function validateRut(rut) {
  rut = rut.replace(/[\.-]/g, '')
  let body = rut.slice(0, -1)
  let checkDigit = rut.slice(-1).toUpperCase()
  if (!/^[0-9]+[kK]?$/i.test(rut)) {
    return false
  }
  let sum = 0
  let factor = 2
  for (let i = body.length - 1; i >= 0; i--) {
    sum += factor * body[i]
    factor = factor === 7 ? 2 : factor + 1
  }
  let calculatedCheckDigit = 11 - (sum % 11)
  calculatedCheckDigit = calculatedCheckDigit === 11 ? 0 : calculatedCheckDigit === 10 ? 'K' : calculatedCheckDigit
  return checkDigit === calculatedCheckDigit
}

function validateEmail(email) {
  const input = $('<input>').attr('type', 'email').val(email)
  return input[0].checkValidity()
}

function setDate(date) {
  const tmp = date.split('/')
  return tmp[2] + '-' + tmp[1] + '-' + tmp[0]
}

function getDate(date) {
  const tmp = date.split('-')
  return tmp[2] + '/' + tmp[1] + '/' + tmp[0]
}

function getAge(birthdate) {
  const birthDate = new Date(birthdate);
  const currentDate = new Date();
  let years = currentDate.getFullYear() - birthDate.getFullYear();
  let months = currentDate.getMonth() - birthDate.getMonth();
  if (
    currentDate.getMonth() < birthDate.getMonth() ||
    (currentDate.getMonth() === birthDate.getMonth() && currentDate.getDate() < birthDate.getDate())
  ) {
    years--;
    months = 12 + months;
  }
  let age = ''
  if (years > 0)
    age += years + ' años '
  if (months > 0)
    age += months + ' meses'
  return age;
}
