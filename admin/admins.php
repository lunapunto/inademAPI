<?php
require_once 'functions.php';
$q = array(
            'path'=>'main',
            'screen'=>'loggedin',
            'grandparent'=>false,
            'parent'     => false,
            'title'=>'Administradores'
           );
admin_filter($q, $_COOKIE);
$current_user = get_current_admin($_COOKIE);
$role = (int) $current_user['role'];
get_header($q,'admin');
get_menu($q,'admin');

?>

<div class="binder">
  <div class="the_content">
    <section>
      <div id="jsGrid">

      </div>
    </section>
  </div>
</div>

<script>
    var url = "<?= dir.'/admin/fetchAdmins.php';?>";
    $.ajax({
        type: "GET",
        url: url
    }).done(function(clients) {

    $("#jsGrid").jsGrid({
        width: "100%",
        height: "auto",
        pagerFormat: "Páginas: {first} {prev} {pages} {next} {last}    {pageIndex} de {pageCount}",
        pagePrevText: "Anterior",
        pageNextText: "Siguiente",
        pageFirstText: "Primera",
        pageLastText: "Última",
        pageNavigatorNextText: "...",
        pageNavigatorPrevText: "...",
        noDataContent: "Sin Resultados",
        autoload: true,
        filtering: true,
        editing: <?= $role !== 3 ? 'false': 'true';?>,
        sorting: true,
        inserting: true,
        paging: true,

        loadIndication: true,
        loadIndicationDelay: 500,
        loadMessage: "Cargando datos...",
        loadShading: false,


        data: clients,
        rowClick: function(args) { return;},
        controller: {
                loadData: function(filter) {
                   return $.ajax({
                        type: "GET",
                        url: url,
                        data: filter,
                    });

                },
                insertItem: function(item) {
                  item.type = 'insert';
                     $.ajax({
                        type: "POST",
                        url: url,
                        data: item
                    }).done(function(msg){
                      window.location.href = window.location.href;
                    });;
                },
                updateItem: function(item) {
                  item.type = 'update';
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: item
                    }).done(function(msg){
                      console.log(this.url);
                    });
                },
                deleteItem: function(item) {
                  item.type = 'delete';
                    return $.ajax({
                        type: "POST",
                        url: url,
                        data: item
                    }).done(function(msg){
                      console.log(msg);
                      alertS('Usuario eliminado', '');
                    }).always(function(msg){
                      console.log(msg);
                    });
                }
            },
        fields: [
            { name: "id",width:80, title: "ID Asesor", inserting: false,type: "number",  filtering: true, editing: false},
            { name: "nombre", width: 140,title: "Nombre",type: "text"},
            { name: "email",width: 180, title: "Correo electrónico", type: "text"},
            { name: "status", title: "Status", type: "checkbox", filtering: false, editable: <?= $role !== 3 ? 'false': 'true';?>},
            { name: "role", title: "Permisos", type: "select", editable: <?= $role !== 3 ? 'false': 'true';?>, filtering: false, items: [
                 { Name: "Lector", Id: 1 },
                 { Name: "Moderador", Id: 2 },
                 { Name: "Administrador", Id: 3 }
            ], valueField: "Id", textField: "Name"},
            {
                type: "control",
                width: 130,
                editButton: <?= $role < 2 ? 'false': 'true';?>,
                deleteButton: <?= $role !== 3 ? 'false': 'true';?>,
                headerTemplate: function() {
                  return '';
                 },


            }

        ]
    });

  });


</script>



<?php get_footer($q, 'admin');?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
