<?php
/**
 * Created by PhpStorm.
 * User: silence
 * Date: 02.03.2011
 * Time: 12:12:51
 * To change this template use File | Settings | File Templates.
 */

class pEngine_View_Helper_MapEditor extends Zend_View_Helper_FormElement
{
    public function mapEditor($name, $value, $attribs) {
        //$height = 0;
        //$width = 0;

        $info = $this->_getInfo($name, $value, $attribs);

        if (!isset($value['title']) || $value['title'] != '')
        {
            $value['title'] = 'Указать на карте...';
        }

        if (isset($value['height']) && isset($value['width']) &&  $value['height'] && $value['width'] && $value['height'] <= 100 && $value['height'])
        {
            $height = $value['height'];
            $width = $value['width'];
        }
        else
        {
            $height = 80;
            $width = 80;
        }


        $options = Zend_Registry::get('options');

        if (isset($options['map']['key'])) {
            $this->view->headScript()->appendFile('http://api-maps.yandex.ru/1.1/index.xml?key=' . $options['map']['key'] . '&modules=pmap', 'text/javascript');
        }
        else {
            // если ключа нет в конфигах берется какой то ключ, для получения ошибки
            $this->view->headScript()->appendFile('http://api-maps.yandex.ru/1.1/index.xml?key=AG6Apk0BAAAAGGblBQIA-UQ1fXqOcOZH4CF2bhTiqRc7ZMEAAAAAAAAAAADaqUS2-7yungkxWijR47BxYG3LEQ==&modules=pmap', 'text/javascript');
        }
        //$this->view->headScript()->appendFile('http://api-maps.yandex.ru/1.1/index.xml?key=AJQMWk0BAAAA8861fgIAmYxaZ4bt39KaMFAXpIyl0yaHK-sAAAAAAAAAAAD6qwMKuVrljEt-p8nyGsYp_7RLDQ==~ADpLGU0BAAAAFoP4OQIAp9O-bV1IhifWifI7Y-CSBnjjv38AAAAAAAAAAAC5g3dfvfhdPZ6JBw6fC-MrvLloaA==~AIeNcE0BAAAA4QfzegIADsSuYwThlTJjUnf_9SB8YRoaWcoAAAAAAAAAAACC9WSd2R8OFG-BQGaykoxL8Ebzeg==&modules=pmap', 'text/javascript');


        // class editor
        $this->view->headScript()->appendScript('
            Editor = function(divName, longitude, latitude, zoom, isPMAP)
            {
                //  является ли карта народной
                if (isPMAP == undefined)
                {
                    this.isPMAP = true;
                }

                var value = ' . json_encode($value) . ';
                
                this.placemarkStyle = "default#whitePoint";
                this.maxZoom = 18;
                this.minZoom = 6;

                this.map = new YMaps.Map(YMaps.jQuery(divName)[0]);

                this.map.addControl(new YMaps.TypeControl(this.getMapTypesAvailable()), new YMaps.ControlPosition(YMaps.ControlPosition.TOP_RIGHT, new YMaps.Point(0, 30)));
                this.map.enableScrollZoom();
                this.map.addControl(new YMaps.Zoom());
                //this.map.addControl(new YMaps.ToolBar());
                this.map.addControl(new YMaps.ScaleLine());

                this.map.setMaxZoom(this.maxZoom);
                this.map.setMinZoom(this.minZoom);


                // поиск
                // @todo boundedBy ограничивает область поиска только благовещенском
                (new YMaps.SearchControl({
                    geocodeOptions:
                    {
                        geocodeProvider: "yandex#pmap",
                        boundedBy: new YMaps.GeoBounds( new YMaps.GeoPoint(127.374833, 50.265256),
                                                        new YMaps.GeoPoint(127.711291, 50.394479))
                    },
                    useMapBounds: true,
                    noCentering: false,
                    noPlacemark: true,
                    resultsPerPage: 7,
                    width: 300
                })).onAddToMap(this.map, new YMaps.ControlPosition(YMaps.ControlPosition.TOP_RIGHT, new YMaps.Point(0, 0)));

                //this.map.setCenter(new YMaps.GeoPoint(longitude, latitude, false), zoom, this.getMapType());


                var toolbar = new YMaps.ToolBar();
                this.addPlacemarkButton(toolbar);
                toolbar.onAddToMap(this.map);
                this.addOkCancelMapButton();

                //this.addPlacemarkSelectorControl();

                this.placemark = "";

                if (value)
                {
                    if (value.point)
                    {
                        var point = new YMaps.GeoPoint(value["point"].latitude, value["point"].longitude)

                        // добавление метки
                        this.placemark = new YMaps.Placemark(
                            point,
                            {
                                draggable: true,
                                style: this.placemarkStyle,
                                hasBalloon: false,
                                hasHint: true
                            }
                        );
                    }
                }

                this.setMapCenter();
            };

            Editor.prototype.getMapType = function()
            {
                if (this.isPMAP)
                {
                    return YMaps.MapType.PMAP;
                }
                else
                {
                    return YMaps.MapType.MAP;
                }
            };

            Editor.prototype.addOkCancelMapButton = function()
            {
                var map = this.map;
                // Создает кнопку
                var cancelButton = new YMaps.ToolBarButton(
                    {
                        caption: "Отменить",
                        hint: "Удаляет отметку и закрывает карту"
                    }
                );

                var obj = this;

                YMaps.Events.observe(cancelButton, cancelButton.Events.Click, function ()
                    {
                        obj.placemark = "";
                        map.removeAllOverlays();
                        //@ todo fds
                        popupp.togglePopup();
                    },
                    map
                );

                var saveButton = new YMaps.ToolBarButton(
                    {
                        caption: "Сохранить",
                        hint: "Закрыть карту и сохранить отметку"
                    }
                );

                YMaps.Events.observe(saveButton, saveButton.Events.Click, function ()
                    {
                        popupp.togglePopup();
                    },
                    map
                );

                var toolBar = new YMaps.ToolBar([
                    cancelButton, saveButton
                ]);

                map.addControl(toolBar, new YMaps.ControlPosition(YMaps.ControlPosition.BOTTOM_LEFT, new YMaps.Point(0, 0)));
            };

            Editor.prototype.setMapCenter = function()
            {
                this.map.redraw(false);
                var searchCity = function(city)
                {
                    var searchString = city;
                    var geocoder = new YMaps.Geocoder(searchString, {geocodeProvider: "yandex#pmap",
                    boundedBy: new YMaps.GeoBounds(new YMaps.GeoPoint(119.507249, 48.6478), new YMaps.GeoPoint(134.82219, 57.440713))});

                    YMaps.Events.observe(geocoder, geocoder.Events.Load, function ()
                    {
                        if (this.length()) {
                            var point = this.get(0).getGeoPoint();
                            object.map.setCenter(point, 13, YMaps.MapType.PMAP);
                        }
                        else
                        {
                            jQuery("#YMapsID").css( { height:"0px" } );
                            jQuery("#createMap").attr("textContent", "Добавить на карте");
                            alert("Указанный город не найден на карте");
                        }
                    });

                    YMaps.Events.observe(geocoder, geocoder.Events.Fault,
                        function (geocoder, errorMessage)
                        {
                            alert("Произошла ошибка: " + errorMessage);
                        }
                    );
                };

                var searchAddress = function()
                {
                    var geocoder = new YMaps.Geocoder(searchString, {geocodeProvider: "yandex#pmap",
                    boundedBy: new YMaps.GeoBounds(new YMaps.GeoPoint(119.507249, 48.6478), new YMaps.GeoPoint(134.82219, 57.440713))});

                    YMaps.Events.observe(geocoder, geocoder.Events.Load, function ()
                    {
                        if (this.length())
                        {
                            var point = this.get(0).getGeoPoint();

                            // добавление метки
                            object.placemark = new YMaps.Placemark(
                                point,
                                {
                                    draggable: true,
                                    style: object.placemarkStyle,
                                    hasBalloon: false,
                                    hasHint: true
                                }
                            );

                            if (jQuery("input#street").val() != "" && number != "")
                            {
                                object.map.addOverlay(object.placemark);
                                object.map.setCenter(point, 16, YMaps.MapType.PMAP);
                            }
                            else
                            {
                                object.map.setCenter(point, 13, YMaps.MapType.PMAP);
                            }

                            // popupp - глобальный объект
                            if (popupp)
                            {

                                if (this.placemark != "")
                                {
                                    popupp.showPopupLink.html("Указано на карте");
                                    popupp.showPopupIcon.removeClass("hidden");
                                } else
                                {
                                    popupp.showPopupLink.html("' . $value['title'] . '");
                                    popupp.showPopupIcon.addClass("hidden");
                                }
                            }
                            return 0;
                        }
                        else
                        {
                            return 1;
                        }
                    });

                    YMaps.Events.observe(geocoder, geocoder.Events.Fault,
                        function (geocoder, errorMessage)
                        {
                            alert("Произошла ошибка: " + errorMessage)
                        }
                    );

                    return 1;
                };


                var object = this;
                
                object.map.removeAllOverlays();

                // поиск на карте по введенному адресу
                // и добавление метки

                if (object.placemark != "")
                {
                    // добавление метки
                    object.map.addOverlay(object.placemark);
                    var point = object.placemark.getGeoPoint();
                    object.map.setCenter(point, 16, YMaps.MapType.PMAP);
                    return true;
                }

                // @todo временное решение. проверяем различные варианты ввода города, номера дома, etc.
                var city = jQuery("input#city").val();
                if (city == undefined)
                {
                    city = jQuery("select#city option:selected").text();
                }
                
                var number = jQuery("input#house_num").val();
                if (number == undefined)
                {
                    number = jQuery("input#number").val();
                }

                var street = jQuery("input#street").val();
                
                var searchString = city + " " + street + " " + number;

                if (city == "")
                {
                    return false;
                }
                else
                {
                    if (street != "" && number != "")
                    {
                        var searchError = searchAddress(searchString); 
                        if (searchError == 1)
                        {
                            searchCity(city);
                        }
                    }
                    else
                    {
                        searchCity(city);
                    }
                }
            }

            // добавление меток
            Editor.prototype.addPlacemarkButton = function(toolbar)
            {
                // кнопка
                var button = new YMaps.ToolBarRadioButton(
                    YMaps.ToolBar.DEFAULT_GROUP,
                    {
                        icon: "http://api.yandex.ru/i/maps/tools/draw/add_point.png",
                        width: 20,
                        hint: "Режим добавления меток"
                    }
                );

                toolbar.add(button);
                var obj = this;
                obj.placemark = "";

                // обреботчик событий
                var listener = YMaps.Events.observe(
                    this.map,
                    this.map.Events.Click,
                    function (map, mEvent)
                    {
                        if (obj.placemark)
                        {
                            obj.map.removeOverlay(obj.placemark);
                            obj.placemark = "";
                        }

                        // добавление метки
                        obj.placemark = new YMaps.Placemark(
                            mEvent.getGeoPoint(),
                            {
                                draggable: true,
                                style: this.placemarkStyle,
                                hasBalloon: false,
                                hasHint: true
                            }
                        );


                        map.addOverlay(obj.placemark);
                    },
                    this
                );

                listener.disable();
                YMaps.Events.observe(
                    button,
                    button.Events.Select,
                    function ()
                    {
                        listener.enable();
                    },
                    toolbar);

                YMaps.Events.observe(
                    button,
                    button.Events.Deselect,
                    function ()
                    {
                        listener.disable();
                    },
                    toolbar
                );
            };

            Editor.prototype.getMapTypesAvailable = function()
            {
                if (this.isPMAP)
                {
                    return [YMaps.MapType.PMAP, YMaps.MapType.PHYBRID];
                }
                else
                {
                    return [YMaps.MapType.MAP, YMaps.MapType.HYBRID];
                }
            };
        ');

        // popup styles
        $this->view->headStyle()->appendStyle('
            .hidden {
                display: none;
              }

              /* Pop-up message and fading background */
            #mask {
               background-color: #000000;
               left: 0;
               -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";/* IE8 */
               filter:"progid:DXImageTransform.Microsoft.Alpha(opacity = 70)"; /* IE5+ */
               filter: "alpha(opacity=70)";/* IE4- */
               moz-opacity: 0; /* Mozilla */
               -khtml-opacity: 0; /* Safari */
               opacity: 0;/* general CSS3 */
               position: fixed;
               top: 0;
               width: 100%;
               z-index: 10;
             }
             
            #popup {
                width:' . $width . '%;
                height:' . $height . '%;
                left:' . (100 - $width) / 2 . '%;
                top: ' . (100 - $height) / 2 . '%;
                background-color: #FFFFFF;
                position: fixed;
                z-index: 11;
            }

            #popup h3 {
                margin-bottom: 10px;
            }

