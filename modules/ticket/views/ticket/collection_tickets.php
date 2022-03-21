<?php

use webvimark\modules\UserManagement\models\User;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\ticket\TicketModule;
use yii\bootstrap\Collapse;
use kartik\widgets\DatePicker;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

\app\assets\BootBoxAsset::register($this);

$this->title = Yii::t('app', 'Cashier Manage Tickets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index padding-full">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php if (User::hasRole('collection_manager')):?>
                <?=
                Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', Yii::t('app','Close Tickets by Period')),
                    '#', ['class' => 'btn btn-warning', 'id' => 'close-all-btn'])
                ;
                ?>
            <?php endif;?>
            <?=
            Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
                        'modelClass' => 'Ticket',
                    ]), ['create'], ['class' => 'btn btn-success'])
            ;?>
        </p>
    </div>

    <?php
    Pjax::begin();

    $columns = [

        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'customer',
            'header' => TicketModule::t('app', 'Customer'),
            'value' => function($model) {
                if (!empty($model->customer)) {
                    return Html::a($model->customer->name . ' ' . $model->customer->lastname . '('. $model->customer->code .')', ['/sale/customer/view', 'id' => $model->customer_id]);
                }
            },
            'format' => 'raw',
            'contentOptions' => ['style' => ['max-width' => '250px;']],
        ],
        [
            'attribute' => 'phone',
            'header' => Yii::t('app', 'Phones'),
            'value' => function($model) {
                if (!empty($model->customer)) {

                    $phones = $model->customer->phone . ' - '. $model->customer->phone2 .
                        ' - '. $model->customer->phone3 . ' - '. $model->customer->phone4;
                    return $phones;
                }
            }
        ],
        [
            'header' => TicketModule::t('app', 'Status'),
            'attribute' => 'status_id',
            'value' => function($model) {
                return Editable::widget([
                    'model' => $model,
                    'attribute' => 'status_id',
                    'asPopover' => true,
                    'header' => Yii::t('app','Status'),
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => ArrayHelper::map($model->category->schema->statuses, 'status_id', 'name'),
                    'options' => ['class' => 'form-control status-class', 'id' => $model->ticket_id],
                    'formOptions' => ['action' => Url::toRoute(['edit-status', 'ticket_id' => $model->ticket_id])],
                    'displayValue' => $model->status->name,
                    'displayValueConfig'=> [
                        ArrayHelper::map($model->category->schema->statuses, 'status_id', 'name')
                    ],
                    'afterInput' => function($form, $widget) {
                         echo '<div class="task_date_div hidden">';
                             $widget->model->task_date = (new \DateTime('now'))->format('Y-m-d');
                             echo $form->field($widget->model, 'task_date')->widget(DatePicker::class, [
                                'options'=>['placeholder'=>'To date', 'id' => 'task-date-'.$widget->model->ticket_id],
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd'
                                ],
                            ])->label(false);
                        echo '</div>';
                    },
                ]);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'task_date',
            'value' => function($model) {
                return $model->task ? $model->task->date : '';
            }
        ],
        'title',
        [
            'label' => Yii::t('app', 'Ticket management quantity'),
            'value' => function($model) {
                return $model->getTicketManagementQuantity();
            }
        ],
        [
            'label' => Yii::t('app', 'Assignated users'),
            'value' => function($model) {
                $assignations = $model->assignations;
                $assignation_string = '';
                foreach ($assignations as $assignation) {
                    $username = ($assignation->user) ? $assignation->user->username : 'n/a';
                    $assignation_string .= $username .', ';
                }
                return $assignation_string;
            },
            'contentOptions' => ['style' => ['max-width' => '250px;']],
        ],
        [
            'header' => Yii::t('app', 'Assign to user'),
            'attribute' => 'user_id',
            'value' => function($model) {
                return Select2::widget([
                    'name' => 'new_assigned_user',
                    'data' => ArrayHelper::map(User::find()->where(['status' => 1])->all(), 'id', 'username'),
                    'options' => ['data-ticket' => $model->ticket_id , 'class' => 'select_to_assig_to_user', 'placeholder' => Yii::t('app','Select ...')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
            },
            'format' => 'raw'
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template' => '{current-account} {observations}{register-management}',
            'buttons' => [
                'observations' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-zoom-in"></span>', '#', [
                       'class' => 'btn btn-info btn-obs',
                       'title' => Yii::t('app', 'Create observation'),
                       'data' => [
                            'ticket' => $model->ticket_id
                       ]
                    ]);
                },
                'current-account' => function ($url, $model) {
                    return Html::a(Yii::t('app', 'Account'), ['/checkout/payment/current-account', 'customer' => $model->customer_id], [
                        'class' => 'btn btn-default',
                    ]);
                },
            ],
            'template' => '{current-account} {observations}'
        ],
    ];
    ?>
    <?= Collapse::widget([
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                    'content' => $this->render('_collection_filters', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'hidden-print'
            ]
        ]);
        ?>

    <div class="container-fluid no-padding no-margin">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'id' => 'grid',
            'responsive' => true,
            'hover' => true,
        ]);
        ?>
    </div>

    <?php Pjax::end(); ?>

