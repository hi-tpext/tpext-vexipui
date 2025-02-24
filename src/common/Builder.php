<?php

namespace tpext\builder\common;

use tpext\think\App;
use tpext\think\View;
use think\facade\Session;
use tpext\common\ExtLoader;
use tpext\builder\inface\Auth;
use tpext\builder\tree\Tree;
use tpext\builder\inface\Renderable;

class Builder implements Renderable
{
    protected $view = '';

    protected $layout = '';

    protected $title = '';

    protected $desc = '';

    protected $csrf_token = '';

    /**
     * Undocumented variable
     *
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Undocumented variable
     *
     * @var Row
     */
    protected $__row__ = null;

    protected $js = [];

    protected $customJs = [];

    protected $css = [];

    protected $vueImport = ['createApp', 'onMounted', 'computed', 'onBeforeMount', 'nextTick', 'ref', 'reactive', 'watch'];

    protected $customCss = [];

    protected $styleSheet = [];

    protected $onMountedScript = [];

    protected $setupScript = [];

    protected $notify = [];

    protected $vueTokens = [];

    protected $componentsImport = [
        'Loading as VxpLoading',
        'Alert as VxpAlert',
        'useModal as VxpModal',
        'Message as VxpMessage',
        'Notice as VxpNotice',
        'Confirm as VxpConfirm',
    ];

    protected $layer;

    protected $commonJs = [
        '/assets/tpextvexipui/lib/axios.min.js',
        '/assets/tpextvexipui/js/jquery-3.7.min.js',
        '/assets/tpextvexipui/js/layui/layui.js',
        '/assets/tpextvexipui/js/tpextbuilder.js',
    ];

    protected $commonCss = [
        '/assets/tpextvexipui/lib/vexip-ui.css',
        '/assets/tpextvexipui/lib/dark/index.css',
        '/assets/tpextvexipui/js/layui/css/layui.css',
        '/assets/tpextvexipui/css/vex.css',
        '/assets/tpextvexipui/css/materialdesignicons.min.css',
    ];

    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected static $auth;

    protected static $minify = false;

    protected static $aver = '1.0';

    protected static $instance = null;

    protected function __construct($title, $desc)
    {
        $this->title = $title;
        $this->desc = $desc;
    }

    /**
     * Undocumented function
     *
     * @param string $title
     * @param string $desc
     * @return static
     */
    public static function getInstance($title = '', $desc = '')
    {
        if (self::$instance == null) {
            self::$instance = new static($title, $desc);
            self::$instance->created();

            ExtLoader::trigger('tpext_create_builder', self::$instance);
        } else {
            if ($title) {
                self::$instance->title($title);
            }
            if ($desc) {
                self::$instance->desc($desc);
            }
        }

        return self::$instance;
    }

