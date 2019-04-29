<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\DocumentType;
use app\modules\sale\models\TaxCondition;
use app\modules\sale\models\CustomerClass;
use app\modules\sale\models\CustomerCategory;
use app\modules\zone\models\Zone;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Customer $model
 * @var yii\widgets\ActiveForm $form
 */
?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyAftuVHJbcAg7ugSH2nbY80VKUmH_ocE5Y"></script>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('app','Address'); ?></h3>
    </div>
    <div class="panel-body">

        <div class="row">

            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($address, 'zone_id')->widget(Select2::className(),[
                            'data' => ArrayHelper::map(Zone::getForSelect(), 'zone_id', 'name' ),
                            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                            'pluginOptions' => [
                                'allowClear' => true
                                ]
                            ]);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-9">
                        <?= $form->field($address, 'street')->textInput(['maxlength' => 100]) ?>
                    </div>
                    <div class="col-xs-3">
                        <?= $form->field($address, 'number')->textInput() ?>
                    </div>    
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <?= $form->field($address, 'between_street_1')->textInput(['maxlength' => 100]) ?>
                    </div>
                    <div class="col-xs-6">
                        <?= $form->field($address, 'between_street_2')->textInput(['maxlength' => 100]) ?>
                    </div>    
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <?= $form->field($address, 'block')->textInput(['maxlength' => 45]) ?>
                    </div>
                    <div class="col-xs-6">
                        <?= $form->field($address, 'house')->textInput(['maxlength' => 45]) ?>
                    </div>    
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <?= $form->field($address, 'tower')->textInput(['maxlength' => 45]) ?>
                    </div>
                    <div class="col-xs-4">
                        <?= $form->field($address, 'floor')->textInput() ?>
                    </div>    
                    <div class="col-xs-4">
                        <?= $form->field($address, 'department')->textInput(['maxlength' => 45]) ?>
                    </div>    
                    
                </div>
                <?php if(Yii::$app->params['map_address']):?>
                <div class="row">
                    <div class="col-xs-12">
                            <?= $form->field($address, 'geocode')->textInput(['maxlength' => 45]) ?>
                    </div>
                    <div class="col-xs-12" id="localize">
                            <?= Html::a('Localizar en Mapa',  null, ['class' => 'btn btn-success']) ;?>
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




<script type="text/javascript">

    var CustomerMap = new function() {
        var self=this;
        var marker;
        var zones=<?= json_encode(ArrayHelper::map(Zone::find()->all(), 'zone_id', 'name' ))?>;
        this.init=function() {
            var lat = {<?php $lt = explode(",", ($address->geocode=="" ? "-34.66352,-68.35941,17": $address->geocode)); echo "lat:".$lt[0].", lng:".$lt[1]; ?>};
            var map = new google.maps.Map(document.getElementById('map_canvas'), {
              center: lat,
              scrollwheel: false,
              zoom: 17,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            });      

            marker = new google.maps.Marker({
                position: lat,
                title: 'Direccion',
                map: map,
                draggable: true
            });
            document.getElementById('localize').addEventListener('click', function() {
                 self.updateZoneSelected();
                 var address= $('[name="Address[street]"]').val() +' '+ $('[name="Address[number]"]').val()+' '+$('[name="Address[zone_id]"]').attr('data-value') ;
                 console.log(address);
                 CustomerMap.geocodeAddress(map,address,marker);
            });

            CustomerMap.updateGeoposicion(lat);
            self.updateZoneSelected();
            google.maps.event.addListener(marker, 'drag', function() {
                CustomerMap.updateGeoposicion(marker.getPosition());
            });
            $(document).ready(function(){
                CustomerMap.getZoneByAjax($('[name="Address[zone_id]"]').val(), $('[name="Address[zone_id]"]'));
            });   
            
        }

        this.updateZoneSelected=function(){
            //console.log(zones);
            $('[name="Address[zone_id]"]').on('change', function(){
                var self=$(this);
                var zones_id=$(this).val();
                if(zones_id!=""){
                    CustomerMap.getZoneByAjax(zones_id, self);
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
                $('#address-geocode').val([latLng.lat(), latLng.lng()].join(', '));
            } else {
                $('#address-geocode').val([latLng.lat, latLng.lng].join(', '));
            }
        }
        
        this.geocodeAddress=function(map, address,marker) {
           
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': address}, function(results, status) {
              if (status === google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                CustomerMap.updateGeoposicion(marker.getPosition());
                google.maps.event.addListener(marker, 'drag', function() {
                    CustomerMap.updateGeoposicion(marker.getPosition());
                });
              } else {
                alert('No se encontraron resultados para la dirección ingresada, pruebe localizarlo manualmente');
              }
            });

        }
    }
</script>
<?php $this->registerJs('CustomerMap.init();') ?>