<?php

namespace bscheshirwork\ymaps;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * Class YMaps
 * @package bscheshirwork\ymaps
 *
 * Inject canvas for map. You can use this canvas in your js.
 * Inject code for generate map (optional, default).
 *     <?= \bscheshirwork\ymaps\YMaps::widget([
 *         'htmlOptions' => [
 *             'style' => 'height: 400px;',
 *         ],
 *     ]); ?>
 *
 * Inject js vars for external js file (optional).
 *
 *
 */
class YMaps extends Widget
{
    /**
     * @var string|array|\Closure The name of connection component from application config.
     * For example:
     * 'ymaps' // by name of application component. May be depend in application config
     * ['class' => Connection::class] // by createObject config within class
     * function($context){return new Connection;} // By closure;
     *
     */
    public $connection = ['class' => Connection::class];

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
     *     var myMap = new ymaps.Map('map', {...}, {...});
     * });
     */
    public $htmlOptions = [
        'id' => 'map',
        'class' => 'yandex-map',
        'style' => 'height: 100%; width: 100%;',
    ];

    /**
     * @var array|string the state for the yandex map object.
     * Please refer to the corresponding Web page for possible options.
     * You SHOULD set 'center' and 'zoom' state
     * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Map-docpage/
     * We can pass php array ['key' => 'value'] to convert it into js object "{key:value}"
     * We can pass string value with pure json "{key:value}" (like a manual).
     */
    public $mapState = [
        'center' => [55.753994, 37.622093],
        'zoom' => 9,
    ];

    /**
     * @var array|string the options for the yandex map object.
     * Please refer to the corresponding Web page for possible options.
     * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Map-docpage/
     * We can pass php array ['key' => 'value'] to convert it into js object "{key:value}"
     * We can pass string value with pure json "{key:value}" (like a manual).
     */
    public $mapOptions = [];

    /**
     * @var bool The marker of insert code to inject map directly
     * ymaps.ready(init);
     * function init() {
     *     var myMap = new ymaps.Map('map', mapState, mapOptions);
     * };
     * widget will generate this js in POS_READY position
     */
    public $simpleMap = true;

    /**
     * @var bool The marker of use extend syntax.
     * If true widget will generate js var in HEAD of page for use in separated js
     * var mapBuilder = function(id, state, options) {new ymaps.Map(id, state, options);}, mapState = {}, mapOptions = {};
     * Example of assets:
     * ymaps.ready(init);
     * function init() {
     *     var myPlacemark,
     *         myMap = mapBuilder(mapId, mapState, mapOptions);
     *     myMap.events.add('click', function (e) {
     *         var coords = e.get('coords');
     *         //...
     *     });
     * };
     */
    public $jsVars = false;

    /**
     * @var array The map of js vars. Key is marker value is name of insertiong var.
     * For use multiple widget on same page
     * [
     *     'map' => 'myMap',
     *     'mapId' => 'map1',
     *     'mapState' => 'mapState1',
     *     'mapOptions' => 'mapOptions1',
     *     'mapBuilder' => 'mapBuilder1',
     * ]
     * If pair is not set the var is named same at missing key;
     */
    public $jsVarNameList = [
        'map' => 'myMap',
        'mapId' => 'mapId',
        'mapState' => 'mapState',
        'mapOptions' => 'mapOptions',
        'mapBuilder' => 'mapBuilder',
    ];

    /**
     * @var array stored the map params
     */
    protected $_mapParams;

    /**
     * Configure necessarry params
     * @return array
     */
    protected function prepareMapParams()
    {
        if (empty($this->_mapParams)) {
            $mapId = $this->htmlOptions['id'];
            $mapState = empty($this->mapState) ? '{}' : (is_array($this->mapState) ? Json::htmlEncode($this->mapState) : $this->mapState);
            $mapOptions = empty($this->mapOptions) ? '{}' : (is_array($this->mapOptions) ? Json::htmlEncode($this->mapOptions) : $this->mapOptions);
            $this->_mapParams = ['id' => $mapId, 'state' => $mapState, 'options' => $mapOptions];
        }

        return $this->_mapParams;
    }

    /**
     * Registers a specific js vars `mapState`, `mapOptions` and `mapBuilder`
     * Example of usage in external js (like a asset):
     * ymaps.ready(init);
     * function init() {
     *     var myPlacemark,
     *         myMap = mapBuilder(mapId, mapState, mapOptions);
     *     myMap.events.add('click', function (e) {
     *         var coords = e.get('coords');
     *         //...
     *     });
     * };
     */
    protected function registerJsVars()
    {
        ['id' => $mapId, 'state' => $mapState, 'options' => $mapOptions] = $this->prepareMapParams();
        $varId = $this->jsVarNameList['mapId'] ?? 'mapId';
        $varState = $this->jsVarNameList['mapState'] ?? 'mapState';
        $varOptions = $this->jsVarNameList['mapOptions'] ?? 'mapOptions';
        $varBuilder = $this->jsVarNameList['mapBuilder'] ?? 'mapBuilder';
        $js = <<<JS
var $varId = '$mapId', 
    $varState = $mapState, 
    $varOptions = $mapOptions, 
    $varBuilder = function(id, state, options) {
        return new ymaps.Map(id, state, options);
    };
JS;
        $this->getView()->registerJs($js, View::POS_HEAD);
    }

    /**
     * Registers a specific js code within simple injection of map object
     */
    protected function registerSimpleMap()
    {
        ['id' => $mapId, 'state' => $mapState, 'options' => $mapOptions] = $this->prepareMapParams();
        $varMap = $this->jsVarNameList['map'] ?? 'myMap';
        $js = <<<JS
ymaps.ready(init);
function init() {
    var $varMap = new ymaps.Map('$mapId', $mapState, $mapOptions);
};
JS;
        $this->getView()->registerJs($js);
    }

    /**
     * @var Connection The stored connection. Attributes of connection can be read from app config.
     */
    protected $_connection;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->htmlOptions['id'] = $this->htmlOptions['id'] ?? 'map';

        if ($this->connection instanceof \Closure) {
            $connectionConfig = $this->connection($this);
        } else {
            $connectionConfig = $this->connection;
        }

        if ($connectionConfig instanceof Connection) {
            $this->_connection = $connectionConfig;
        } elseif (
            is_string($connectionConfig) &&
            Yii::$app->hasModule($connectionConfig) &&
            ($connection = Yii::$app->getModule($connectionConfig)) instanceof Connection
        ) {
            $this->_connection = $connection;
        } elseif (is_array($connectionConfig)) {
            $connectionConfig['class'] = $connectionConfig['class'] ?? Connection::class;
            $this->_connection = Yii::createObject($connectionConfig);
        } else {
            $this->_connection = Yii::createObject($connectionConfig);
        }

        if ($this->jsVars ?? false) {
            $this->registerJsVars();
        }
        if ($this->simpleMap) {
            $this->registerSimpleMap();
        }
    }

    /**
     * {@inheritdoc}
     * @return string|void
     */
    public
    function run()
    {
        parent::run();
        echo Html::tag($this->tagName, '', $this->htmlOptions);
    }

}