    /**
     * 销毁实列
     *
     * @return void
     */
    public static function destroyInstance()
    {
        if (self::$instance) {
            self::$instance->destroy();
            self::$instance = null;
        }
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    protected function created()
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function title($val)
    {
        $this->title = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function desc($val)
    {
        $this->desc = $val;
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param mixed $val
     * @return $this
     */
    public function layout($val)
    {
        $this->layout = $val;
        return $this;
    }

    /**
     * 设置视图模板路径，避免不同的应用中使用Builder时模板缓存冲突
     *
     * @param string $template
     * @return $this
     */
    public function setView($template)
    {
        $this->view = $template;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getCsrfToken()
    {
        if (!$this->csrf_token) {

            $token = Session::get('_csrf_token_');

            if (empty($token)) {
                $token = md5('_csrf_token_' . time() . uniqid());
                Session::set('_csrf_token_', $token);
            }

            $this->csrf_token = $token;
        }

        return $this->csrf_token;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function addJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->js = array_merge($this->js, $val);
        return $this;
    }

    /**
     * 添加自定义js，不会被minify
     *
     * @param array|string $val
     * @return $this
     */
    public function customJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->customJs = array_merge($this->customJs, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function addCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->css = array_merge($this->css, $val);
        return $this;
    }

    /**
     * 添加自定义css，不会被minify
     *
     * @param array|string $val
     * @return $this
     */
    public function customCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->customCss = array_merge($this->customCss, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function removeJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }

        foreach ($this->js as $k => $j) {
            if (in_array($j, $val)) {
                unset($this->js[$k]);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function removeCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }

        foreach ($this->css as $k => $c) {
            if (in_array($c, $val)) {
                unset($this->css[$k]);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @param string $newVal
     * @return $this
     */
    public function replaceJs($val, $newVal)
    {
        foreach ($this->js as $k => $j) {
            if ($val == $j) {
                $this->js[$k] = $newVal;
                break;
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @param string $newVal
     * @return $this
     */
    public function replaceCss($val, $newVal)
    {
        foreach ($this->css as $k => $c) {
            if ($val == $c) {
                $this->css[$k] = $newVal;
                break;
            }
        }

        return $this;
    }

    /**
     * add script to onMountedScript
     * 需要在onMounted中执行的 js script
     * 也可用于执行一些jquery方法
     * 
     * @param array|string $val
     * @return $this
     */
    public function addScript($val)
    {
        $this->addOnMountedScript($val);
        return $this;
    }

    /**
     * 需要在onMounted中执行的 js script
     * 也可用于执行一些jquery方法
     * 
     * @param array|string $scripts
     * @return $this
     */
    public function addOnMountedScript($scripts)
    {
        if (!is_array($scripts)) {
            $scripts = [$scripts];
        }
        foreach ($scripts as $script) {
            if (!empty($script)) {
                $this->onMountedScript[md5($script)] = $script;
            }
        }
        return $this;
    }

    /**
     * 需要在setup执行的js script
     *
     * @param array|string $scripts
     * @return void
     */
    public function addSetupScript($scripts)
    {
        if (!is_array($scripts)) {
            $scripts = [$scripts];
        }
        foreach ($scripts as $script) {
            if (!empty($script)) {
                $this->setupScript[md5($script)] = $script;
            }
        }
    }

    /**
     * 需要在setup中return的vue变量
     *
     * @param array|string $tokens
     * @return void
     */
    public function addVueToken($tokens)
    {
        if (!is_array($tokens)) {
            $tokens = [$tokens];
        }
        $this->vueTokens = array_merge($this->vueTokens, $tokens);
    }

    /**
     * 需要导入的Vue属性 默认 ['createApp', 'onMounted', 'computed', 'onBeforeMount', 'nextTick', 'ref', 'reactive', 'watch']
     *
     * @param array|string $names
     * @return void
     */
    public function addVueImport($names)
    {
        if (!is_array($names)) {
            $names = [$names];
        }
        $this->vueImport = array_merge($this->vueImport, $names);
    }

    /**
     * Undocumented function
     *
     * @param array|string $styles
     * @return $this
     */
    public function addStyleSheet($styles)
    {
        if (!is_array($styles)) {
            $styles = [$styles];
        }
        foreach ($styles as $style) {
            if (!empty($script)) {
                $this->styleSheet[md5($script)] = $style;
            }
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * return onMountedScript
     *
     * @return array
     */
    public function getScript()
    {
        return $this->onMountedScript;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getOnMountedScript()
    {
        return $this->onMountedScript;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getSetupScript()
    {
        return $this->setupScript;
    }

    /**
     * Undocumented function
     *
     * @return array
     */    public function getVueTokens()
    {
        return $this->vueTokens;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getStyleSheet()
    {
        return $this->styleSheet;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clearRows()
    {
        $this->rows = [];
        $this->__row__ = null;

        return $this;
    }

    /**
     * Undocumented function
     * lightyear.notify('修改成功，页面即将自动跳转~', 'success', 5000, 'mdi mdi-emoticon-happy', 'top', 'center');
     * @param string $msg
     * @param string $type
     * @param integer $delay
     * @param string $icon
     * @param string $from
     * @param string $align
     * @return $this
     */
    public function notify($msg, $type = 'info', $delay = 2000, $icon = '', $from = 'top-right', $align = 'center')
    {
        $this->notify = [$msg, $type, $delay, $icon, $from, $align];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * Undocumented function
     *
     * @return Row
     */
    public function row()
    {
        $row = Row::make();
        $this->rows[] = $row;
        $this->__row__ = $row;
        return $row;
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Column
     */
    public function column($size = 12)
    {
        if (!$this->__row__) {
            $this->row();
        }

        return $this->__row__->column($size);
    }

    /**
     * 获取一个form
     *
     * @param integer|string $size col大小
     * @return Form
     */
    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    /**
     * 获取一个表格
     *
     * @param integer|string $size col大小
     * @return Table
     */
    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    /**
     * 获取一个工具栏
     *
     * @param integer|string $size col大小
     * @return Toolbar
     */
    public function toolbar($size = 12)
    {
        return $this->column($size)->toolbar();
    }

    /**
     * 默认获取一个Tree树
     * 
     * @param integer|string $size col大小
     * @return Tree
     */
    public function tree($size = 12)
    {
        return $this->column($size)->tree();
    }

    /**
     * 获取一个Tree树
     * @deprecated tree()
     * @param integer|string $size col大小
     * @return Tree
     */
    public function zTree($size = 12)
    {
        return $this->column($size)->Tree();
    }

    /**
     * 获取一个Tree树
     * @deprecated tree()
     * @param integer|string $size col大小
     * @return Tree
     */
    public function jsTree($size = 12)
    {
        return $this->column($size)->Tree();
    }

    /**
     * 获取一自定义内容
     *
     * @param integer|string $size col大小
     * @return Content
     */
    public function content($size = 12)
    {
        return $this->column($size)->content();
    }

    /**
     * 获取一tab内容
     *
     * @param integer|string $size col大小
     * @return Tab
     */
    public function tab($size = 12)
    {
        return $this->column($size)->tab();
    }

    /**
     * 获取一个分割面板
     * 
     * @param integer|string $size
     * @return Split
     */
    public function Split($size = 12)
    {
        return $this->column($size)->Split();
    }

    /**
     * 获取一Swiper
     *
     * @param integer|string $size col大小
     * @return Swiper
     */
    public function swiper($size = 12)
    {
        return $this->column($size)->swiper();
    }

    /**
     * 获取layer
     *
     * @return Layer
     */
    public function layer(...$arguments)
    {
        if (!$this->layer) {
            $this->layer = Column::makeWidget('Layer', $arguments);
        }

        return $this->layer;
    }

    /**
     * Undocumented function
     *
     * @param string $template
     * @param array $vars
     * @param integer|string $size col大小
     * @return $this
     */
    public function fetch($template = '', $vars = [], $size = 12)
    {
        $this->content($size)->fetch($template, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param array $vars
     * @param integer|string $size col大小
     * @return $this
     */
    public function display($content = '', $vars = [], $size = 12)
    {
        $this->content($size)->display($content, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array|string
     */
    public function commonJs()
    {
        return $this->commonJs;
    }

    /**
     * Undocumented function
     *
     * @return array|string
     */
    public function commonCss()
    {
        return $this->commonCss;
    }

    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row->beforRender();
        }

        $this->addJs($this->commonJs());
        $this->addCss($this->commonCss());
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return void
     */
    public static function minify($val)
    {
        static::$minify = $val;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public static function isMinify()
    {
        return static::$minify;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return void
     */
    public static function aver($val)
    {
        static::$aver = $val;
    }

    /**
     * Undocumented function
     *
     * @param string|Auth $class
     * @return void
     */
    public static function auth($class)
    {
        static::$auth = $class;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return boolean
     */
    public static function checkUrl($url)
    {
        //如果不是完整的[moudle/controller/action]格式
        if (preg_match('/^\w+$/', $url) || preg_match('/^\w+(\.\w+)?\/\w+$/', $url)) {
            $url = url($url);
        }

        if (!empty(static::$auth)) {

            return static::$auth::checkUrl($url);
        }

        return true;
    }

    /**
     * 需要引入的UI opentiny Component
     *
     * @param array|string $components
     * @return void
     */
    public function addComponentImport($components)
    {
        if (!is_array($components)) {
            $components = [$components];
        }
        $this->componentsImport = array_merge($this->componentsImport, $components);
    }

    /**
     * Undocumented function
     *
     * @return View
     */
    public function render()
    {
        if ($this->layer) {
            return $this->layer->getViewShow();
        }

        $this->beforRender();

        if (empty($this->view)) {
            $this->view = Module::getInstance()->getViewsPath() . 'content.html';
        }

        if (!empty($this->notify)) {
            $script = <<<EOT

        VxpNotice({
            type: '{$this->notify[1]}',
            title: '{$this->notify[1]}',
            content: '{$this->notify[0]}',
            placement: {$this->notify[4]},
            duration: {$this->notify[2]},
            parseHtml : true,
        });

EOT;
            $this->onMountedScript[] = $script;
        }

        if (static::$minify) {
            $this->js = $this->customJs;
            $this->css = $this->customCss;
        } else {
            $this->js = array_merge($this->js, $this->customJs);
            $this->css = array_merge($this->css, $this->customCss);
        }

        foreach ($this->css as &$c) {
            if (strpos($c, '?') == false && strpos($c, 'http') == false) {
                $c .= '?aver=' . static::$aver;
            }
        }

        unset($c);

        foreach ($this->js as &$j) {
            if (strpos($j, '?') == false && strpos($j, 'http') == false) {
                $j .= '?aver=' . static::$aver;
            }
        }

        unset($j);

        $__blang = include Module::getInstance()->getRoot() . 'src' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . App::getDefaultLang() . '.php';

        if (empty($this->layout)) {
            $this->layout = Module::getInstance()->getViewsPath() . 'layout.html';
        }

        $vars = [
            'title' => $this->title ? $this->title : '',
            'desc' => $this->desc,
            'rows' => $this->rows,
            'vueImport' => implode(', ', array_unique($this->vueImport)),
            'componentsImport' => implode(',' . PHP_EOL . "\t", array_unique($this->componentsImport)),
            'onMountedScript' => implode('', array_values($this->onMountedScript)),
            'setupScript' => implode('', array_values($this->setupScript)),
            'vueTokens' => count($this->vueTokens) > 0 ? implode(',' . PHP_EOL . "\t\t", array_unique($this->vueTokens)) . "\n" : '',
            'admin_js' => array_unique($this->js),
            'admin_css' => array_unique($this->css),
            'stylesheet' => implode('', array_values($this->styleSheet)),
            '__blang' => json_encode($__blang, JSON_UNESCAPED_UNICODE),
            'aver' => static::$aver,
            'builderLayout' => $this->layout,
        ];

        View::share([
            '__token__' => $this->getCsrfToken(),
            'admin_page_title' => $this->desc,
            'admin_page_position' => $this->title
        ]);

        $viewshow = new View($this->view);

        return $viewshow->assign($vars);
    }

    public function __toString()
    {
        return $this->render()->getContent();
    }

    public function destroy()
    {
        foreach ($this->rows as $row) {
            $row->destroy();
        }

        $this->rows = null;
    }
}
