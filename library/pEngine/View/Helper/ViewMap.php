<?php

class pEngine_View_Helper_ViewMap extends Zend_View_Helper_Abstract
{
    /*
     * $mainInfo = array('latitude', 'longitude')
     * @param array $mainInfo
     *
     * пока не реализовано:
     * $point = array(array( 'latitude', 'longitude', 'title', 'comment'))
     * @param array(array) $points
     *
     * @param int $height
     */

    public function viewMap($mainInfo, $height = 400, $points = array())
    {
        if (!isset($mainInfo['zoom']))
        {
            $mainInfo['zoom'] = 17;
        }

        if (!isset($mainInfo['search']) )
        {
            if (!floatval($mainInfo['latitude']))
            {
                return false;
            }
            if (!floatval($mainInfo['longitude']))
            {
                return false;
            }

            if (count($points) == 0)
            {
                $points = array();
            }
        }

        $additionalTemplateBalloon = "'<div><div>$[title]</div><div>$[comment]</div></div>'";

        $options = Zend_Registry::get('options');

        if (isset($options['map']['key'])) {
            $this->view->headScript()->appendFile('http://api-maps.yandex.ru/1.1/index.xml?key=' . $options['map']['key'] . '&modules=pmap', 'text/javascript');
        }
        else {
            // если ключа нет в конфигах берется какой то ключ, для получения ошибки
            $this->view->headScript()->appendFile('http://api-maps.yandex.ru/1.1/index.xml?key=AG6Apk0BAAAAGGblBQIA-UQ1fXqOcOZH4CF2bhTiqRc7ZMEAAAAAAAAAAADaqUS2-7yungkxWijR47BxYG3LEQ==&modules=pmap', 'text/javascript');
        }


        $scr = "
            function createMap()
            {
                jQuery('#YMapsID').css( { height:'{$height}px' } );
                return new YMaps.Map(YMaps.jQuery('#YMapsID')[0]);
            }

            height = {$height};

            var main = " . json_encode($mainInfo) . ";

            const zoom = main.zoom;

            placemark = '';

            function addPlacemark(map, latitude, longitude)
            {
                style = new YMaps.Style('default#houseIcon');

                placemark = new YMaps.Placemark(
                    new YMaps.GeoPoint(latitude, longitude, false),
                    {
                        hasBalloon: false,
                        draggable: false,
                        style: style
                    }
                );

                map.addOverlay(placemark);
                map.setCenter(new YMaps.GeoPoint(latitude, longitude, false), zoom, YMaps.MapType.PMAP);
                jQuery('#positionOnMap').removeClass('hidden');
            }

            function addAdditionalPlacemarks()
            {
                points = " . json_encode($points) . ";

                var style;
                // название дефалтного стиля для наследования
                var placemarkStyle;
                jQuery.each(
                    points,
                    function()
                    {
                        if (this.title == 'магазин')
                        {
                            placemarkStyle = 'default#bankIcon';
                        }
                        if (this.title == 'остановка')
                        {
                            placemarkStyle = 'default#busIcon';
                        }

                        style = new YMaps.Style(placemarkStyle);
                        style.balloonContentStyle =
                        {
                            template: new YMaps.Template({$additionalTemplateBalloon})
                        }

                        var placemark = new YMaps.Placemark(
                            new YMaps.GeoPoint(this.latitude, this.longitude, false),
                            {
                                 draggable: false,
                                 style: style
                            }
                        );

                        placemark.title = this.title;
                        placemark.comment = this.comment;
                        map.addOverlay(placemark);
                    }
                );
            }

            function trySearchAndAddPlacemark()
            {
                var object = this;
                var searchString = main.city + ' ' + main.street + ' ' + main.house_num;

                var geocoder = new YMaps.Geocoder(
                    searchString,
                    {
                        geocodeProvider: 'yandex#pmap',
                        boundedBy: new YMaps.GeoBounds(
                            new YMaps.GeoPoint(119.507249, 48.6478),
                            new YMaps.GeoPoint(134.82219, 57.440713)
                        )
                    }
                );

                YMaps.Events.observe(geocoder, geocoder.Events.Load, function ()
                {
                    if (this.length())
                    {
                        var point = this.get(0).getGeoPoint();
                        addPlacemark(object.map, point.getX(), point.getY());
                    }
                });

                YMaps.Events.observe(geocoder, geocoder.Events.Fault,
                    function (geocoder, errorMessage)
                    {
                        alert('Произошла ошибка: ' + errorMessage);
                    }
                );
            }


            function getConfigurationToolBar()
            {
                var toolBar = new YMaps.ToolBar([
                    //new YMaps.ToolBar.MoveButton(),
                    //new YMaps.ToolBar.MagnifierButton(),
                    new YMaps.ToolBar.RulerButton()
                ]);

                return toolBar;
            }

            function run(map)
            {
                var toolBar = getConfigurationToolBar(map);
                map.addControl(new YMaps.TypeControl([YMaps.MapType.PMAP, YMaps.MapType.PHYBRID]));
                map.addControl(toolBar);
                map.addControl(new YMaps.Zoom(
                    {
                        customTips: []

                    }
                ));
                map.setMinZoom(10);
                map.setMaxZoom(18);
                map.disableDragging();
                map.disableDblClickZoom();


                //setMapCenter(main.latitude, main.longitude, zoom);
                if (main.search)
                {
                    trySearchAndAddPlacemark();
                }
                else
                {
                    addPlacemark(map, main.latitude, main.longitude);
                }
                addAdditionalPlacemarks();
            }
        ";

        $this->view->headScript()->appendScript($scr);


        $this->view->headScript()->appendScript("
            jQuery(document).ready(
                function()
                {
                    map = createMap();
                    run(map);
                }
            );"
        );

        $html = "<div id='YMapsID'></div>";

        return $html;

    }

}
