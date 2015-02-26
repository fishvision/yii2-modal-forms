<?php

use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

Modal::begin([
    'header' => '<span></span>',
    'id' => 'fv-form-modal',
    'options' => is_array($options) && sizeof($options) > 0 ? $options : [
        'class' => 'fv-modal fade',
    ]
]);

?>

    <div class="body">
        <div class="loading"></div>
        <div class="form"></div>
    </div>

    <div class="clearfix"></div>

    <?php if ($showFooter === true) : ?>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Close') ?></button>
        </div>
    <?php endif; ?>

<?php
Modal::end();
?>