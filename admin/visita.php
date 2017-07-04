<?php
require_once 'functions.php';
$id = i('id');
$q = array(
            'path'=>'main',
            'screen'=>'loggedin',
            'classes' => 'hideSearch',
            'grandparent'=>'<a href="'.dir.'/admin/visitas.php">Visitas</a>',
            'parent'     => false,
            'title'=>'Visita (Negocio #'.$id.')'
           );
admin_filter($q, $_COOKIE);
$current_user = get_current_admin($_COOKIE);
$role = (int) $current_user['role'];

get_header($q,'admin');
get_menu($q,'admin');
$visitas = new visitas;
$v = $visitas->get_visitas_from_microempresario($id, 'fecha_hora_inicio', 'ASC');
$m = new microempresario($id);
$mi = $m->info;
$hrs_ = 0;
$hours = array();
$labels = array();
$lat = array();
$lng = array();
$llng = array();
$hour = $hrs;
foreach($v as $visita){
  $k = sdate($visita['fecha_hora_inicio'], false);
  $labels[] = '"'.$k.'"';
  $hours[] = $visita['time'];
  $hrs_ += $visita['time'];
  $lat_ = ($visita['latitud_inicio'] + $visita['latitud_final']) / 2;
  $lng_ = ($visita['longitud_inicio'] + $visita['longitud_final']) / 2;
  $lat[] = $lat_;
  $lng[] = $lng_;
  $llng[] = '{lat: '.$lat_.', lng:'.$lng_.'}';

}
$jsLabel = '['.implode($labels, ',').']';
$jsDate = '['.implode($hours,',').']';

$latCenter = 0;
$lngCenter = 0;
$latLngX = 0;
foreach($lat as $lt){
  $latCenter = $lt;
}
foreach($lng as $ln){
  $lngCenter = $ln;
}



$entregables = get_micro_entregables($id);
$urlPhotos = 'http://www.descifrainadem.mx/AplicacionMovil/uploads/';
$pic1 = $mi->pic1 ? filterpic($mi->pic1) : false;
$pic2 = $mi->pic2 ? filterpic($mi->pic2) : false;
$isokconv = $entregables['isokconv'];

$reporte = $mi->existeReportePDF ? true : false;
$reporteURL = 'http://52.161.23.253/ReporteINADEM/Reportes/INADEM_'.$id.'.pdf';
$reporte = UR_exists($reporteURL);
?>

