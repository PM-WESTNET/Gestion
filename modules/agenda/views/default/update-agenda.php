<div id="agenda" class="padding-full">

    <div class="container">

        <h1><?= \app\modules\agenda\AgendaModule::t('app', 'My agenda'); ?></h1>

    </div>

    <div id="agenda" class="padding-full">
        <?php
        //Eventos
        echo \yii2fullcalendar\yii2fullcalendar::widget(array(
            'events' => $events,
            'options' => [
                'lang' => 'es',
            ]
        ));
        ?>
    </div>

    <div class="modal fade" id="task-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Actualizar tarea</h4>
                </div>
                <div class="modal-body">
                    <iframe id="task-iframe" src=""  style="overflow-x: hidden;">

                    </iframe>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
</div>