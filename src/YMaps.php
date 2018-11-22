<?php

namespace bscheshirwork\ymaps;

use yii\base\Widget;
use yii\helpers\Html;

class YMaps extends Widget
{
    /** @var string The tag name for canvas within map*/
    public $tagName = 'div';

    /** @var array the html options of canvas tag*/
    public $htmlOptions = array(
        'class' => 'yandex-map',
        'style' => 'height: 100%; width: 100%;',
    );

    /**
     * {@inheritdoc}
     * @return string|void
     */
    public function run()
    {
        parent::run();

        echo Html::tag($this->tagName, '', $this->htmlOptions);
    }

}