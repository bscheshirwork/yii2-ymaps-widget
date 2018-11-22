<?php

namespace bscheshirwork\ymaps;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class YMaps
 * @package bscheshirwork\ymaps
 *
 * Connect yandex map like to
 * <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
 * or if yu wish use apiKey (optional)
 * <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=<your API-key>" type="text/javascript"></script>
 *
 * Inject canvas for map. You can use this canvas in your js.
 */
class YMaps extends Widget
{

    /**
     * @var string the url of yandex map api excluded trailing slash.
     * Include protocol http|https
     * Build template is apiUri/apiVersion/?apikey=apiKey
     * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/load-docpage/
     */
    public $apiUri = 'https://api-maps.yandex.ru';

    /**
     * @var string The version of API. Can be 2.1-dev, 2.1.17 or 2.1
     * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/versions/concepts/index-docpage
     */
    public $apiVersion = '2.1';

    /**
     * @var array The api params for customize yandex api url string.
     * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/load-docpage/
     * For example:
     * 'apikey' Your own API cay. Optional. Work fine without it.
     * 'lang' The language of yandex maps in any formats: "ru", "ru-RU", "ru_RU".
     * Supported lang=ru_RU; lang=en_US; lang=en_RU; lang=ru_UA; lang=uk_UA; lang=tr_TR.
     * If not set the application language settings `\Yii::$app->language` will be use
     * 'load' list of modules to load: `load=Map,Placemark,map.addon.balloon`. If not set all modules will use ``
     */
    public $apiParams = [
    ];

    /**
     * @var string The tag name for canvas within map
     */
    public $tagName = 'div';

    /**
     * @var array the html options of canvas tag
     * note: the HTML element MUST have a non-zero height
     *
     * 'id' will be used in your own js for attach map object
     * ymaps.ready(function () {
     *     var myMap = new ymaps.Map('map', {
     *             center: [55.751574, 37.573856],
     *             zoom: 9
     *         }, {
     *             searchControlProvider: 'yandex#search'
     *         });
     * });
     */
    public $htmlOptions = [
        'id' => 'map',
        'class' => 'yandex-map',
        'style' => 'height: 100%; width: 100%;',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->apiParams['language'] = $this->apiParams['language'] ?? Yii::$app->language;
        $url = $this->apiUri . '/' . $this->apiVersion . '/?' . http_build_query($this->apiParams);
        Yii::$app->view->registerJsFile($url);
    }

    /**
     * {@inheritdoc}
     * @return string|void
     */
    public function run()
    {
        parent::run();
        $this->htmlOptions['id'] = $this->htmlOptions['id'] ?? 'map';
        echo Html::tag($this->tagName, '', $this->htmlOptions);
    }

}