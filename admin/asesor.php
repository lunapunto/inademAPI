<?php
require_once 'functions.php';
$q = array(
            'path'=>'main',
            'screen'=>'loggedin',
            'classes' => 'hideSearch',

            'grandparent'=>'<a href="'.dir.'/asesores.php">Asesores</a>',
            'parent'     => false,
            'title'=>'Asesor'
           );
admin_filter($q, $_COOKIE);
get_header($q,'admin');
get_menu($q,'admin');
$id = i('id');
$aclass = new asesores;
$asesor = $aclass->get_single($id);
$visitas = new visitas;
$v = $visitas->get_visitas_from_asesor($id);
$lngCenter = 0;
$latCenter = 0;
$latLngX = 0;
$lats = array();
$lngs = array();
$dates = array();
$hrs = array();
?>


<div class="binder">
  <div class="the_content">

    <header>
      <div class="title">
        Asesor
      </div>
      <div class="subtitle">
      <?= san($asesor->nombre);?>
      </div>
    </header>


    <section>

      <div class="bigmeta">

        <div class="data">
          <div class="cypher">
            <?= hrs($asesor->time);?>
          </div>
          <div class="title">
            Total de tiempo en sesiones
          </div>
        </div>

        <div class="data">
          <div class="cypher">
            <?= $asesor->visitas;?>
          </div>
          <div class="title">
            Total de sesiones
          </div>
        </div>



        <div class="table visita-table">

          <div class="th tr">
            <div class="td">
              ID Sesión
            </div>
            <div class="td">
              Negocio
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
            $latCenter += $vis['latitud_final'];
            $lngCenter += $vis['longitud_final'];
            $lats[] = $vis['latitud_final'];
            $lngs[] = $vis['longitud_final'];
            $dates[] = sdate($vis['fecha_hora_inicio']);
            $hrs[] = $hr_v;
            $latLngX ++;
          ?>
          <div class="tc tr">
            <div class="td">
              #<?= $vis['id_visita'];?>
            </div>
            <div class="td">
              <?= get_microempresario_name($vis['id_negocio']);?>
            </div>
            <div class="td td-right">
              <?= pdate($vis['fecha_hora_inicio'], false);?>
            </div>
            <div class="td td-right">
              <?= hrs($hr_v);?>
            </div>
            <div class="td td-right">
              <a href="<?= admin.'/visita.php?id='.$vis['id_negocio'];?>">
                Negocio
              </a>
              &nbsp; &nbsp;
              <a href="<?= admin.'/visita-single.php?idparent='.$id.'&asesor=ok&id='.$vis['id_visita'];?>">
                Ver
              </a>
            </div>
          </div>
          <?php endforeach;?>


        </div>




      </div>


      <div class="visita-metas">

        <div class="vm-tr">
          <div class="vm-td vm-th">
            Última vez que visitó a un negocio
          </div>
          <div class="vm-td vm-tc">
            <?= pdate($visitas->get_asesor_last_visita($id), false);?>
          </div>
        </div>

      </div>

      <div class="maps">
        <div class="maps-name">
          Ubicación de las visitas
        </div>
        <div id="map">

        </div>
      </div>

      <script type="text/javascript">

      var map;
      function initMap() {
        var latLng = {lat: <?= $lats[0]?>, lng: <?= $lngs[0];?>};
        map = new google.maps.Map(document.getElementById('map'), {
          center: latLng,
          scrollwheel: false,
          zoom: 13
        });
        <?php for($l = 0; $l<$latLngX; $l++ ):?>
        var marker = new google.maps.Marker({
          position: {lat: <?= $lats[$l];?>, lng: <?= $lngs[$l];?>},
          map: map,
        });
        <?php endfor;?>
      }

    </script>
    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoBnelmi80gWbqLelL1j2G84Y5B84HKxE&callback=initMap">
    </script>


      <div class="grapher-container">
        <div class="grapher-title">
          Gráfica de visitas
        </div>
      <canvas id="visitas-graph" class="graphers"></canvas>
      </div>

      <script type="text/javascript">
        $(function(){

          var ctx = $("#visitas-graph");
          var data = {
              labels: <?= json_encode($dates);?>,
              datasets: [
                {
                  label: 'Tiempo de visita',
                  data:  <?= json_encode($hrs);?>,
                  backgroundColor: 'rgba(13, 31, 48, 0.5)',
                  borderColor: 'rgba(13, 31, 48, 0.6)',
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


    </section>




  </div>
</div>






<?php get_footer($q, 'admin');?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
