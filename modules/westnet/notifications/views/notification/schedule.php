<!-- Schedule -->
<?php if (!empty($schedule)) : ?>

    <h4><?= \app\modules\westnet\notifications\NotificationsModule::t('app', 'Schedule will be:'); ?></h4>

    <ul class="no-margin no-padding margin-bottom-half">

        <?php foreach ($schedule as $time) : ?>

            <li class="display-inline-block bg-success padding-quarter margin-quarter"><?= $time; ?> hs</li>

        <?php endforeach; ?>

    </ul>

<?php endif; ?>
<!-- end Schedule -->