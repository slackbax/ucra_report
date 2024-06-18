$(document).ready(function () {
  const $form = $('#form-kidney'), $data = $('#patdata_list'), $down = $('#down_link'), $loader = $('#loader'),
    $result = $('#result_div'),
    pressureChart = document.getElementById('pressureChart'), heartChart = document.getElementById('heartChart'),
    options = {
      url: 'ajax/load-data.php',
      type: 'post',
      dataType: 'json',
      beforeSubmit: validateForm,
      success: showResponse
    }

  $('#patdata').MultiFile({
    max: 2,
    accept: 'csv',
    STRING: {
      selected:'Seleccionado: $file',
      denied: '¡Archivo de tipo .$ext no permitido! Inténtalo nuevamente.'
    }
  })

  let pChart = '', hChart = ''

  function validateForm() {
    $loader.css('display', 'block');

    if ($data.html() === '') {
      new Noty({
        text: '<strong>¡Error!</strong><br>Debe elegir un archivo para analizar los datos.',
        type: 'error'
      }).show()

      $loader.css('display', 'none');
      return false;
    } else {
      return true
    }
  }

  function showResponse(r) {
    $loader.css('display', 'none');
    if (r.type) {
      new Noty({
        text: '<strong>¡Éxito!</strong><br>El archivo ha sido evaluado correctamente.',
        type: 'success'
      }).show()

      $('input:file').MultiFile('reset')
      $down.attr('href', r.url).css('display', 'block')
      $result.css('display', 'block');

      if (pChart !== '') pChart.destroy()
      if (hChart !== '') hChart.destroy()

      pChart = new Chart(pressureChart, {
        type: 'line',
        data: {
          labels: r.stats.dates.data,
          datasets: [{
            label: 'Sistólica',
            data: r.stats.systolic.data,
            borderWidth: 1,
            borderColor: '#DC3545',
            backgroundColor: '#DC3545'
          }, {
            label: 'Presión Media',
            data: r.stats.avg,
            borderWidth: 2,
            borderColor: '#ffffff',
            backgroundColor: '#666666'
          }, {
            label: 'Diastólica',
            data: r.stats.diastolic.data,
            borderWidth: 1,
            borderColor: '#325285',
            backgroundColor: '#325285',
            fill: {
              target: '-2',
              below: 'rgba(183, 183, 183, .5)'
            }
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: 'Evolución de Presión Arterial'
            },
            annotation: {
              annotations: {
                systolic: {
                  type: 'line',
                  mode: 'horizontal',
                  yMin: 140,
                  yMax: 140,
                  borderColor: '#DC3545',
                  borderWidth: 1,
                  borderDash: [5, 5]
                },
                diastolic: {
                  type: 'line',
                  mode: 'horizontal',
                  yMin: 90,
                  yMax: 90,
                  borderColor: '#325285',
                  borderWidth: 1,
                  borderDash: [5, 5]
                }
              }
            },
          },
          scales: {
            y: {
              max: Math.ceil((r.stats.systolic.max + 20) / 10) * 10,
              min: Math.ceil((r.stats.diastolic.min - 30) / 10) * 10,
              beginAtZero: false,
              title: {
                display: true,
                text: 'mmHg'
              }
            },
            x: {
              title: {
                display: true,
                text: 'Momento de medición'
              }
            }
          }
        }
      });

      hChart = new Chart(heartChart, {
        type: 'line',
        data: {
          labels: r.stats.dates.data,
          datasets: [{
            label: 'Latidos por minuto',
            data: r.stats.pulse.data,
            borderWidth: 1,
            borderColor: '#59A09B',
            backgroundColor: '#59A09B'
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: 'Evolución de Frecuencia Cardíaca'
            }
          },
          scales: {
            y: {
              max: Math.ceil((r.stats.pulse.max + 10) / 10) * 10,
              min: Math.ceil((r.stats.pulse.min - 10) / 10) * 10,
              beginAtZero: false,
              title: {
                display: true,
                text: 'BPM'
              }
            },
            x: {
              title: {
                display: true,
                text: 'Momento de medición'
              }
            }
          }
        }
      });
    } else {
      new Noty({
        text: '<strong>¡Error!</strong><br>' + r.msg,
        type: 'error'
      }).show()
    }
  }

  $form.submit(function () {
    $(this).ajaxSubmit(options)
    return false
  })
})