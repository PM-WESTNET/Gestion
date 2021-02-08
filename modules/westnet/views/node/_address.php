<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\zone\models\Zone;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */
?>
<div id="address" class="panel panel-default">
    <div class="panel-body">

        <div class="row">

            <div class="col-xs-12">
                
                <?php if(Yii::$app->params['map_address'] && !isset($hideMap) || $hideMap == false):?>
                <div class="row">
                    <div class="col-xs-12">
                            <?= $form->field($model, 'geocode')->textInput(['value' => (empty($model->geocode) ? "-32.8988839,-68.8194614" : $model->geocode)])  ?>
                    </div>
                    <div class="col-xs-6">
                            <?= Html::a('Localizar en Mapa',  null, ['class' => 'btn btn-success', 'id' => 'localize']) ;?>
                    </div>
                    <div class="col-xs-6">
                        <?= Html::a('Pegar URL',  null, ['class' => 'btn btn-success', 'id' => 'paste-url']) ;?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="map_canvas" style="width:100%; height:300px"></div>
                    </div>  
                </div>    
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<div id="url_dialog" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="url-modal-title">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        Ingresa la URL del mapa.
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <input type="text" name="url_mapa" id="url_mapa" style="width: 100%" />
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-default" id="btn-parse-url">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyAftuVHJbcAg7ugSH2nbY80VKUmH_ocE5Y"></script>

