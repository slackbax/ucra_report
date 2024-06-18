<?php
require '../src/settings.php';
require '../src/constants.php';
require '../src/functions.php';
extract($_GET);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRT Biobío</title>
  <?php include '../src/favicon.php' ?>
  <?php include '../src/styles.php' ?>
</head>

<body class="hold-transition layout-top-nav layout-footer-fixed">
<input type="hidden" id="callapk" value="<?php echo base64_encode(API_KEY) ?>">
<input type="hidden" id="callid">
<section class="content" id="main-screen">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <div class="text-center mt-3 mb-3">
          <img alt="CRT Biobio" src="../dist/img/logo_crt.png" style="height:60px">
        </div>

        <div id="patient-data">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">
                Datos del paciente
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-3">
                  <label for="irut">Documento</label>
                  <input type="text" class="form-control form-control-border" id="irut" readonly autocomplete="off">
                  <input type="hidden" id="ipid" name="ipid">
                </div>

                <div class="form-group col-md-9">
                  <label for="iname">Nombre completo</label>
                  <input type="text" class="form-control form-control-border" id="iname" name="iname" readonly autocomplete="off">
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-3">
                  <label for="ibdate">Fecha de nacimiento</label>
                  <input type="text" class="form-control form-control-border" id="ibdate" name="ibdate" readonly autocomplete="off">
                </div>

                <div class="form-group col-md-4">
                  <label for="iedad">Edad</label>
                  <input type="text" class="form-control form-control-border" id="iedad" name="iedad" readonly autocomplete="off">
                </div>

                <div class="form-group col-md-5">
                  <label for="isex">Sexo biológico</label>
                  <input type="text" class="form-control form-control-border" id="isex" name="isex" readonly autocomplete="off">
                </div>
              </div>

              <div class="row">
                <div class="col-12">
                  <label for="imorbids">Antecedentes</label>
                  <div class="form-control form-control-border" id="imorbids" style="min-height:100px"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">
                Anamnesis próxima
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <label for="ireason">Motivo de consulta</label>
                  <div class="form-control form-control-border" id="ireason" style="min-height:100px"></div>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-3">
                  <label for="ipulse">F. cardíaca</label>
                  <div class="input-group">
                    <input type="text" id="ipulse" class="form-control text-right" readonly autocomplete="off">
                    <div class="input-group-append">
                      <span class="input-group-text">l/m</span>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-3">
                  <label for="irespiration">F. respiratoria</label>
                  <div class="input-group">
                    <input type="text" id="irespiration" class="form-control text-right" readonly autocomplete="off">
                    <div class="input-group-append">
                      <span class="input-group-text">r/m</span>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-3">
                  <label for="itemperature">Temp.</label>
                  <div class="input-group">
                    <input type="text" id="itemperature" class="form-control text-right" readonly autocomplete="off">
                    <div class="input-group-append">
                      <span class="input-group-text">ºC</span>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-3">
                  <label for="isaturation">Ox. pulso</label>
                  <div class="input-group">
                    <input type="text" id="isaturation" class="form-control text-right" readonly autocomplete="off">
                    <div class="input-group-append">
                      <span class="input-group-text">%</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-3 offset-md-3">
                  <label for="ipressure">P. arterial</label>
                  <div class="input-group">
                    <input type="text" name="ipressure" id="ipressure" class="form-control text-right" readonly autocomplete="off">
                    <div class="input-group-append">
                      <span class="input-group-text">mm Hg</span>
                    </div>
                  </div>
                </div>
                <div class="form-group col-md-3">
                  <label for="iglycemia">Glicemia capilar</label>
                  <div class="input-group">
                    <input type="text" name="iglycemia" id="iglycemia" class="form-control text-right" readonly autocomplete="off">
                    <div class="input-group-append">
                      <span class="input-group-text">mg/dL</span>
                    </div>
                  </div>
                </div>
                <div class="form-group col-md-3">
                  <button type="button" id="show_history" class="btn btn-block btn-info btn-form" tabindex="-1" data-toggle="modal" data-target="#history-txt">
                    <i class="fa fa-history mr-2"></i>Historial
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6" id="video-meeting"></div>
    </div>
  </div>
</section>