<div class="binder">
  <div class="the_content" id="visita_single">
    <input id="idnegocio" type="hidden" value="<?= $id;?>">



    <section>

      <div class="bigmeta">

        <div class="data">
          <div class="cypher">
            <?= $entregables['formattedTime'];?>
          </div>
          <div class="title">
            Total de tiempo en visita
          </div>
        </div>

        <div class="data">
          <div class="cypher">
            <?= count($v);?>
          </div>
          <div class="title">
            Total de sesiones
          </div>
        </div>

        <div class="data">
          <div class="cypher">
            <?= $entregables['formattedEnt'];?>
          </div>
          <div class="title">
            Entregables
          </div>
        </div>


        <div class="data">
          <div class="cypher">
            <?= i('timeFalta', $entregables) ? hrs(6 - $entregables['rawTime']) :  '-';?>
          </div>
          <div class="title">
            Tiempo Restante
          </div>
        </div>

        <div class="table visita-table">

          <div class="th tr">
            <div class="td">
              ID Sesión
            </div>
            <div class="td">
              Asesor
            </div>
            <div class="td td-right">
              Inicio
            </div>
            <div class="td td-right">
              Duración
            </div>
            <div class="td td-right">

            </div>
          </div>

          <?php
          foreach($v as $vis):
            $hr_v = $visitas->get_visita_timetotal($vis['id_visita']);
          ?>
          <div class="tc tr">
            <div class="td">
              #<?= $vis['id_visita'];?>
            </div>
            <div class="td">
              <?= get_asesor_name($vis['id_asesor']);?>
            </div>
            <div class="td td-right">
              <?= pdate($vis['fecha_hora_inicio'], false);?>
            </div>
            <div class="td td-right">
              <?= hrs($hr_v);?>
            </div>
            <div class="td td-right">
              <a href="<?= admin.'/asesor.php?id='.$vis['id_asesor'];?>" target="_blank">
                Asesor
              </a>
              &nbsp; &nbsp;
              <a target="_blank" href="<?= admin.'/visita-single.php?idparent='.$id.'&id='.$vis['id_visita'];?>">
                Ver
              </a>
            </div>
          </div>
          <?php endforeach;?>


        </div>




      </div>

      <div class="vs-group">
        <div class="visita-single-title">
          Entregables
        </div>

        <?php if(count($entregables['falta'])):?>
        <div class="pent perror">
          <div class="title">
            Faltan los siguientes entregables:
          </div>
          <div class="ul">
            <?php foreach($entregables['falta'] as $falta):?>
              <div class="li">
                &bull; <?= $falta;?>
              </div>
            <?php endforeach;?>
          </div>
        </div>
        <?php else:?>
        <div class="pent psuccess">
          <div class="title">
            Se ha cumplido con todos los entregables.
          </div>
        </div>
        <?php endif;?>


        <div id="entregables-table">

          <?php if($reporte):?>
            <a href="<?= $reporteURL;?>" target="_blank">
              <div class="btn">
                Descargar reporte de micromercado
              </div>
            </a>
          <?php endif;?>

          <?php if($isokconv):?>
            <div class="addhours">
              <form action="ajax.php" method="post">

              <span>Añadir</span>
              <select id="hours" name="hours">
                <?php
                  for($i = 1; $i<=6; $i++):
                    $y = $i - 1 + .5;
                ?>
                <option value="<?= $y;?>">
                  <?= $y;?>
                </option>
                <option value="<?= $i;?>">
                  <?= $i;?>
                </option>
                <?php endfor;?>
              </select>
              <span>horas</span>

              <button type="submit">Añadir</button>
              <input type="hidden" name="id" value="<?= $id;?>">
              <input type="hidden" name="action" value="addfalsehours">
            </form>

            </div>
          <?php endif;?>


          <div class="tr th">
            <div class="td">
              Nombre del entregable
            </div>
            <div class="td">
              Contenido
            </div>
            <div class="td  td-right">
              ¿Es válido?
            </div>
          </div>

          <div class="tr trdivider">
              Análisis
          </div>

          <div class="tr tc">
            <div class="td">
              Plan de acción
            </div>

            <div class="td">
              <div class="td-content">
                <textarea <?= $role !== 3 ? 'disabled': '';?> data-to="analisis_plan_accion"><?= san($mi->analisis_plan_accion);?></textarea>
              </div>
            </div>

            <div class="td td-right">
                <div class="toggle">
                  <input <?= $mi->analisis_plan_accion &&  $mi->analisis_plan_accion_status ? 'checked' : '';?> type="checkbox" class="toggle-entregable"  data-group="analisis_plan_accion_status" value="<?= $mi->usuarioid;?>">
                </div>
            </div>

          </div>

          <div class="tr tc">
            <div class="td">
              Análisis financiero
            </div>

            <div class="td">
              <div class="td-content">
                <textarea <?= $role !== 3 ? 'disabled': '';?> data-to="analisis_financiamiento"><?= san($mi->analisis_financiamiento);?></textarea>
              </div>
            </div>

            <div class="td   td-right">
                <div class="toggle">
                  <input <?= $mi->analisis_financiamiento &&  $mi->analisis_financiamiento_status ? 'checked' : '';?> type="checkbox" class="toggle-entregable"  data-group="analisis_financiamiento_status" value="<?= $mi->usuarioid;?>">
                </div>
            </div>

          </div>

          <div class="tr tc">
            <div class="td">
              Análisis micromercado
            </div>

            <div class="td">
              <div class="td-content">
                <textarea <?= $role !== 3 ? 'disabled': '';?> data-to="analisis"><?= san($mi->analisis);?></textarea>
              </div>
            </div>

            <div class="td  td-right">
                <div class="toggle">
                  <input <?= $mi->analisis &&  $mi->analisis_status ? 'checked' : '';?> type="checkbox" class="toggle-entregable"  data-group="analisis_status" value="<?= $mi->usuarioid;?>">
                </div>
            </div>

          </div>
          <?php if($pic1 || $pic2):?>
          <div class="tr trdivider">
            Fotos
          </div>
          <div class="tr tc">
            <div class="td">
            </div>

            <div class="td td-photos">
              <?php if($pic1):?>
              <div class="td-photo">
                <a href="javascript:void(0)" data-featherlight="<?= $pic1;?>">
                  <div class="filler"  style="background-image: url(<?= $pic1;?>)">

                  </div>
                </a>

              </div>
            <?php endif;?>
            <?php if($pic2):?>

              <div class="td-photo" >
                <a href="javascript:void(0)" data-featherlight="<?= $pic2;?>">
                  <div class="filler"  style="background-image: url(<?= $pic2;?>)">

                  </div>
                </a>
              </div>
            <?php endif;?>
            </div>

            <div class="td  td-right">

            </div>

          </div>
        <?php endif;?>


        </div>
      </div>


      <div class="vs-group">
        <div class="visita-single-title">
          Datos del microempresario
        </div>
        <div class="visita-metas">

        <?php
          $mdata = array(
            'Nombre'           => 'nombre',
            'Convocatoria'     => 'convocatoria',
            'Año Convocatoria' => 'convocatoria_year',
            'Persona Fiscal'   => 'tipoNegocio',
            'Edad'             => 'edad',
            'Teléfono'         => 'numero_telefonico',
            'Sexo'             => 'sexo',
            'RFC'              => 'rfc',
            'Giro'             => 'giro',
            'Nivel Estudios'   => 'estudios_usu',
            'Años de experiencia' => 'expe_anios'
          );
          foreach($mdata as $name => $key):
         ?>
         <div class="vm-tr">
           <div class="vm-td vm-th">
             <?= $name;?>
           </div>
           <div class="vm-td vm-tc">
             <?= san($mi->$key);?>
           </div>
         </div>
        <?php endforeach;?>


        <div class="visita-metas">

        <?php
          $mdata = array(
            'Cuenta Bancaria Negocio' => 'cuenta_bancaria',
            'SAT Negocio'             => 'firma_sat_negocio'
          );
          foreach($mdata as $name => $key):
         ?>
         <div class="vm-tr">
           <div class="vm-td vm-th">
             <?= $name;?>
           </div>
           <div class="vm-td vm-tc">
             <?= ($mi->key ? 'Sí' : 'No');?>
           </div>
         </div>
        <?php endforeach;?>

        <!-- GIRO -->




        <div class="vm-tr">
          <div class="vm-td vm-th">
            Dado de alta
          </div>
          <div class="vm-td vm-tc">
            <?= pdate($mi->fechaalta, false);?>
          </div>
        </div>
        <div class="vm-tr">
          <div class="vm-td vm-th">
            Última vez que se visitó al negocio
          </div>
          <div class="vm-td vm-tc">
            <?= $visitas->get_last_visita($id);?>
          </div>
        </div>

      </div>
      </div>

      <div class="maps">
        <div class="maps-name visita-single-title">
          Sesiones
        </div>
        <div id="map">

        </div>
      </div>

      <script type="text/javascript">

      var map;
      function initMap() {
        var latLng = {lat: <?= $latCenter;?>, lng: <?= $lngCenter;?>};
        map = new google.maps.Map(document.getElementById('map'), {
          center: latLng,
          scrollwheel: false,
          zoom: 16
        });
        <?php foreach ($llng as $latlang):?>
        var marker = new google.maps.Marker({
          position: <?= $latlang;?>,
          map: map,
        });
        <?php endforeach;?>
      }

    </script>
    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoBnelmi80gWbqLelL1j2G84Y5B84HKxE&callback=initMap">
    </script>


      <?php if(1 < count($v)):?>
      <div class="grapher-container">
        <div class="grapher-title">
          Gráfica de sesiones
        </div>
      <canvas id="visitas-graph" class="graphers"></canvas>
      </div>

      <script type="text/javascript">
        $(function(){

          var ctx = $("#visitas-graph");
          var data = {
              labels: <?= $jsLabel;?>,
              datasets: [
                {
                  label: 'Horas de visita',
                  data: <?= $jsDate;?>,
                  backgroundColor: 'rgba(114,191,68,0.2)',
                  borderColor: 'rgba(114,191,68,0.3)',
                  fill: true,
                  borderWidth: 2
              }
            ]
          }
          var chartInstance = new Chart(ctx, {
          type: 'line',
          data: data,
          options: {
            responsive: true,
            labelFontSize: 40,
            hover: {
                 // Overrides the global setting
                 mode: 'index'
             },
             scales: {
               xAxes: [{
                      ticks: {
                          beginAtZero:true
                      }
                    }],
                    yAxes: [{
                           ticks: {
                               beginAtZero:true
                           }
                         }]

             }
          }
        });





        })


      </script>
    <?php endif;?>


    </section>




  </div>
</div>






<?php get_footer($q, 'admin');?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
