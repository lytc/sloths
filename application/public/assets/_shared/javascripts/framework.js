$(function() {
    var $doc = $(document)
    var $globalAlert = $('#global-alert')

    $doc.ajaxError(function(e, xhr) {
        if (xhr.status === 0) {
            return
        }

        $globalAlert
            .removeClass('alert-success alert-info alert-warning')
            .addClass('alert-danger')
            .html(xhr.responseText)
            .slideDown()
    })


    $doc.on('click', 'a[data-method]', function(e) {
        e.preventDefault()
        var me = $(this)
        var data = me.data()
        var method = data.method
        var url = me.attr('href')

        var $form = $('<form action="' + url + '" method="POST"><input type="hidden" name="_method" value="' + method + '"></form>')

        if (data.ajax !== undefined) {
            $form.attr('data-ajax', 1)
        }

        if (data.confirm !== undefined) {
            $form.attr('data-confirm', data.confirm)
        } else if (method == 'DELETE' && data.confirm === undefined) {
            $form.attr('data-confirm', '')
        }

        $form.appendTo(document.body)
        $form.submit()
    })

    $doc.on('submit', '[data-ajax]', function(e) {
        e.preventDefault()

        var $form = $(this)
        var data = $form.data()

        var confirmMessage = data.confirm === ''? 'Are you sure?' : data.confirm

        if (confirmMessage && !confirm(confirmMessage)) {
            return
        }


        var method = data.method
        var $btnSubmit = $form.find(':submit')
        $btnSubmit.data('loading-text') || $btnSubmit.data('loading-text', '<i class="fa fa-spinner fa-spin"></i> Saving...')
        $btnSubmit.button('loading')

        var $alert = $form.find('.form-alert')

        if (!$alert.length) {
            $alert = $globalAlert
        }

        $alert.slideUp(0)
        $form.find('.form-group').removeClass('has-error')

        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            headers: {
                'X-Http-Method-Override': method
            },
            data: $form.serialize()
        }).done(function(result) {
            if (result == 1) {
                result = {success: true}
            }

            if ($.isPlainObject(result) && undefined === result.success) {
                result.success = true
            }

            if (!result.messages) {
                result.messages = result.success? 'All change saved!' : 'Whoops! Something went wrong!'
            }

            var messageHtml = '';

            if (typeof result.messages !== 'string') {
                var name

                for (name in result.messages) {
                    var $input = $form.find('[name=' + name + ']')
                    var $label = $form.find('label[for=' + name + ']')
                    if (!$label.length) {
                        $label = $input.parent().prev('label')
                    }

                    messageHtml += $label.html() + ' ' + result.messages[name]
                    messageHtml = '<div>' + messageHtml + '</div>'

                    $input.closest('.form-group').addClass('has-error')
                }
            } else {
                messageHtml = result.messages
            }

            $alert.removeClass('alert-success alert-danger')
            $alert.addClass('alert alert-' + (result.success? 'success' : 'danger'))
            $alert.html(messageHtml)
            $alert.slideDown(200)

        }).always(function() {
            $btnSubmit.button('reset')
        })
    })

    // grid
    $doc.on('click', '.grid [data-action=DELETE]', function(e) {
        e.preventDefault()
        var me = $(this)
        var data = me.data()

        if (data.confirm === undefined) {
            data.confirm = ''
        }

        var confirmMessage = data.confirm === ''? 'Are you sure?' : data.confirm

        if (confirmMessage && !confirm(confirmMessage)) {
            return
        }

        me.addClass('disabled')
        var $icon = me.find('i.fa').addClass('fa-spin')

        $.ajax({
            type: 'POST',
            url: me.attr('href'),
            headers: {
                'X-Http-Method-Override': 'DELETE'
            }
        }).done(function(result) {
            if (result == 1) {
                result = {success: true}
            }

            if ($.isPlainObject(result) && undefined === result.success) {
                result.success = true
            }

            if (result.success) {
                me.closest('tr').fadeOut(function() {
                    $(this).remove()
                })
            }

        }).always(function() {
            me.removeClass('disabled')
            $icon.removeClass('fa-spin')
        })
    })
})