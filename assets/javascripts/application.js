$(function() {
    $('.code').each(function() {
        var $me = $(this)
        var type = $me.data('type')
        var code = this.tagName == 'TEXTAREA'? $me.val() : $me.html()
        code = code.replace(/ {4}/, '').replace(/\n {4}/g, '\n').replace(/\n$/, '')

        if (!type || type == 'php') {
            type = 'text/x-php'
        }

        if (type == 'sql') {
            type = 'text/x-mysql'
        }

        var config = {
            mode: type,
            lineNumbers: true,
            readOnly: true,
            viewportMargin: Infinity,
            indentUnit: 4,
            theme: 'mdn-like',
            value: code
        }

        var $code = $('<div>').replaceAll($me)
        CodeMirror($code.get(0), config)
    })
})