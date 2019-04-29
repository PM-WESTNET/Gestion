<!-- Collector info -->
<h4>
    <?= app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Collector found!'); ?>
</h4>
<ul class="list-group no-margin-bottom">
    <li class="list-group-item">
        <span class="badge"><?= $model->number; ?></span>
        <strong class="text-primary"><?= $model->getAttributeLabel("number"); ?></strong>
    </li>
    <li class="list-group-item">
        <span class="badge"><?= $model->name; ?> <?= $model->lastname; ?></span>
        <strong class="text-primary"><?= $model->getAttributeLabel("name"); ?></strong>
    </li>
    <li class="list-group-item">
        <span class="badge"><?= $model->document_type; ?> <?= $model->document_number; ?></span>
        <strong class="text-primary"><?= $model->getAttributeLabel("document_number"); ?></strong>
    </li>
</ul>
<!-- end Collector info -->