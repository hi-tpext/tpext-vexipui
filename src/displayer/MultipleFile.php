<?php

namespace tpext\builder\displayer;

use tpext\think\App;
use tpext\builder\common\Module;
use tpext\builder\logic\ImageHandler;
use tpext\builder\traits\HasImageDriver;
use tpext\builder\traits\HasStorageDriver;

/**
 * MultipleFile class
 * @method $this  image()
 * @method $this  office()
 * @method $this  video()
 * @method $this  audio()
 * @method $this  pkg()
 */
class MultipleFile extends Field
{
    use HasStorageDriver;
    use HasImageDriver;

    protected $view = 'multiplefile';
    protected $placeholder = '';
    protected $canUpload = true;
    protected $showInput = true;
    protected $showChooseBtn = true;
    protected $showUploadBtn = true;
    protected $cover = '/assets/tpextvexipui/images/cover/file.svg';
    protected $__default = '/assets/tpextvexipui/images/ext/0.png';
    protected $propBind = false;
    protected $postAsString = false;

    protected $jsOptions = [
        'accept' => '', //mimeTypes
        'url' => '',
        'data' => [],
        'allow-drag' => false,
        'default-files' => [],
        'list-type' => 'thumbnail',
        'multiple' => true,
        'hidden-files' => true,
        'field' => 'file',
        'with-credentials' => false,
        //非组件参数
        'ext' => [
            //
            'jpg',
            'jpeg',
            'gif',
            'wbmp',
            'webp',
            'png',
            'bmp',
            'ico',
            'swf',
            'psd',
            'jpc',
            'jp2',
            'jpx',
            'jb2',
            'swc',
            'iff',
            'xbm',
            'svg',
            //
            "flv",
            "mkv",
            "avi",
            "rm",
            "rmvb",
            "mpeg",
            "mpg",
            "ogv",
            "mov",
            "wmv",
            "mp4",
            "webm",
            //
            "ogg",
            "mp3",
            "wav",
            "mid",
            //
            "rar",
            "zip",
            "tar",
            "gz",
            "7z",
            "bz2",
            "cab",
            "iso",
            //
            "doc",
            "docx",
            "xls",
            "xlsx",
            "ppt",
            "pptx",
            "pdf",
            "txt",
            "md",
            //
            "xml",
            "json",
        ],
        'fileSingleSizeLimit' => 250 * 1024 * 1024,
        'fileNumLimit' => 5,
        'fileSizeLimit' => 0,
        'thumbnailWidth' => 80,
        'thumbnailHeight' => 80,
        'isImage' => false,
        'chooseUrl' => '',
        'chooseLayerSize' => ['98%', '98%']
    ];

