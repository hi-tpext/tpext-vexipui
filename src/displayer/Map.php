<?php

namespace tpext\builder\displayer;

use tpext\builder\common\Module;

class Map extends Text
{
    protected $view = 'map';

    protected $type = 'amap';

    protected $prefix = '<i class="mdi mdi-map-marker-radius"></i>';

    protected $jsOptions = [];

    protected $height = '450px';

    protected $width = '100%';

    protected $defaultLat = '102.709629,24.847463';

    protected $propBind = false;

    /**
     * Undocumented function
     *
     * @param string|int $val
     * @return $this
     */
    public function height($val)
    {
        if (is_numeric($val)) {
            $val .= 'px';
        }
        $this->height = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|int $val
     * @return $this
     */
    public function width($val)
    {
        if (is_numeric($val)) {
            $val .= 'px';
        }
        $this->width = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function zoom($val)
    {
        $this->jsOptions['zoom'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function amap()
    {
        $this->type = 'amap';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function baidu()
    {
        $this->type = 'baidu';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function google()
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function tcent()
    {
        $this->type = 'tcent';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function yandex()
    {
        $this->type = 'yandex';
        return $this;
    }

    /**
     * 自己实现的地图，或者地图jsapi更新后已不适用需要重写js
     *
     * @return $this
     */
    public function other()
    {
        $this->type = 'other';
        return $this;
    }

    protected function fieldScript()
    {
        $config = Module::getInstance()->getConfig();

        if ($this->type == 'amap') {
            $this->amapScript($config['amap_js_key']);
        } else if ($this->type == 'baidu') {
            $this->js[] = $config['baidu_map_js_key'];
            $this->baiduScript();
        } else if ($this->type == 'tcent') {
            $this->tcentScript($config['tcent_map_js_key']);
        } else if ($this->type == 'yandex') {
            $this->js[] = $config['yandex_map_js_key'];
            $this->yandexScript();
        } else if ($this->type == 'other') {
            // 自己实现
        }

        $fieldId = $this->getId();

        $script = <<<EOT

    const {$fieldId}Search = ref('');
    
EOT;
        $this->setupScript[] = $script;
        $this->addVueToken(["{$fieldId}Search"]);
    }

    public function customVars()
    {
        return array_merge(parent::customVars(), [
            'maptype' => $this->type,
            'placeholder' => $this->placeholder ?: __blang('bilder_please_select') . $this->label,
            'mapStyle' => 'style="width: ' . $this->width . ';height: ' . $this->height . ';max-width: 100%;"',
        ]);
    }

    protected function tcentScript($jsKey)
    {
        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        $value = $this->renderValue();

        $position = array_filter(explode(',', $value), 'strlen');
        if ($value == ',' || count($position) != 2) {
            $position = [24.847463, 102.709629];
            $value = '24.847463,102.709629';
        } else {
            $value = $position[1] . ',' . $position[0];
        }

        $this->jsOptions = array_merge([
            'zoom' => 15,
            'panControl' => true,
            'zoomControl' => true,
            'scaleControl' => true,
        ], $this->jsOptions);

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $readonly = $this->isReadonly() || $this->isDisabled() ? 'true' : 'false';

        $script = <<<EOT

        var readonly = {$readonly};

        window.tcentInit = function() {
            var center = new qq.maps.LatLng({$value});

            var map = new qq.maps.Map(document.getElementById("map-{$fieldId}"), {
                center : center,
                {$configs}
            });

            var marker = new qq.maps.Marker({
                position: center,
                draggable: true,
                map: map
            });

            if(!readonly)
            {
                if(!{$VModel}) {
                    var citylocation = new qq.maps.CityService();
                    citylocation.setComplete(function(result) {
                        map.setCenter(result.detail.latLng);
                        marker.setPosition(result.detail.latLng);
                        {$VModel} = result.detail.latLng.getLng() + ',' + result.detail.latLng.getLat();
                    });
                    citylocation.searchLocalCity();
                }

                qq.maps.event.addListener(map, 'click', function(event) {
                    marker.setPosition(event.latLng);
                    {$VModel} = event.latLng.getLng() + ',' + event.latLng.getLat();
                });

                qq.maps.event.addListener(marker, 'dragend', function() {
                    var pp = marker.getPosition();
                    {$VModel} = pp.getLng() + ',' + pp.getLat();
                });

                $('#search-{$fieldId} input').attr('id' ,'search-{$fieldId}-input');

                var ap = new qq.maps.place.Autocomplete(document.getElementById('search-{$fieldId}-input'));
                var searchService = new qq.maps.SearchService({
                    map : map
                });

                qq.maps.event.addListener(ap, "confirm", function(res) {
                    searchService.search(res.value);
                });
            }
        }

        var url = '{$jsKey}&callback=tcentInit';

        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = url;
        document.body.appendChild(script);
EOT;
        $this->onMountedScript[] = $script;
    }

    protected function yandexScript()
    {
        //未做测试
        $fieldId = $this->getId();
        $VModel = $this->getVModel();
        $value = $this->renderValue();

        $position = array_filter(explode(',', $value), 'strlen');
        if ($value == ',' || count($position) != 2) {
            $position = [24.847463, 102.709629];
            $value = '24.847463,102.709629';
        } else {
            $position = [$position[1], $position[0]];
            $value = $position[1] . ',' . $position[0];
        }

        $this->jsOptions = array_merge([
            'center' => $position,
            'zoom' => 14,
        ], $this->jsOptions);

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $readonly = $this->isReadonly() || $this->isDisabled() ? 'true' : 'false';

        $script = <<<EOT

        var readonly = {$readonly};

        ymaps.ready(function() {
            var myMap = new ymaps.Map('map-{$fieldId}', {
                {$configs}
            });

            var myPlacemark = new ymaps.Placemark([{$value}], {
            }, {
                preset: 'islands#redDotIcon',
                draggable: true
            });

            if(!readonly) {
                myPlacemark.events.add(['dragend'], function (e) {
                    {$VModel} = myPlacemark.geometry.getCoordinates()[1] + ',' + myPlacemark.geometry.getCoordinates()[0];
                });
            }

            myMap.geoObjects.add(myPlacemark);
        });
EOT;
        $this->onMountedScript[] = $script;
    }

    protected function baiduScript()
    {
        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        $value = $this->renderValue();

        $position = array_filter(explode(',', $value), 'strlen');
        if ($value == ',' || count($position) != 2) {
            $position = [102.709629, 24.847463];
            $value = '102.709629,24.847463';
        }

        $this->jsOptions = array_merge([
            'zoom' => 14,
        ], $this->jsOptions);

        $zoom = $this->jsOptions['zoom'];

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $readonly = $this->isReadonly() || $this->isDisabled() ? 'true' : 'false';

        $script = <<<EOT

        var readonly = {$readonly};
        var map = new BMap.Map("map-{$fieldId}");
        var point = new BMap.Point({$value});
        map.centerAndZoom(point, {$zoom});

        var marker = new BMap.Marker(point);        // 创建标注
        map.addOverlay(marker);

        if(!readonly) {
            if(!{$VModel}) {
                var geolocation = new BMap.Geolocation();
                geolocation.getCurrentPosition(function(r) {
                    if(this.getStatus() == BMAP_STATUS_SUCCESS) {
                        marker.setPosition(r.point);
                        map.panTo(r.point);
                        {$VModel} = r.point.lng + ',' + r.point.lat;
                    }
                    else {
                        console.log('failed' + this.getStatus());
                    }
                });
            }

            marker.enableDragging();
            marker.addEventListener("dragend", function(e) {
                {$VModel} = e.point.lng + ',' + e.point.lat;
            })

            map.addEventListener("click", function(e) {
                marker.setPosition(e.point);
                {$VModel} = e.point.lng + ',' + e.point.lat;
            });

            $('#search-{$fieldId} input').attr('id' ,'search-{$fieldId}-input');

            var ac = new BMap.Autocomplete({"input" :  "search-{$fieldId}-input", "location" : map});

            var myValue;

            ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
                var _value = e.item.value;
                myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
                setPlace();
            });

            function setPlace() {
                function myFun() {
                    var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
                    map.centerAndZoom(pp, {$zoom});
                    marker.setPosition(pp);
                    {$VModel} = e.point.lng + ',' + e.point.lat;
                }
                var local = new BMap.LocalSearch(map, { //智能搜索
                    onSearchComplete: myFun
                });
                local.search(myValue);
            }
        }

        map.addControl(new BMap.NavigationControl());
        map.addControl(new BMap.ScaleControl());
        map.addControl(new BMap.OverviewMapControl());
EOT;
        $this->onMountedScript[] = $script;
    }

    protected function amapScript($jsKey)
    {
        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        if (is_array($this->default)) {
            $this->default = implode(',', $this->default);
        }

        $value = $this->renderValue();

        $position = array_filter(explode(',', $value), 'strlen');
        if ($value == ',' || count($position) != 2) {
            $position = [102.709629, 24.847463];
            $value = '102.709629,24.847463';
        }

        $this->jsOptions = array_merge([
            'center' => $position,
            'zoom' => 15,
        ], $this->jsOptions);

        $zoom = $this->jsOptions['zoom'];

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $jscode = '';

        if (preg_match('/jscode=([^&]+)/i', $jsKey, $mch)) {
            $jscode = $mch[1]; //得到安全密钥
            $jsKey = str_replace(['&jscode=', $jscode], '', $jsKey); //替换url中的安全密钥
        }

        $readonly = $this->isReadonly() || $this->isDisabled() ? 'true' : 'false';

        $script = <<<EOT

        var readonly = {$readonly};

        window._AMapSecurityConfig = {
            securityJsCode : '{$jscode}',
        }

        window.amapInit = function() {
            var map = new AMap.Map('map-{$fieldId}', {
                {$configs}
            });

            var marker = new AMap.Marker({
                draggable: true,
                position: new AMap.LngLat({$value}),   // 经纬度对象，也可以是经纬度构成的一维数组
            });

            // 将创建的点标记添加到已有的地图实例：
            map.add(marker);

            if(readonly) {
                return;
            }

            map.on('click', function(e) {
                marker.setPosition(e.lnglat);
                {$VModel} = e.lnglat.getLng() + ',' + e.lnglat.getLat();
            });

            marker.on('dragend', function (e) {
                {$VModel} = e.lnglat.getLng() + ',' + e.lnglat.getLat();
            });

            if(!{$VModel}) {
                map.plugin('AMap.Geolocation', function () {
                    geolocation = new AMap.Geolocation();
                    map.addControl(geolocation);
                    geolocation.getCurrentPosition();
                    AMap.event.addListener(geolocation, 'complete', function (data) {
                        marker.setPosition(data.position);
                        {$VModel} = data.position.getLng() + ',' + data.position.getLat();
                    });
                });
            }

            $('#search-{$fieldId} input').attr('id' ,'search-{$fieldId}-input');

            AMap.plugin('AMap.Autocomplete',function() {
                var autoOptions = {
                    input : "search-{$fieldId}-input"
                };

                var autocomplete= new AMap.Autocomplete(autoOptions);
                AMap.event.addListener(autocomplete, "select", function(data) {
                    map.setZoomAndCenter({$zoom}, data.poi.location);
                    marker.setPosition(data.poi.location);
                    {$VModel} = data.poi.location.lng + ',' + data.poi.location.lat;
                });
            });
        }

        var url = '{$jsKey}&callback=amapInit';

        var jsapi = document.createElement('script');
        jsapi.charset = 'utf-8';
        jsapi.src = url;
        document.body.appendChild(jsapi);
EOT;
        $this->onMountedScript[] = $script;
    }
}
