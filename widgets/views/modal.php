<?php

use yii\bootstrap\Modal;

Modal::begin([
    'header' => '<span></span>',
    'id' => $this->context->modalId,
    'options' => is_array($this->context->modalOptions) && sizeof($this->context->modalOptions) > 0 ? $this->context->modalOptions : [
        'class' => 'fv-modal fade',
    ]
]);

?>

    <div class="body">
        <div class="loading"></div>
        <div class="form"></div>
    </div>

    <div class="clearfix"></div>

<?php if ($this->context->showFooter === true) : ?>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Close') ?></button>
    </div>
<?php endif; ?>

<?php
Modal::end();
?>