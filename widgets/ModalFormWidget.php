<?php

namespace fishvision\modalforms\widgets;

use fishvision\modalforms\helpers\StaticModalFormHelper;
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
     * @var bool whether or not to show the footer
     */
    public $showFooter = true;

    /**
     * @var array
     */
    public $modalOptions = [];

    /**
     * @var bool
     */
    public $ajaxSubmit = true;

    /**
     * @var string
     */
    public $modalId = 'fv-form-modal';

    /**
     * @var array
     */
    private $views = [];

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        // If the first time running, register basic JS
        if (StaticModalFormHelper::registeredCount() === 0) {
            $this->view->registerJs('var fvModalId = {};', View::POS_HEAD);
            $this->view->registerJs('var fvAjaxSubmit = {};', View::POS_HEAD);
            $this->view->registerJs('var fvCallbacks = {};', View::POS_HEAD);
            Yii::$app->view->params['fvModalForms']= [];
        }

        // Register the view if asset bundle not previously registered
        if (!StaticModalFormHelper::isRegistered($this->modalId)) {
            $this->view->registerJs('fvModalId[\'' . $this->modalId . '\'] = "' . $this->modalId . '";', View::POS_HEAD);
            $this->view->registerJs('fvAjaxSubmit[\'' . $this->modalId . '\'] = {};', View::POS_HEAD);
            $this->view->registerJs('fvCallbacks[\'' . $this->modalId . '\'] = {};', View::POS_HEAD);
            Yii::$app->view->params['fvModalForms'][] = $this->render('modal');

            // Mark the group as registered
            StaticModalFormHelper::register($this->modalId);
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
        $options = implode('", "', [$this->header, $this->route, $this->formId, $this->modalId]);
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