<?php if(!isset($hideMap) || $hideMap == false): ?>
<script type="text/javascript">
    var NodeMap = new function() {
        var self=this;
        var marker;
        var zones=<?= json_encode(ArrayHelper::map(Zone::find()->all(), 'zone_id', 'name' ))?>;
        <?php
        $lat = '{ lat: -32.8988839, lng: -68.8194614 }';
        if($model->geocode) {
            $lt = explode(',', ($model->geocode==NULL ? "-32.8988839,-68.8194614": $model->geocode));
            if(count($lt)==2) {
                $lat = "{ lat:".$lt[0].",lng:".$lt[1] . '}';
            }
        }
        ?>
        var lat= <?php echo $lat ?>;
        var map;
        this.init=function() {
            
            NodeMap.createMap();

            $(document).ready(function(){
                NodeMap.getZoneByAjax($('[name="Address[zone_id]"]').val(), $('[name="Address[zone_id]"]'));
            });

            $(document).off('click', '#paste-url').on('click', '#paste-url', function(){
                NodeMap.showPasteUrl();
            });

            $(document).off('click', '#btn-parse-url').on('click', '#btn-parse-url', function(evt){
                if($('#url_mapa').val()!='') {
                    $('#url_dialog').modal('hide');
                    NodeMap.parseUrl($('#url_mapa').val());
                }
            });

            $(document).off('keypress', '#url_mapa').on('keypress', '#url_mapa', function(evt){
                if( evt.which == 13 ) {
                    evt.preventDefault();
                }
            });
        }

        this.updateZoneSelected=function(){
            $('[name="Address[zone_id]"]').on('change', function(){
                var self=$(this);
                var zones_id=$(this).val();
                if(zones_id!=""){
                    NodeMap.getZoneByAjax(zones_id, self);
               }
            });
            
        }
        
        this.getZoneByAjax=function(zones_id,object){
            $.ajax({
               url:'<?= yii\helpers\Url::to(['/zone/zone/full-zone']) ?>',
               type:'post',
               data:{zone_id:zones_id},
               dataType:'json',
               beforeSend:function(){

               }   
           }).done(function(response){
               if(response.status=='success'){
                  var fullzone= response.fullzone;
                  object.attr('data-value',fullzone);
                }
           });
        }

        this.updateGeoposicion=function(latLng) {
            if (typeof latLng.lat == "function" ) {
                $('#node-geocode').val([latLng.lat(), latLng.lng()].join(','));
            } else {
                $('#node-geocode').val([latLng.lat, latLng.lng].join(','));
            }
        }
        
        this.geocodeAddress=function(map, address,marker) {
           
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': address}, function(results, status) {
              if (status === google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                NodeMap.updateGeoposicion(marker.getPosition());
                google.maps.event.addListener(map, 'center_changed', function() {
                    NodeMap.updateGeoposicion(marker.getPosition());
                });
              } else {
                alert('No se encontraron resultados para la dirección ingresada, pruebe localizarlo manualmente');
              }
            });

        }
        
        this.createMap = function(){
            map = new google.maps.Map(document.getElementById('map_canvas'), {
              center: lat,
              scrollwheel: true,
              zoom: 17,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            });      

            marker = new google.maps.Marker({
                position: lat,
                title: 'Direccion',
                map: map,
                draggable: true
            });

            $(document).on('click','#localize', function() {
                 self.updateZoneSelected();

                 var rawTown = $('#select2-address-zone_id-container').attr('title');
                 var townParts = rawTown.split(', ');

                 var town = '';
                 if (townParts.length > 2){
                     town = townParts[townParts.length - 3]
                 }
                 if (townParts.length > 1){
                     town = town + ', ' + townParts[townParts.length - 2]
                 }
                 if (townParts.length > 0){
                     town = town + ', ' + townParts[townParts.length - 1]
                 }

                 var address= $('[name="Address[street]"]').val() +' '+ $('[name="Address[number]"]').val()+', '+ town ;
                 NodeMap.geocodeAddress(map,address, marker);
            });
            
            google.maps.event.addListener(marker, 'position_changed', function() {
                NodeMap.updateGeoposicion(marker.getPosition());
            });
        }

        this.showPasteUrl = function() {
            $('#url_mapa').val('');
            $('#url_dialog').modal('show');
        }

        this.parseUrl = function(url) {
            var positionParsed = null;
            var position = [];

            url = url.replace("%2C", ",").replace("%20", "");

            var data = parseUrlArray(url);
            if(data && data.length >= 2) {
                if(data.length == 2) {
                    data = data[1][0][1];
                } else {
                    if(data[2][0].length == 2) {
                        data = data[2][0][1];
                    } else {
                        data = data[2][0][2];
                    }
                }
                positionParsed = data.join(',');
            } else {
                data = url.match("q=(.*)");
                data = (data[1]).split("&");
                positionParsed = data[0];
            }
            if(positionParsed) {
                position = positionParsed.split(",");
            }

            if(position.length == 2) {
                $("#node-geocode").val(positionParsed);
                var lng = new google.maps.LatLng( parseFloat(position[0]), parseFloat(position[1]));

                marker.setPosition( lng );
                map.panTo( lng );

                $('#url_dialog').modal('hide');
            }
        }

        function parseUrlArray(url) {
            var parts = url.split('!').filter(function(s) { return s.length > 0; }),
                root = [],                      // Root elemet
                curr = root,                    // Current array element being appended to
                m_stack = [root,],              // Stack of "m" elements
                m_count = [parts.length,];      // Number of elements to put under each level

            parts.forEach(function(el) {
                var kind = el.substr(1, 1),
                    value = el.substr(2);

                // Decrement all the m_counts
                for (var i = 0; i < m_count.length; i++) {
                    m_count[i]--;
                }

                if (kind === 'm') {            // Add a new array to capture coming values
                    var new_arr = [];
                    m_count.push(value);
                    curr.push(new_arr);
                    m_stack.push(new_arr);
                    curr = new_arr;
                }
                else {
                    if (kind == 'b') {                                    // Assuming these are boolean
                        curr.push(value == '1');
                    }
                    else if (kind == 'd' || kind == 'f') {                // Float or double
                        curr.push(parseFloat(value));
                    }
                    else if (kind == 'i' || kind == 'u' || kind == 'e') { // Integer, unsigned or enum as int
                        curr.push(parseInt(value));
                    }
                    else {                                                // Store anything else as a string
                        curr.push(value);
                    }
                }

                // Pop off all the arrays that have their values already
                while (m_count[m_count.length - 1] === 0) {
                    m_stack.pop();
                    m_count.pop();
                    curr = m_stack[m_stack.length - 1];
                }
            });
            return root;
        }
    }
</script>

<?php $this->registerJs('NodeMap.init();') ?>

<?php endif; ?>
