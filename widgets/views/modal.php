<?php

use yii\bootstrap\Modal;

Modal::begin([
    'header' => '<span></span>',
    'id' => 'fv-form-modal',
    'options' => [
        'class' => 'fv-modal fade',
    ],
]);

?>

    <div class="body">
        <div class="loading"></div>
        <div class="form"></div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Close') ?></button>
        <?php /*<button type="button" class="btn btn-primary"><?= Yii::t('app', 'Submit') ?></button>*/ ?>
    </div>

<?php
Modal::end();
?>