<?php
    class pEngine_View_Helper_FormMap extends Zend_View_Helper_FormElement
    {
        public function init()
        {
        }

        /**
         *
         * @access public
         *
         * Для метки по умолчанию используются input поля со следующими id : city, street, number
         * без элемента с id=city содержащим название города, не будет функционировать
         *
         * координаты основной точки содержаться в hidden элементах  name="latitude" и name="longitude"
         *
         * array('height', 'city', 'realtyType', 'transactionType')
         * @param array $value
         *
         * @param array $attribs Attributes for the element tag.
         *
         * @return string The element XHTML.
         */
	    public function formMap($name, $value, $attribs)
        {
            $height = $value['height'];

            if ($height == null)
            {
                $height = 400;
            }

            $options = Zend_Registry::get('options');

            if (isset($options['map']['key'])) {
                $this->view->headScript()->appendFile('http://api-maps.yandex.ru/1.1/index.xml?key=' . $options['map']['key'] . '&modules=pmap', 'text/javascript');
            }
            else {
                // если ключа нет в конфигах берется какой то ключ, для получения ошибки
                $this->view->headScript()->appendFile('http://api-maps.yandex.ru/1.1/index.xml?key=AG6Apk0BAAAAGGblBQIA-UQ1fXqOcOZH4CF2bhTiqRc7ZMEAAAAAAAAAAADaqUS2-7yungkxWijR47BxYG3LEQ==&modules=pmap', 'text/javascript');
            }
            
            //$this->view->headScript()->appendFile('http://api-maps.yandex.ru/1.1/index.xml?key=ADpLGU0BAAAAFoP4OQIAp9O-bV1IhifWifI7Y-CSBnjjv38AAAAAAAAAAAC5g3dfvfhdPZ6JBw6fC-MrvLloaA==~AJQMWk0BAAAA8861fgIAmYxaZ4bt39KaMFAXpIyl0yaHK-sAAAAAAAAAAAD6qwMKuVrljEt-p8nyGsYp_7RLDQ==~AG6Apk0BAAAAGGblBQIA-UQ1fXqOcOZH4CF2bhTiqRc7ZMEAAAAAAAAAAADaqUS2-7yungkxWijR47BxYG3LEQ==&modules=pmap', 'text/javascript');
            $this->view->headScript()->appendScript("
                yaMap = '';
                Map = function() {
                    this.map = '';

                    this.mark = [];

                    this.placemark = '';

                    this.lastId = 0;
                    this.maxZoom = 18;
                    this.minZoom = 13;

                    this.markCount = 0;
                };


                Map.prototype.setMapCenter = function()
                {
                    //var object = this;
                    // поиск на карте по введенному адресу
                    // и добавление метки
                    var searchString = jQuery('input#city').val() + ' ' + jQuery('input#street').val() + ' ' + jQuery('input#number').val();

                    var geocoder = new YMaps.Geocoder(searchString, {geocodeProvider: 'yandex#pmap',
                    boundedBy: new YMaps.GeoBounds(new YMaps.GeoPoint(119.507249, 48.6478), new YMaps.GeoPoint(134.82219, 57.440713))});

                    YMaps.Events.observe(geocoder, geocoder.Events.Load, function ()
                    {
                        if (this.length())
                        {
                            var point = this.get(0).getGeoPoint();
                            yaMap.placemark = new YMaps.Placemark(
                                point,
                                {
                                     draggable: true,
                                     hasBalloon: false,
                                     style: 'default#houseIcon'

                                }
                            );

                            if (jQuery('input#street').val() != '' && jQuery('input#number').val() != '')
                            {
                                yaMap.map.addOverlay(yaMap.placemark);
                                yaMap.map.setCenter(point, 16, YMaps.MapType.PMAP);
                            }
                            else
                            {
                                yaMap.map.setCenter(point, 13, YMaps.MapType.PMAP);
                            }
                        }
                        else
                        {
                            // если на крате не найден адрес, то осуществляется поиск по названию города
                            var searchString = jQuery('input#city').val();

                            var geocoder = new YMaps.Geocoder(searchString, {geocodeProvider: 'yandex#pmap',
                            boundedBy: new YMaps.GeoBounds(new YMaps.GeoPoint(119.507249, 48.6478), new YMaps.GeoPoint(134.82219, 57.440713))});

                            YMaps.Events.observe(geocoder, geocoder.Events.Load, function ()
                            {
                                if (this.length()) {
                                    var point = this.get(0).getGeoPoint();

                                    yaMap.map.setCenter(point, 13, YMaps.MapType.PMAP);
                                }
                                else
                                {
                                    jQuery('#YMapsID').css( { height:'0px' } );
                                    jQuery('#createMap').attr('textContent', 'Добавить на карте');
                                    alert('Ничего указанный город не найден на карте');
                                }
                            });

                            YMaps.Events.observe(geocoder, geocoder.Events.Fault,
                                function (geocoder, errorMessage)
                                {
                                    alert('Произошла ошибка: ' + errorMessage)
                                }
                            );
                        }
                    });

                    YMaps.Events.observe(geocoder, geocoder.Events.Fault,
                        function (geocoder, errorMessage)
                        {
                            alert('Произошла ошибка: ' + errorMessage)
                        }
                    );
                }


                Map.prototype.createMap = function()
                {
                    this.map = new YMaps.Map(YMaps.jQuery('#YMapsID')[0]);
                    var toolBar = this.getConfigurationToolBar(this.map);
                    this.map.addControl(new YMaps.TypeControl([YMaps.MapType.PMAP, YMaps.MapType.PHYBRID]));
                    this.map.enableScrollZoom();
                    this.map.addControl(toolBar);
                    this.map.setMaxZoom(this.maxZoom);
                    this.map.setMinZoom(this.minZoom);
                    this.map.addControl(new YMaps.Zoom());

                };

                Map.prototype.setAddressByPointIfStreetNotSet = function(point)
                {
                    var geocoder = new YMaps.Geocoder(point, {geocodeProvider: 'yandex#map'});

                    YMaps.Events.observe(geocoder, geocoder.Events.Load, function () {
                        console.log(this);
                        if (this.length()) {
                            console.log(this.get(0));
                            //map.addOverlay(this.get(0));
                            //map.panTo(this.get(0).getGeoPoint())
                        }else {
                            console.log('не найдено')
                        }
                    });
                };
" .
                // конфигурирвание и добавление кнопки добавления главной метки к тулбару
"
                Map.prototype.addMainPointButtonToToolBar = function(toolBar)
                {
                    var buttonAddPoint = new YMaps.ToolBarRadioButton(
                        'default',
                        {
                            caption: 'Указать на карте',
                            hint: 'Указать на карте'
                        }
                    );

" .
                    // eventAddPoint событие для обработки клика на карте в режиме добавлении метки
"
                    var eventAddPoint = '';
                    var object = this;

                    YMaps.Events.observe(buttonAddPoint, buttonAddPoint.Events.Select,
                        function ()
                        {
                            eventAddPoint = YMaps.Events.observe(
                                object.map,
                                object.map.Events.Click,
                                function (map, mEvent)
                                {
                                    if (object.placemark != '')
                                    {
                                        object.map.removeOverlay(object.placemark);
                                        object.placemark = '';
                                    }


                                    object.placemark = new YMaps.Placemark(
                                        mEvent.getGeoPoint(),
                                        {
                                             draggable: true,
                                             hasBalloon: false,
                                             style: 'default#houseIcon'
                                        }
                                    );

                                    object.map.addOverlay(object.placemark);

                                }
                            );
                        },
                        toolBar
                    );

                    YMaps.Events.observe(buttonAddPoint, buttonAddPoint.Events.Deselect,
                        function ()
                        {
                            eventAddPoint.cleanup();
                        },
                        toolBar
                    );

                    toolBar.add(buttonAddPoint);
                };" .

                // конфигурирвание и добавление кнопки автобусной остановки к тулбару
                "
                Map.prototype.addBusStopPointButtonToToolBar = function(toolBar)
                {
                    var buttonAddPoint = new YMaps.ToolBarRadioButton(
                        'default',
                        {
                            caption: 'Остановка',
                            hint: 'Указать остановку'
                        }
                    );

                    var placemarkStyle = 'default#busIcon';
                    var balloonTemplate = this.getAdditionalBalloonTemplate();

                    this.addAdditionalButtonsEvents(buttonAddPoint, balloonTemplate, toolBar, placemarkStyle, 'остановка')
                };" .

                // конфигурирвание и добавление кнопки магазин к тулбару
                "
                Map.prototype.addShopButtonToToolBar = function(toolBar)
                {
                    var buttonAddPoint = new YMaps.ToolBarRadioButton(
                        'default',
                        {
                            caption: 'Магазин',
                            hint: 'Указать магазин'
                        }
                    );
                    var placemarkStyle = 'default#bankIcon';
                    var balloonTemplate = this.getAdditionalBalloonTemplate();
                    this.addAdditionalButtonsEvents(buttonAddPoint, balloonTemplate, toolBar, placemarkStyle, 'магазин')
                };



                Map.prototype.getAdditionalBalloonTemplate = function()
                {
                    return " .
                    " '<div>' + " .
                        " '<input id=\"markNumber\" type=\"hidden\" value=\"$[number]\">' + " .
                        " '<div>$[title]</div><br\/>' + " .
                        " '<textarea style=\'width:98%\' id=\'markComment\' rows=\'5\'>$[comment]</textarea><br/>' + " .
                        " '<button onclick=\"saveClick(event)\">Сохранить</button>' + " .
                        " '<button onclick=\"deleteClick(event)\">Удалить</button>' + " .
                    " '</div>';
                }


                function cancelEvent(e)
                {
                    if (e.stopPropagation)
                    {
                        e.stopPropagation();
                    }
                    else
                    {
                        e.cancelBubble = true;
                    }
                }

                function deleteClick(event)
                {
                    cancelEvent(event);
                    var number = jQuery('#markNumber').val();
                    yaMap.map.closeBalloon();
                    yaMap.map.removeOverlay(yaMap.mark[number]);
                    delete yaMap.mark[number];
                    return false;
                }

                function saveClick(event)
                {
                    cancelEvent(event);
                    var number = jQuery('#markNumber').val();
                    yaMap.mark[number].comment = jQuery('#markComment').val();
                    yaMap.map.closeBalloon();
                }

                " .

                /*
                       * button - кнопка для тулбара, balloonTemplate - необходимый шаблон,toolBar - собственно тулбар
                       * placemarkStyle стиль метки изначально унаследованный от метки с необходимой иконкой
                       * title - заголовок во всплывающей подсказке
                       */
                "
                Map.prototype.addAdditionalButtonsEvents = function(button, balloonTemplate, toolBar, placemarkStyle, title)
                {
                    var eventAddPoint = '';
                    var object = this;

                    if (placemarkStyle == undefined)
                    {
                        placemarkStyle = 'default#lightblueSmallPoint';
                    }

                    YMaps.Events.observe(button, button.Events.Select,
                        function ()
                        {
                            var style = new YMaps.Style(placemarkStyle);

                            style.balloonContentStyle =
                            {
                                template: new YMaps.Template(balloonTemplate)
                            }

                            eventAddPoint = YMaps.Events.observe(
                                object.map,
                                object.map.Events.Click,
                                function (map, mEvent)
                                {
                                    var mark = new YMaps.Placemark(
                                        mEvent.getGeoPoint(),
                                        {
                                            draggable: true,
                                            style: style
                                        }
                                    );

                                    mark.number = object.markCount;
                                    mark.title = title;

                                    object.mark[object.markCount++] = mark;

                                    object.map.addOverlay(mark);
                                    mark.openBalloon();
                                }
                            );
                        },
                        toolBar
                    );

                    YMaps.Events.observe(button, button.Events.Deselect,
                        function ()
                        {
                            eventAddPoint.cleanup();
                        },
                        toolBar
                    );

                    toolBar.add(button);
                };

                Map.prototype.getConfigurationToolBar = function ()
                {
                    var toolBar = new YMaps.ToolBar([
                        new YMaps.ToolBar.MoveButton(),
                        new YMaps.ToolBar.MagnifierButton(),
                        new YMaps.ToolBar.RulerButton()
                    ]);

                    this.addMainPointButtonToToolBar(toolBar);
                    this.addBusStopPointButtonToToolBar(toolBar);
                    this.addShopButtonToToolBar(toolBar);

                    return toolBar;
                };

                Map.prototype.removeMarkById = function(placemarkId)
                {
                    var object = this;

                    jQuery.each(
                        this.mark,
                        function ()
                        {
                            if (this.id == placemarkId)
                            {
                                object.map.removeOverlay(this);
                            }
                        }
                    );
                };

                Map.prototype.clearMap = function()
                {
                    this.map.removeAllOverlays();

                    this.mark = [];
                    this.placemark = '';
                };
            ");


            $this->view->headScript()->appendScript("
                jQuery(document).ready(
                    function()
                    {
                        yaMap = '';

                        jQuery('#createMap').click(
                            function()
                            {
                                if (jQuery('#YMapsID').height() == 0)
                                {
                                    jQuery('#YMapsID').css( { height:'{$height}px' } );
                                    yaMap = new Map();
                                    yaMap.createMap();

                                    yaMap.setMapCenter();
                                    jQuery('#createMap').attr('textContent', 'Закрыть карту и удалить метку');
                                }
                                else
                                {
                                    jQuery('#YMapsID').css( { height:'0px' } );
                                    jQuery('#createMap').attr('textContent', 'Добавить на карте');

                                    yaMap.clearMap();
                                }

                                return false;
                            }
                        );


                        jQuery('#send').click(
                            function()
                            {
                                if (yaMap.placemark != '')
                                {
                                    var point = yaMap.placemark.getGeoPoint();
                                    jQuery('#results').append(" . "\"<input type='hidden' name='mainLatitude' value='\" + point.getX() + \"'/>\"" . ");
                                    jQuery('#results').append(" . "\"<input type='hidden' name='mainLongitude' value='\" + point.getY() + \"'/>\"" . ");

                                    jQuery.each(
                                        yaMap.mark,
                                        function()
                                        {
                                            if (this)
                                            {
                                                point = this.getGeoPoint();
                                                jQuery('#results').append(" . "\"<input type='hidden' name='title[]' value='\" + this.title + \"'/>\"" . ");
                                                jQuery('#results').append(" . "\"<input type='hidden' name='comment[]' value='\" + this.comment + \"'/>\"" . ");
                                                jQuery('#results').append(" . "\"<input type='hidden' name='latitude[]' value='\" + point.getX() + \"'/>\"" . ");
                                                jQuery('#results').append(" . "\"<input type='hidden' name='longitude[]' value='\" + point.getY() + \"'/>\"" . ");
                                            }
                                        }
                                    )
                                }

                                return true;
                            }
                        );

                    }
                );"
            );


            $html = "
                <html>
                    <body>
                        <button id='createMap'>
                            Добавить на карте
                        </button>
                        <div id='YMapsID' ></div>
                        <div id='results'></div>
                    </body>
                </html>
            ";


            return $html;
	    }
    }