    protected $extTypes = [
        'image' => ['jpg', 'jpeg', 'gif', 'wbmp', 'webp', 'png', 'bmp', 'ico', 'swf', 'psd', 'jpc', 'jp2', 'jpx', 'jb2', 'swc', 'iff', 'xbm', 'svg'],
        'office' => ["doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf"],
        'video' => ["flv", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg", "ogv", "mov", "wmv", "mp4", "webm"],
        'audio' => ["ogg", "mp3", "wav", "mid"],
        'pkg' => ["rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso"],
    ];

    protected $coverList = [
        'image' => '/assets/tpextvexipui/images/cover/image.svg',
        'office' => '/assets/tpextvexipui/images/cover/office.svg',
        'video' => '/assets/tpextvexipui/images/cover/video.svg',
        'audio' => '/assets/tpextvexipui/images/cover/audio.svg',
        'pkg' => '/assets/tpextvexipui/images/cover/pkg.svg',
    ];

    /**
     * Undocumented function
     * 对老版本参数兼容
     * @param array $options
     * @return $this
     */
    public function jsOptions($options)
    {
        $newOptions = [];
        foreach ($options as $k => $v) {
            if ($k == 'limit') {
                $newOptions['fileNumLimit'] = $v;
            } else if ($k == 'mimeTypes' || $k == 'accept') {
                if ($v == '*/*') {
                    $v = '';
                }
                $newOptions['accept'] = $v;
            } else if ($k == 'upload_url') {
                $newOptions['url'] = $v;
            } else {
                $newOptions[$k] = $v;
            }
        }
        $this->jsOptions = array_merge($this->jsOptions, $newOptions);

        return $this;
    }

    /**
     * Undocumented function
     * 
     * @return bool
     */
    public function isInput()
    {
        return $this->canUpload;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function placeholder($val)
    {
        $this->placeholder = $val;
        return $this;
    }

    /**
     * 可以上传
     *
     * @param boolean $val
     * @return $this
     */
    public function canUpload($val = true)
    {
        $this->canUpload = $val;
        return $this;
    }

    /**
     * 是否显示文件输入框
     *
     * @param boolean $val
     * @return $this
     */
    public function showInput($val = true)
    {
        $this->showInput = $val;
        return $this;
    }

    /**
     * 是否显示[选择已上传文件]按钮
     *
     * @param boolean $val
     * @return $this
     */
    public function showChooseBtn($val = true)
    {
        $this->showChooseBtn = $val;
        return $this;
    }

    /**
     * 是否显示[上传新文件]按钮
     *
     * @param boolean $val
     * @return $this
     */
    public function showUploadBtn($val = true)
    {
        $this->showUploadBtn = $val;
        return $this;
    }

    /**
     * 同时禁用[上传新文件][选择已上传文件]
     * 可通过cover图片控制
     * 
     * @param boolean $val
     * @return $this
     */
    public function disableButtons($val = true)
    {
        $this->showUploadBtn = !$val;
        $this->showChooseBtn = !$val;

        return $this;
    }

    /**
     * 累计文件数量限制
     * 
     * @param int $val
     * @return $this
     */
    public function limit($val)
    {
        $this->jsOptions['limit'] = $val;
        return $this;
    }

    /**
     * 占位图片，设置为空则不显示
     *
     * @param string|false $val
     * @return $this
     */
    public function cover($val)
    {
        $this->cover = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function smallSize()
    {
        $this->jsOptions['thumbnailWidth'] = 50;
        $this->jsOptions['thumbnailHeight'] = 50;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function mediumSize()
    {
        $this->jsOptions['thumbnailWidth'] = 120;
        $this->jsOptions['thumbnailHeight'] = 120;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function bigSize()
    {
        $this->jsOptions['thumbnailWidth'] = 240;
        $this->jsOptions['thumbnailHeight'] = 240;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function largeSize()
    {
        $this->jsOptions['thumbnailWidth'] = 480;
        $this->jsOptions['thumbnailHeight'] = 480;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $w
     * @param integer $h
     * @return $this
     */
    public function thumbSize($w, $h)
    {
        $this->jsOptions['thumbnailWidth'] = $w;
        $this->jsOptions['thumbnailHeight'] = $h;

        return $this;
    }

    /**
     * 提交时是否把数组转成字符串
     * 
     * @param boolean $val
     * @return $this
     */
    public function postAsString($val = true)
    {
        $this->postAsString = $val;
        return $this;
    }

    /**
     * Undocumented function
     * @return $this
     */
    public function beforRender()
    {
        $token = $this->getCsrfToken();
        $this->jsOptions['url'] = (string)url($this->getUploadUrl(), [
            'utype' => 'webuploader',
            'token' => $token,
            'driver' => $this->getStorageDriver(),
            'is_rand_name' => $this->isRandName(),
            'image_driver' => $this->getImageDriver(),
            'image_commonds' => $this->getImageCommands()
        ]);
        $this->jsOptions['chooseUrl'] = url(Module::getInstance()->getChooseUrl()) . '?';
        return parent::beforRender();
    }

    /**
     * @return string|array
     */
    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $this->canUpload = !$this->readonly && $this->canUpload;
        if (!$this->canUpload) {
            if (empty($this->default)) {
                $this->default = $this->__default;
            }
        }

        $value = !($this->value === '' || $this->value === null || $this->value === []) ? $this->value : $this->default;

        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        if (count($value) > $this->jsOptions['fileNumLimit']) {
            $value = array_slice($value, 0, $this->jsOptions['fileNumLimit']);
        }

        $this->renderValue = implode(',', array_filter($value, 'strlen'));

        return $this->renderValue;
    }

    protected function fieldScript()
    {
        $fieldId = $this->getId();
        if ($this->disabled || $this->readonly || !$this->canUpload) {
            return;
        }
        $VModel = $this->getVModel();
        $fieldName = $this->getName();
        $hasCover = $this->cover ? 'true' : 'false';

        $script = <<<EOT

    const {$fieldId}Ref = ref(null);
    const {$fieldId}UploadRef = ref(null);
    const {$fieldId}UploadingNum = ref(0);
    const {$fieldId}HasCover = {$hasCover};
    let {$fieldId}Row = null;//如果是在items中，保持当前行的实例

    const {$fieldId}FileNum = computed({
        get() {
            return {$fieldId}Row ? {$fieldId}Row.{$fieldName}.split(',').filter(x => x.trim()).length : {$VModel}.split(',').filter(x => x.trim()).length;
        }
    });

    const {$fieldId}GetpreviewUrl = (url) => {
        if(__isImage(url, {$fieldId}Op.value)) {
            return url;
        }
        return '/index/file/extimg?type=' + url.replace(/.+?\.(\w+)$/, '$1');
    }

    const {$fieldId}UploadFile = (row) => {
        {$fieldId}Row = row;
        if({$fieldId}Op.value.fileNumLimit > 1 && {$fieldId}FileNum >= {$fieldId}Op.value.fileNumLimit) {
            VxpMessage.warning(__blang.bilder_maximum_upload_files_num_is + {$fieldId}Op.value.fileNumLimit);
            return false;
        }
        {$fieldId}UploadRef.value.click();
    };

    const {$fieldId}ChooseFile = (row) => {
        {$fieldId}Row = row;
        if({$fieldId}Op.value.fileNumLimit > 1 && {$fieldId}FileNum >= {$fieldId}Op.value.fileNumLimit) {
            VxpMessage.warning(__blang.bilder_maximum_upload_files_num_is + {$fieldId}Op.value.fileNumLimit);
            return false;
        }

        let chooseUrl = {$fieldId}Op.value.chooseUrl || '/admin/attachment/index?';
        let size = {$fieldId}Op.value.chooseLayerSize || ['98%', '98%'];

        layer.open({
            type: 2,
            title: __blang.bilder_choose_uploaded_file,
            shadeClose: false,
            scrollbar: false,
            shade: 0.3,
            anim: 2,    //从最底部往上滑入
            area: size,
            content: chooseUrl + 'choose=1&id={$fieldId}&limit=' + {$fieldId}Op.value.fileNumLimit + '&ext=' + {$fieldId}Op.value.ext.join(','),
            success: function (layero, index) {
                window.onChooseFile = (fileUrls) => {
                    {$fieldId}PushFiles(fileUrls);
                    nextTick(() => {
                        layer.close(index);
                    });
                };
                $(':focus').blur();
                this.enterEsc = function (event) {
                    if (event.keyCode === 13) {
                        return false; //阻止系统默认回车事件
                    }
                    if (event.keyCode === 0x1B) {
                        var index2 = layer.msg(__blang.bilder_confirm_close_this_window, {
                            time: 2000,
                            btn: [__blang.bilder_button_ok, __blang.bilder_button_cancel],
                            yes: function (params) {
                                layer.close(index);
                                layer.close(index2);
                            }
                        });
                        return false; //阻止系统默认esc事件
                    }
                };
                $(document).on('keydown', this.enterEsc);	//监听键盘事件，关闭层
            },
            end: function () {
                $(document).off('keydown', this.enterEsc);	//解除键盘关闭事件
            }
        });
    };

    const {$fieldId}RemoveFile = (row, index) => {
        {$fieldId}Row = row;
        VxpConfirm.open({
            title : __blang.bilder_operation_tips,
            content: __blang.bilder_confirm_to_remove_file,
            confirmText : __blang.bilder_button_ok,
            cancelText : __blang.bilder_button_cancel,
        }).then((res) => {
            if(res) {
                let files = {$fieldId}Row ? {$fieldId}Row.{$fieldName}.split(',').filter(x => x.trim()) : {$VModel}.split(',').filter(x => x.trim());
                files.splice(index, 1);
                if({$fieldId}Row) {
                    {$fieldId}Row.{$fieldName} = files.join(',');
                } else {
                    {$VModel} = files.join(',');
                }
            }
        });
    };

    const {$fieldId}BeforeAddFile = (file) => {
        if({$fieldId}Op.value.fileNumLimit > 1 && {$fieldId}FileNum >= {$fieldId}Op.value.fileNumLimit) {
            VxpMessage.warning(__blang.bilder_maximum_upload_files_num_is + {$fieldId}Op.value.fileNumLimit);
            return false;
        }
        return true;
    };

    const {$fieldId}BeforeUpload = (file) => {
        return new Promise((resolve, reject) => {
            if({$fieldId}Op.value.fileNumLimit > 1 && {$fieldId}FileNum >= {$fieldId}Op.value.fileNumLimit) {
                VxpMessage.warning(__blang.bilder_maximum_upload_files_num_is + {$fieldId}Op.value.fileNumLimit);
                reject();
            }
            if({$fieldId}Op.value.fileSingleSizeLimit && file.size > {$fieldId}Op.value.fileSingleSizeLimit) {
                let limitSize = {$fieldId}Op.value.fileSingleSizeLimit > 1024 * 1024 ? ({$fieldId}Op.value.fileSingleSizeLimit/1024/1024).toFixed(2) + 'MB' : ({$fieldId}Op.value.fileSingleSizeLimit/1024).toFixed(2) +'KB';
                VxpMessage.warning(__blang.bilder_file_size_cannot_exceed + ({$fieldId}Op.value.fileSingleSizeLimit / 1024) + 'kb' + __blang.bilder_please_upload_again);
                reject();
            }
            let ext = file.name.replace(/.+?\.(\w+)$/, '$1');
            if(!{$fieldId}Op.value.ext.includes(ext)) {
                VxpMessage.warning(file.name + __blang.bilder_file_type_suffix_allowed_is + ':' + {$fieldId}Op.value.ext.join(', '));
                reject();
            }
            {$fieldId}Op.value.data = {
                name: file.name,
                size: file.size,
                type: file.type,
                lastModifiedDate: new Date(file.lastModified).toLocaleString()
            };
            resolve();
        })
    };

    const {$fieldId}Error = (file, error) => {
        VxpNotice.open({
            type: 'error',
            content: __blang.bilder_file_uploading_failed,
            placement: 'top-right',
            duration: 2000,
        });
    }

    const {$fieldId}Success = (file, response) => {
        console.log(file, response);
        {$fieldId}PushFiles(file.response.url);
        VxpNotice.open({
            type: 'success',
            content: __blang.bilder_file_uploading_succeeded,
            placement: 'top-right',
            duration: 2000,
        });
    }

    const {$fieldId}Progress = (file, percent) => {
        
    }

    const {$fieldId}PushFiles = (urls) => {
        let arr = urls.split(',').filter(x => x.trim());
        if(!arr.length) {
            return;
        }
        if({$fieldId}Op.value.fileNumLimit < 2) {
            if({$fieldId}Row) {
                {$fieldId}Row.{$fieldName} = arr[0];
            }
            else {
                {$VModel} = arr[0];
            }
            return;
        }
        let files = {$fieldId}Row ? {$fieldId}Row.{$fieldName}.split(',').filter(x => x.trim()) : {$VModel}.split(',').filter(x => x.trim());
        arr.forEach(url => {
            if (files.length >= {$fieldId}Op.value.fileNumLimit) {
                return;
            }
            files.push(url);
        });
        if({$fieldId}Row) {
            {$fieldId}Row.{$fieldName} = files.join(',');
        } else {
            {$VModel} = files.join(',');
        }
    };

EOT;
        $this->setupScript[] = $script;
        $this->addVueToken([
            "{$fieldId}Ref",
            "{$fieldId}UploadRef",
            "{$fieldId}UploadFile",
            "{$fieldId}ChooseFile",
            "{$fieldId}RemoveFile",
            "{$fieldId}BeforeUpload",
            "{$fieldId}BeforeAddFile",
            "{$fieldId}Error",
            "{$fieldId}Success",
            "{$fieldId}Progress",
            "{$fieldId}GetpreviewUrl",
        ]);

        $script = <<<EOT
        //兼容jquery修改input的值 $('#inputid').val('url').trigger('change');
        $('#{$fieldId}-input-div').find('input').attr('id', '{$fieldId}');
        
EOT;

        $this->onMountedScript[] = $script;

        if ($this->postAsString) {
            $VModel = $this->getVModel();

            $script = <<<EOT

        if (Array.isArray({$VModel})) {
            {$VModel} = {$VModel}.join(',');
        }

EOT;
            $this->convertScript[] = $script;
        }
    }

    public function customVars()
    {
        return [
            'canUpload' => $this->canUpload,
            'showInput' => $this->showInput,
            'cover' => $this->cover,
            'showChooseBtn' => $this->showChooseBtn,
            'showUploadBtn' => $this->showUploadBtn,
            'thumbnailStyle' => 'style="width:' . $this->jsOptions['thumbnailWidth'] . 'px;height:' . $this->jsOptions['thumbnailHeight'] . 'px;"',
            'placeholder' => $this->placeholder ?: __blang('bilder_please_select') . $this->label
        ];
    }

    /**
     * Undocumented function
     *
     * @param string|array $types ['jpg', 'jpeg', 'gif'] or 'jpg,jpeg,gif'
     * @return $this
     */
    public function extTypes($types)
    {
        $this->jsOptions['ext'] = is_string($types) ? explode(',', $types) : $types;
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (isset($this->extTypes[$name])) {
            $this->jsOptions['ext'] = $this->extTypes[$name];
            if ($this->cover) {
                $this->cover = $this->coverList[$name];
            }
            return $this;
        }

        throw new \InvalidArgumentException(__blang('bilder_invalid_argument_exception') . ' : ' . $name);
    }

    /**
     * 获取缩略图
     * @return array
     */
    public function thumbs()
    {
        $files = explode(',', $this->renderValue());
        if ($this->canUpload) { //可上传图片时，不使用缩略图
            return [];
        }

        $handler = new ImageHandler();
        $options = [
            'width' => $this->jsOptions['thumbnailWidth'] * 2,
            'height' => $this->jsOptions['thumbnailHeight'] * 2,
        ];

        if (!is_dir(App::getPublicPath() . '/thumb/')) {
            mkdir(App::getPublicPath() . '/thumb/', 0777, true);
        }

        $thumbs = [];

        foreach ($files as $file) {
            if (strstr($file, '/assets/tpextbuilder/images/')) {
                $thumbs[] = $file;
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'])) {
                $thumbs[] = $file;
                continue;
            }

            $thumbFile = '/thumb/' . md5($file) . '-' . $options['width'] . 'x' . $options['height'] . '.' . $ext;

            if (is_file(App::getPublicPath() .$thumbFile)) {
                $thumbs[] = $thumbFile;
                continue;
            }

            if (strstr($file, 'http')) {
                $data = @file_get_contents($file);
                if (!$data) {
                    $thumbs[] = $file;
                    continue;
                }
                if (!@file_put_contents(App::getPublicPath() . $thumbFile, $data)) {
                    $thumbs[] = $file;
                    continue;
                }
                $file = $thumbFile;
            } else if (!is_file(App::getPublicPath() . $file)) {
                $thumbs[] = $file;
                continue;
            }
            try {
                $options['to_path'] = App::getPublicPath() . $thumbFile;
                $thumbs[] = $handler->resize($file, $options);
            } catch (\Exception $e) {
                $thumbs[] = $file;
            }
        }

        return $thumbs;
    }
}