</div>

<script>

    var Tickets = new function () {

        this.init = function () {
            $(document).on('click', '.btn-obs', function (e) {
                e.preventDefault();
                Tickets.getObservations($(this).data('ticket'));
            });

            $(document).on('click', '#add_obs_btn', function (e) {
                e.preventDefault();
                Tickets.addObservationForm($(this));
            });

            $(document).on('click', '#add_management_btn', function (e) {
                e.preventDefault();
                Tickets.addManagementForm($(this));
            });

            $(document).on('click', '#observation-submit-btn', function (e) {
                e.preventDefault();
                Tickets.createObservation()
            });

            $(document).on('click', '#management-submit-btn', function (e) {
                e.preventDefault();
                Tickets.registerTicketManagement()
            });

            $('.select_to_assig_to_user').on('change', function (e) {
                Tickets.assignTicketToUser($(this).data('ticket'), $(this).val())
            })

            $('.status-class').on('change', function() {
                Tickets.statusAllowToSetDate($(this).val());
            });
            $('.close').on('click', function () {
                $('.task_date_div').addClass('hidden');
            });
            $('.kv-editable-reset').on('click', function () {
                $('.task_date_div').addClass('hidden');
            })
            $('.kv-editable-submit').on('click', function () {
                $('.task_date_div').addClass('hidden');
            })
            $('.task_date_div').addClass('hidden');
        };

        this.statusAllowToSetDate = function(status_id) {
            $.ajax({
                url: "<?= Url::to(['status-has-event-action'])?>",
                data: {status_id: status_id},
                dataType: 'json',
                method: 'GET'
            }).done(function (response) {
                if(response.status = 'success') {
                    if(response.has_event_action) {
                        $('.task_date_div').removeClass('hidden');
                    } else {
                        $('.task_date_div').addClass('hidden');
                    }
                }
            })
        }

        this.assignTicketToUser = function(ticket_id, users_id) {
            $.ajax({
                url: "<?= Url::to(['assign-ticket-to-user'])?>",
                data: {ticket_id: ticket_id, users_id: users_id},
                dataType: 'json',
                method: 'POST'
            }).done(function (response) {
                location.reload();
            })
        }

        this.getObservations = function (id) {
            $.ajax({
                url : "<?= Url::to(['get-observations'])?>",
                data: $.param({id: id}),
                dataType: 'json',
            }).done(function (response) {
                bootbox.dialog ({
                    title: '<h3>'+response.title+'</h3>',
                    size: 'large',
                    message: response.observations
                });
            });
        };

        this.addObservationForm = function (btn) {
            bootbox.hideAll();
            $.ajax({
                url : "<?= Url::to(['get-observation-form'])?>",
                data: $.param({ticket_id: $(btn).data('ticket')}),
                dataType: 'json',
            }).done(function (response) {
                bootbox.dialog ({
                    title: '<h3><?= Yii::t('app','Create Observation')?></h3>',
                    size: 'large',
                    message: response.form
                });
            })
        };

        this.addManagementForm = function (btn) {
            bootbox.hideAll();
            $.ajax({
                url : "<?= Url::to(['get-management-form'])?>",
                data: $.param({ticket_id: $(btn).data('ticket'), observation_id: $(btn).data('observation')}),
                dataType: 'json',
            }).done(function (response) {
                bootbox.dialog ({
                    title: '<h3><?= Yii::t('app','Register ticket management')?></h3>',
                    size: 'large',
                    message: response.form
                });
            })
        };

        this.createObservation = function () {
            var ticket_id = $('#observation-ticket_id').val();
            $.ajax({
                url: '<?= Url::to(['/ticket/observation/create'])?>',
                method: 'POST',
                data: $('#observation-form').serializeArray(),
                dataType: 'json',
                success: function(data){
                    if(data.status === 'success') {
                        bootbox.hideAll();
                        Tickets.getObservations(ticket_id);
                    }
                }
            });
        }

        this.registerTicketManagement = function () {
            var ticket_id = $('#ticketmanagement-ticket_id').val();
            $.ajax({
                url: '<?= Url::to(['/ticket/ticket-management/register-ticket-management'])?>',
                method: 'POST',
                data: $('#management-form').serializeArray(),
                dataType: 'json',
                success: function(data){
                    if(data.status === 'success') {
                        console.log('asdada');
                        bootbox.hideAll();
                        Tickets.getObservations(ticket_id);
                    }
                }
            });
        }
    };

