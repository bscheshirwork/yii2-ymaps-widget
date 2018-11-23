<?php

namespace bscheshirwork\ymaps;

use Yii;
use yii\base\Component;

/**
 * Class Connection
 * @package bscheshirwork\ymaps
 *
 * Connect yandex map like to
 * <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
 * or if yu wish use apiKey (optional)
 * <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=<your API-key>" type="text/javascript"></script>
 *
 * Can be configure in config
 * 'components' => [
 *     'ymaps' => [
 *         'class' => \bscheshirwork\ymaps\Connection::class,
 *         'apiUri' => 'https://enterprise.api-maps.yandex.ru',
 *         'apiParams' => ['apikey' => '<hash>'],
 *     ]
 * ],
 *
 * Can be redefine
 * 'container' => [
 *     'definitions' => [
 *         \bscheshirwork\ymaps\Connection::class => \common\models\YMapsConnection::class,
 *     ],
 * ],
 *
 */
class Connection extends Component
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
     * 'apikey' Your own API key. Optional. Work fine without it.
     * 'lang' The language of yandex maps in any formats: "ru", "ru-RU", "ru_RU".
     * Supported lang=ru_RU; lang=en_US; lang=en_RU; lang=ru_UA; lang=uk_UA; lang=tr_TR.
     * If not set the application language settings `\Yii::$app->language` will be use
     * 'load' list of modules to load: `load=Map,Placemark,map.addon.balloon`. If not set all modules will use all packages
     */
    public $apiParams = [
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->apiParams['lang'] = $this->apiParams['lang'] ?? Yii::$app->language;
        $url = $this->apiUri . '/' . $this->apiVersion . '/?' . http_build_query($this->apiParams);
        Yii::$app->view->registerJsFile($url);
    }

}