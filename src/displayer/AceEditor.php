<?php

namespace tpext\builder\displayer;

class AceEditor extends Field
{
    protected $view = 'aceeditor';

    protected $js = [
        //core
        '/assets/tpextvexipui/js/ace/ace.js',
        //ext
        '/assets/tpextvexipui/js/ace/ext-beautify.js',
        '/assets/tpextvexipui/js/ace/ext-error_marker.js',
        '/assets/tpextvexipui/js/ace/ext-language_tools.js',
        '/assets/tpextvexipui/js/ace/ext-keybinding_menu.js',
        '/assets/tpextvexipui/js/ace/ext-searchbox.js',
        '/assets/tpextvexipui/js/ace/ext-spellcheck.js',
        '/assets/tpextvexipui/js/ace/ext-static_highlight.js',
        '/assets/tpextvexipui/js/ace/ext-statusbar.js',
        //mode
        '/assets/tpextvexipui/js/ace/mode-css.js',
        '/assets/tpextvexipui/js/ace/mode-text.js',
        '/assets/tpextvexipui/js/ace/mode-html.js',
        '/assets/tpextvexipui/js/ace/mode-javascript.js',
        //theme
        '/assets/tpextvexipui/js/ace/theme-one_dark.js', //dark
        '/assets/tpextvexipui/js/ace/theme-textmate.js', //bright
    ];

    protected $jsOptions = [
        'mode' => 'text',
        'dark' => true,
        'fontSize' => 14,
        'height' => '1000px',
        'width' => '100%',
        //
        'enableBasicAutocompletion' => true,
        'enableSnippets' => true,
        'enableLiveAutocompletion' => true,
    ];

    protected function fieldScript()
    {
        $configs = json_encode($this->jsOptions);

        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        $readonly = $this->readonly || $this->disabled ? 'true' : 'false';

        $script = <<<EOT
        
    var configs = {$configs};
    var readonly = {$readonly};

    $('#{$fieldId}-editor').css({
        position: 'relative',
        width: configs.width,
        height: configs.height,
    });

    var aceeditor = ace.edit("{$fieldId}-editor");
    aceeditor.setTheme("ace/theme/"+ (configs.dark ? 'one_dark' : 'textmate'));
    aceeditor.session.setMode("ace/mode/" + configs.mode);
    aceeditor.setFontSize(configs.fontSize);
    
    aceeditor.setOptions({
        enableBasicAutocompletion: configs.enableBasicAutocompletion,
        enableSnippets: configs.enableSnippets,
        enableLiveAutocompletion: configs.enableLiveAutocompletion
    });

    aceeditor.resize();
    aceeditor.setReadOnly(readonly);
    aceeditor.getSession().setUseWrapMode(true);
    aceeditor.setShowPrintMargin(false);
    aceeditor.setValue($('#{$fieldId}').val());
    aceeditor.moveCursorTo(0, 0);
    aceeditor.session.on('change', function(e) {
        {$VModel} = aceeditor.getValue();
    });
       
EOT;
        $this->onMountedScript[] = $script;
    }

    /**
     * 设置代码语言模式
     *
     * @param string $val css/html/javascript/text
     * @return $this
     */
    public function setMode($val = 'text')
    {
        $this->jsOptions['mode'] = $val;

        return $this;
    }

    /**
     * 设置是否为黑色模式
     *
     * @param boolean $val
     * @return $this
     */
    public function setDark($val = true)
    {
        $this->jsOptions['dark'] = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    protected function vBindOp()
    {
        return $this;
    }
}
