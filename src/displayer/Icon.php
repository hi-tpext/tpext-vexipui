<?php

namespace tpext\builder\displayer;

class Icon extends Text
{
    protected $view = 'icon';

    protected $default = 'mdi mdi-access-point';

    protected $jsonUrl = '/assets/tpextvexipui/fontjson/materialdesignicons.json';

    protected $propBind = false;

    /**
     * Undocumented function
     *
     * @param string $url
     * @return $this
     */
    public function jsonUrl($url)
    {
        $this->jsonUrl = $url;
        return $this;
    }

    protected $jsOptions = [
        'ModalOp' => [
            'top' => '10%',
            'width' => '542px',
            'modal-class' => 'icon-selector-modal',
            'no-footer' => true,
            'transfer' => true,
        ],
        'gridOp' => [
            'show-header' => false,
            'height' => 260,
            'border' => true,
            'highlight' => false,
            'virtual' => true,
        ],
        'visible' => false,
    ];

    protected function fieldScript()
    {
        $fieldId = $this->getId();
        $VModel = $this->getVModel();
        $url = $this->jsonUrl;
        $fieldName = $this->getName();

        $script = <<<EOT

    let {$fieldId}IconList = [];
    let {$fieldId}Data = [];
    let {$fieldId}Row = null;//如果是在items中，保持当前行的实例
    const {$fieldId}Icon = ref(VexipIcon.MagnifyingGlass);
    const {$fieldId}ActivePage = ref(1);
    const {$fieldId}PageSize = ref(112);
    const {$fieldId}TableData = ref([]);
    const {$fieldId}Kwd = ref('');

    const {$fieldId}PagerConfig = ref({
        'size-options': [112, 192, 256, 320, 448, 576, 640, 768, 896, 1024, 2048],
        'total': 0,
        'size': 'small',
        'plugins': ['total', 'size'],
    });

    const {$fieldId}GetData = () => {
        const offset = ({$fieldId}ActivePage.value - 1) * {$fieldId}PageSize.value;
        {$fieldId}TableData.value = {$fieldId}Split({$fieldId}Data.slice(offset, offset + {$fieldId}PageSize.value));
        {$fieldId}PagerConfig.value.total = {$fieldId}Data.length;
    };

    const {$fieldId}Split = (list) => {
        let rowsize = 16;
        let rows = Math.ceil(list.length / rowsize);
        let result = [];
        let row = [];
        for (let j = 0; j < list.length; j+=1) {
            row.push(list[j]);
            if (row.length === rowsize || j === list.length - 1) {
                result.push(row);
                row = [];
            }
        }
        return result;
    };

    const {$fieldId}OpenIconSelector = (tRow) => {
        {$fieldId}Row = tRow;
        {$fieldId}Op.value.visible = !{$fieldId}Op.value.visible;
    };

    const {$fieldId}CellClick = ({row, columnIndex}) => {
        if({$fieldId}Row) {
            {$fieldId}Row.{$fieldName} = row[columnIndex].name;
        } else {
            {$VModel} = row[columnIndex].name;
        }
        {$fieldId}Op.value.visible = false;
    };

    const {$fieldId}Search = () => {
        let value = {$fieldId}Kwd.value;
        if(!value.trim()) {
            {$fieldId}Data = {$fieldId}IconList;
        }
        else{
            {$fieldId}Data = {$fieldId}IconList.filter(d => d.name.indexOf(value.trim()) > -1);
        }
        {$fieldId}ActivePage.value = 1;
        {$fieldId}GetData();
    };

    let {$fieldId}SearchChangeTimer = null;

    const {$fieldId}SearchInput = (value) => {
        if({$fieldId}SearchChangeTimer) {
            clearTimeout({$fieldId}SearchChangeTimer);
        }
        {$fieldId}SearchChangeTimer = setTimeout(() => {
            if(!value.trim()) {
                {$fieldId}Data = {$fieldId}IconList;
            }
            else{
                {$fieldId}Data = {$fieldId}IconList.filter(d => d.name.indexOf(value.trim()) > -1);
            }
            {$fieldId}ActivePage.value = 1;
            {$fieldId}GetData();
        }, 100);
    };

    const {$fieldId}Open = () => {
        if({$fieldId}IconList.length) {
            {$fieldId}GetData();
            return;
        }
        axios({
            method: 'get',
            url: '{$url}',
            responseType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            timeout: 60000,
        }).then(res => {
            let list = res.data.glyphs || [];
            {$fieldId}IconList = list.map(d => { return {name: d.css } });
            {$fieldId}Data = {$fieldId}IconList;
            if({$fieldId}Row) {
                if(list.length && !{$fieldId}Row.{$fieldName}) {
                    {$fieldId}Row.{$fieldName} = {$fieldId}IconList[0].name;
                }
            } else {
                if(list.length && !{$VModel}) {
                    {$VModel} = {$fieldId}IconList[0].name;
                }
            }
            {$fieldId}GetData();
        })
        .catch(e => {
            console.log(e);
            VxpMessage.error(__blang.bilder_network_error + (e.message || JSON.stringify(e)));
        });
    };
EOT;

        $this->setupScript[] = $script;

        $this->addVueToken([
            "{$fieldId}SearchInput",
            "{$fieldId}Search",
            "{$fieldId}OpenIconSelector",
            "{$fieldId}CellClick",
            "{$fieldId}Open",
            "{$fieldId}PagerConfig",
            "{$fieldId}Icon",
            "{$fieldId}ActivePage",
            "{$fieldId}PageSize",
            "{$fieldId}TableData",
            "{$fieldId}GetData",
            "{$fieldId}Kwd",
        ]);
    }
}
