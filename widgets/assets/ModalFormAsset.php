<?php

namespace fishvision\modalforms\widgets\assets;

use yii\web\AssetBundle;

/**
 * Class ModalFormAsset
 * @package fishvision\modalforms\widgets\assets
 */
class ModalFormAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/fishvision/yii2-modal-forms/widgets/';

    /**
     * @var array
     */
    public $js = [
        'js/modal-forms.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'css/style.css',
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}