            div.mail {
                border: 5px solid #6ebd6e;
                padding:10px;
            }
            
            span#showPopupIcon{
                background: url("/images/ok_icon.png") no-repeat;
                width: 16px;
                height: 16px;
                display: inline-block;
                position: relative;
                top: 4px;
                left: 5px;
                margin: 0px;
                padding: 0px;
            }
        ');

        // popup script
        $this->view->headScript()->appendScript('
            Popup = function(obj, template)
            {
                var self = this;
                var winHeight = jQuery(window).height();

                this.mask = jQuery(' . '"<div id=\'mask\' class=\'hidden\'></div>"' . ');
                this.popup = jQuery(' . '"<div id=\'popup\' class=\'hidden\' ></div>"' . ');

                var body = jQuery("body");
                var template = jQuery(template);

                var showPopup = undefined;

                template.appendTo(this.popup);
                this.popup.appendTo(body);
                this.mask.appendTo(body);

                //@todo переделать!!!!
                this.showPopupLink = jQuery("#showPopup");
                this.showPopupIcon = this.showPopupLink.next("#showPopupIcon");
                
                if (editor == "")
                {
                    editor = new Editor("#YMapsEditor", 127.534135, 50.274938, 13);
                }



                jQuery(this.mask).click(
                    function()
                    {
                        self.togglePopup();
                    }
                );
            }

            Popup.prototype.setMarkedOnMap = function(placemark)
            {
                if (placemark != "")
                {
                    this.showPopupLink.html("Указано на карте");
                    this.showPopupIcon.removeClass("hidden");
                }
            }
            
            Popup.prototype.togglePopup = function()
            {
                if(jQuery(this.popup).hasClass("hidden"))
                {
                    // if invisible
                    if(jQuery.browser.msie)
                    {
                        jQuery(this.mask).height(jQuery(document).height()).toggleClass("hidden");
                    }
                    else
                    {
                        jQuery(this.mask).height(jQuery(document).height()).toggleClass("hidden").fadeTo("slow", 0.6);
                    }
                    jQuery(this.popup).toggleClass("hidden");
                }
                else
                {
                    // if visible
                    jQuery(this.mask).toggleClass("hidden");
                    jQuery(this.popup).toggleClass("hidden");
                }

                if (editor.placemark != ""){
                    this.showPopupLink.html("Указано на карте");
                    this.showPopupIcon.removeClass("hidden");
                } else {
                    this.showPopupLink.html("' . $value['title'] . '");
                    this.showPopupIcon.addClass("hidden");
                }

                return false;
            };
        ');

        $this->view->headScript()->appendScript('
            var editor = "";
            var popupp = "";
            jQuery(document).ready(
                function()
                {
                    popupp = new Popup("ololo", ' . '"<div id=\"YMapsEditor\" style=\"width:100%;height:100%\"></div>" ' . ');
                    popupp.setMarkedOnMap(editor.placemark);

                    jQuery("#showPopup").click(
                        function()
                        {
                            popupp.togglePopup();
                            editor.setMapCenter();

                            return false;
                        }
                    );

                    jQuery("form").submit(
                        function()
                        {
                            if (editor.placemark != "")
                            {
                                var point = editor.placemark.getGeoPoint();
                                jQuery("[name=\'latitude\']").val(point.getX());
                                jQuery("[name=\'longitude\']").val(point.getY());
                            }
                            return true;
                        }
                    );

                    jQuery("#street").focusout(
                        function()
                        {
                            editor.setMapCenter();
                        }
                    );

                    jQuery("#house_num").focusout(
                        function()
                        {
                            editor.setMapCenter();
                        }
                    );
                }
            );
        ');

        $html = '<a href="#" class="" id="showPopup">' . $value['title'] . '</a><span id="showPopupIcon" class="hidden"></span>';
        $html .= '<input type="hidden" name="longitude">';
        $html .= '<input type="hidden" name="latitude">';




        // @todo временно введенные для тестирования поля
//        $html .= '<input type="hidden" id="city" value="Благовещенск">';
//        $html .= '<input type="hidden" id="street" value="Институтская">';
//        $html .= '<input type="hidden" id="number" value="15">';

        return $html;
    }
}