<div class="modal fade" id="welcome">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <div class="text-center">
          <img alt="CRT Biobio" src="../dist/img/logo_crt.png" style="height:60px">
        </div>

        <div class="bs-stepper">
          <div class="bs-stepper-header d-none d-md-flex" role="tablist">
            <div class="step" data-target="#patient-data">
              <button type="button" class="step-trigger" role="tab" aria-controls="patient-data" id="patient-data-trigger">
                <span class="bs-stepper-circle">1</span>
                <span class="bs-stepper-label">Datos del paciente</span>
              </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#attention-data">
              <button type="button" class="step-trigger" role="tab" aria-controls="attention-data" id="attention-data-trigger">
                <span class="bs-stepper-circle">2</span>
                <span class="bs-stepper-label">Anamnesis próxima</span>
              </button>
            </div>
          </div>

          <form id="fWelcome" role="form">
            <div class="bs-stepper-content pr-0 pl-0 pb-0">
              <div id="patient-data" class="content" role="tabpanel" aria-labelledby="patient-data-trigger">
                <div class="card card-success card-flat">
                  <div class="card-header">
                    <h3 class="card-title">
                      Datos del paciente
                    </h3>
                  </div>

                  <div class="card-body">
                    <div class="row">
                      <div class="form-group col-12">
                        <div class="icheck-default">
                          <input type="radio" name="is_rut" id="rd-rut" value="1" checked="">
                          <label for="rd-rut">Cédula de identidad</label>
                        </div>
                        <div class="icheck-default">
                          <input type="radio" name="is_rut" id="rd-passport" value="0">
                          <label for="rd-passport">Pasaporte</label>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="rut" class="require-field">Nº documento</label>
                        <input type="text" class="form-control" id="rut" name="rut" placeholder="12.345.678-9" maxlength="12" autocomplete="off">
                        <input type="text" class="form-control" id="passp" name="rut" placeholder="Número de documento" autocomplete="off">
                        <input type="hidden" class="loaded" id="pid" name="pid" value="1">
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="name" class="require-field">Nombres</label>
                        <input type="text" class="form-control loaded" id="name" name="name" placeholder="Ingresa nombres del paciente" autocomplete="off">
                      </div>

                      <div class="form-group col-md-6">
                        <label for="pname" class="require-field">Primer apellido</label>
                        <input type="text" class="form-control loaded" id="pname" name="pname" placeholder="Ingresa primer apellido del paciente" autocomplete="off">
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="sname">Segundo apellido</label>
                        <input type="text" class="form-control loaded" id="sname" name="sname" placeholder="Ingresa segundo apellido del paciente" autocomplete="off">
                      </div>

                      <div class="form-group col-md-6">
                        <label for="sex" class="require-field">Sexo biológico</label>
                        <select type="text" class="form-control loaded" id="sex" name="sex">
                          <option value="">Selecciona sexo del paciente</option>
                          <option value="F">Femenino</option>
                          <option value="M">Masculino</option>
                        </select>
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="bdate" class="require-field">Fecha de nacimiento</label>
                        <div id="gdate" class="input-group date" data-target-input="nearest">
                          <input type="text" class="form-control loaded datetimepicker-input float-right" id="bdate" name="bdate" data-target="#gdate" placeholder="dd/mm/yyyy" autocomplete="off">
                          <div class="input-group-append" data-target="#gdate" data-toggle="datetimepicker">
                          <span class="input-group-text">
                              <i class="fad fa-calendar-alt"></i>
                          </span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12">
                        <label for="morbids">Antecedentes</label>
                        <textarea name="morbids" id="morbids" class="summernote" rows="4"></textarea>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12">
                        <p class="text-gray">Los datos marcados con * son obligatorios</p>
                      </div>
                    </div>
                  </div>

                  <div class="card-footer">
                    <div class="row">
                      <div class="col-12 text-right">
                        <button type="button" class="btn btn-outline-success" onclick="stepper.next()">Siguiente<i class="fa fa-chevron-right ml-2"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div id="attention-data" class="content" role="tabpanel" aria-labelledby="attention-data-trigger">
                <div class="card card-success card-flat">
                  <div class="card-header">
                    <h3 class="card-title">
                      Anamnesis próxima
                    </h3>
                  </div>

                  <div class="card-body">
                    <div class="row">
                      <div class="col-12">
                        <label for="reason" class="require-field">Motivo de consulta</label>
                        <textarea name="reason" id="reason" class="summernote" rows="4"></textarea>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="pulse" class="require-field">Frecuencia cardíaca</label>
                        <div class="input-group">
                          <input type="text" name="pulse" id="pulse" class="form-control text-right number" autocomplete="off">
                          <div class="input-group-append">
                            <span class="input-group-text">l/m</span>
                          </div>
                        </div>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="respiration" class="require-field">Frecuencia respiratoria</label>
                        <div class="input-group">
                          <input type="text" name="respiration" id="respiration" class="form-control text-right number" autocomplete="off">
                          <div class="input-group-append">
                            <span class="input-group-text">r/m</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="temperature" class="require-field">Temperatura</label>
                        <div class="input-group">
                          <input type="text" name="temperature" id="temperature" class="form-control text-right number" autocomplete="off">
                          <div class="input-group-append">
                            <span class="input-group-text">ºC</span>
                          </div>
                        </div>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="saturation" class="require-field">Oximetría de pulso</label>
                        <div class="input-group">
                          <input type="text" name="saturation" id="saturation" class="form-control text-right number" autocomplete="off">
                          <div class="input-group-append">
                            <span class="input-group-text">%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="pressure">Presión arterial</label>
                        <div class="input-group">
                          <input type="text" name="pressure" id="pressure" class="form-control text-right" autocomplete="off">
                          <div class="input-group-append">
                            <span class="input-group-text">mm Hg</span>
                          </div>
                        </div>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="glycemia">Glicemia capilar</label>
                        <div class="input-group">
                          <input type="text" name="glycemia" id="glycemia" class="form-control text-right" autocomplete="off">
                          <div class="input-group-append">
                            <span class="input-group-text">mg/dL</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12">
                        <p class="text-gray">Los datos marcados con * son obligatorios</p>
                      </div>
                    </div>
                  </div>

                  <div class="card-footer">
                    <div class="row">
                      <div class="col-12 text-right">
                        <button type="button" class="btn btn-default text-secondary border-secondary" onclick="stepper.previous()">
                          <i class="fa fa-chevron-left mr-2"></i>Volver
                        </button>
                        <button type="submit" id="create-call" class="btn btn-lg btn-success">
                          <i class="fa fa-video mr-2"></i>Guardar y crear llamada
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="history-txt">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Historial del paciente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fad fa-times mr-2"></i>Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<?php include '../src/scripts.php' ?>
<script src="/build/js/index.js?<?php echo time() ?>"></script>
</body>
</html>
