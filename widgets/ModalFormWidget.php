<?php

namespace fishvision\modalforms\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use fishvision\modalforms\widgets\assets\ModalFormAsset;

/**
 * Class ModalFormWidget
 * @package fishvision\modalforms\widgets
 */
class ModalFormWidget extends Widget
{
    /**
     * @var string the route to send the ajax call to
     */
    public $route;

    /**
     * @var string the form id to display
     */
    public $formId;

    /**
     * @var array
     */
    public $button = [
        'label' => 'Modal Popup',
        'options' => [
            'classs' => 'btn-lg',
        ],
    ];

    /**
     * @var array callbacks
     */
    public $callbacks = [];

    /**
     * @var string
     */
    public $id = 'fv-contact-btn';

    /**
     * @var string
     */
    public $header;

    /**
     * @var array
     */
    private $views = [];

    /**
     * @var bool
     */
    public $ajaxSubmit = true;

    /**
     * @var string
     */
    private $modalId = 'fv-form-modal';

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        // Register the view if asset bundle not previously registered
        if (!isset($this->view->assetBundles[ModalFormAsset::className()])) {
            $this->views[] = $this->render('modal');
            $this->view->registerJs('var fvModalId = "' . $this->modalId . '";', View::POS_HEAD);
            $this->view->registerJs('var fvAjaxSubmit = {};', View::POS_HEAD);
            $this->view->registerJs('var fvCallbacks = {};', View::POS_HEAD);
        }

        // Button - merge user options with static options
        $this->button = ArrayHelper::merge($this->button, [
            'id' => $this->id,
            'options' => [
                'data' => [
                    'toggle' => 'modal',
                    'target' => '#' . $this->modalId,
                ],
            ],
        ]);

        // Register asset
        ModalFormAsset::register($this->view);

        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        // Create the button
        $this->views[] = $this->render('modal-btn', [
            'button' => $this->button,
        ]);

        // Register the view onclick
        $options = implode('", "', [$this->header, $this->route, $this->formId]);
        $this->view->registerJs('jQuery("#' . $this->id . '").on("click", function(e) { modalForm("' . $options . '"); });');


        // Ajax submit
        if ($this->ajaxSubmit) {
            $this->view->registerJs('fvAjaxSubmit["' . $this->formId . '"] = true;', View::POS_HEAD);
            $this->view->registerJs('jQuery("#' . $this->modalId . '").on("submit", "form", modalFormSubmit);');

            // Callbacks
            if (sizeof($this->callbacks) > 0) {
                $this->view->registerJs('fvCallbacks["' . $this->formId . '"] = {};', View::POS_HEAD);
            }

            // Success callback
            foreach ($this->callbacks as $col => $val) {
                $this->view->registerJs('fvCallbacks["' . $this->formId . '"]["' . Html::encode($col) . '"] = ' . Html::encode($val) . ';',
                    View::POS_HEAD);
            }
        }

        // Return the view html
        return implode('', $this->views);
    }
}