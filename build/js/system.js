$(document).ready(function () {
  Noty.overrideDefaults({
    theme: 'sunset',
    timeout: 3000,
    killer: true,
    closeWith: ['click']
  })
  $('[data-toggle="tooltip"]').tooltip({html:true})
})