# Yandex map widget for yii2

This is widget for representation yandex maps from yii2 widget.

## Installation

Add 
```
    "bscheshirwork/yii2-ymaps-widget": "*@dev",
```
into `require` section of you `composer.json` file.

## Configure (optional)

You can change default values of `Connector` for customize 
[connection to yandex map API](https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/load-docpage/)

First: add into config of application the `ymaps` components:

```php
<?php
return [
        'components' => [
            'ymaps' => [
                'class' => \bscheshirwork\ymaps\Connection::class,
                'apiUri' => 'https://enterprise.api-maps.yandex.ru',
                'apiParams' => ['apikey' => '<hash>'],
            ],
        ],
    ];
```

Second: use this component in widget `connection` param 
```php
<?= \bscheshirwork\ymaps\YMaps::widget([
    'connection' => 'ymaps',
    'mapState' => [
        'center' => [55.7372, 37.6066],
        'zoom' => 9,
    ],
    ]); ?>
```


## Usage

### Simple
        
Inject canvas for map. 

In view:
Inject code for generate map (optional, default).

```php
<?= \bscheshirwork\ymaps\YMaps::widget([
    'htmlOptions' => [
        'style' => 'height: 400px;',
    ],
    'mapState' => '{
        center: [55.9238145091058, 37.897131347654376],
        zoom: 10
    }',
    'mapOptions' => <<<JS
    {
        searchControlProvider: 'yandex#search'
    }
JS
]); ?>
```

The <div id='map' style='height: 400px;' ></div> will be generated;
The yandex maps will be initialized and assign to this canvas.

Tips: `mapState` and `mapOptions` can accept `json` string or php `array`

### Advanced usage

Advanced: Inject js vars for external js file.

In view:
```php
    <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>

    <?= \bscheshirwork\ymaps\YMaps::widget([
        'htmlOptions' => [
            'style' => 'height: 400px;',
        ],
        'mapState' => [
            'center' => [$model->latitude ?: 55.7372, $model->longitude ?: 37.6066],
            'zoom' => 9,
        ],
        'simpleMap' => false,
        'jsVars' => true,
    ]); ?>
```

The <div id='map' style='height: 400px;' ></div> will be generated. The JS vars will be generated and inserting into `POS_HEAD` position

In js:
```js
ymaps.ready(init);

function init() {
    var myPlacemark,
        myMap = mapBuilder(mapId, mapState, mapOptions);
    myMap.events.add('click', function (e) {
        var coords = e.get('coords');
        myPlacemark = new ymaps.Placemark(coords, {
            iconCaption: 'caption'
        }, {
            preset: 'islands#violetDotIconWithCaption',
            draggable: true
        });
        myMap.geoObjects.add(myPlacemark);
    });
    
    // ...
}
```

All rights reserved.
 
2018 © bscheshir.work@gmail.com