</script>

<?php $this->registerJs('Tickets.init()')?>

<?php if (User::hasRole('collection_manager')):?>
    <script>

        var CollectionManager = new function() {
            this.init = function () {
                $(document).on('click', '#close-all-btn', function(e){
                    e.preventDefault();
                    bootbox.dialog({
                        title: "<h4><?php echo Yii::t('app','Close Tickets by Period')?></h4>",
                        className: 'close-modal',
                        message:
                            '<form id="close-form" action="<?php echo Url::to(['/ticket/ticket/close-collection-tickets-by-period'])?>" method="get">'+
                                '<input type="hidden" name="r" value="/ticket/ticket/close-collection-tickets-by-period"' +
                            '<div class="row">'+
                                '<div class="col-lg-12">' +
                                '<div="form-group">' +
                                    '<label>'+ "<?php echo Yii::t('app','From Date')?>" +'</label>'+
                                    '<?php echo DatePicker::widget([
                                        'name' => 'TicketSearch[close_to_date]',
                                        'id' => 'close_from_date',
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-mm-yyyy'
                                        ],
                                        'options' => ['class' => 'datepicker']
                                    ])?>'+
                                 '</div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="row">'+
                            '<div class="col-lg-12">' +
                                 '<div="form-group">' +
                                    '<label>'+ "<?php echo Yii::t('app','To Date')?>" +'</label>'+
                                    '<?php echo DatePicker::widget([
                                        'name' => 'TicketSearch[close_to_date]',
                                        'id' => 'close_to_date',
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy'
                                        ],
                                        'options' => ['class' => 'datepicker']
                                    ])?>'+
                                 '</div>'+
                            '</div>'+
                            '</div>'+
                            '</form>',
                        buttons: {
                            close: {
                                label: "<?php echo Yii::t('app','Close Tickets')?>",
                                className: 'btn btn-primary',
                                callback: function() {
                                    $('#close-form').trigger('submit');
                                }
                            }
                        }
                    }).on('shown.bs.modal', function () {
                        $(".datepicker").kvDatepicker({
                            format: 'dd-mm-yyyy',
                            language: 'es'
                        });
                        $('.close-modal').removeAttr('tabindex');
                    });
                })
            }
        }

    </script>

    <?php $this->registerJs('CollectionManager.init()')?>
<?php endif;?>