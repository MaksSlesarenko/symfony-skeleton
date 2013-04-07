$(function(){
    
    $('<div id="loading">').ajaxStart(function () {
        $(this).show();
    }).ajaxComplete(function () {
        $(this).hide();
    }).appendTo($('.nav-collapse'));
    
    $("#alert-messages").ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        if (ajaxSettings.dataType === 'json') {
            try {
                var data = jQuery.parseJSON(jqXHR.responseText);
                
                displayAlerts(data.flashbag);
            } catch (error) {
                displayAlerts({error: ['Error requesting page "' + ajaxSettings.url + '"']});
            }
        }
    }).ajaxSuccess(function (event, jqXHR, ajaxSettings) {
        if (ajaxSettings.dataType === 'json') {
            try {
                var data = jQuery.parseJSON(jqXHR.responseText);
                displayAlerts(data.flashbag);
            } catch (error) {
                return;
            }
        }
    }).children().each(function(i){
        $(this).delay(4000 + (i * 1000)).fadeOut();
    });
    
    $('body').on('click', '.ajax-modal', function(e) {
        e.preventDefault();

        var $this = $(this);
        var url   = $this.attr('href');
        

        $.get(url, function(data) {
            var dialog = $('<div class="modal hide fade"></div>').appendTo('body');
            dialog.append(data);
            dialog.modal();

            
            $('.ajax-form').ajaxForm({
                dataType: 'json',
                success: function(data) {
                    ajaxFormHandler(data, dialog, $this);
                }
            });
        });
    });
    
    function ajaxFormHandler(data, dialog, el){
        if (true === data.success) {
            dialog.modal('hide');
            
            var callback = el.attr('data-callback');

            if (window[callback]) {
                window[callback].call(el);
            }
        } else {
            dialog.html(data.form).find('form').ajaxForm({
                dataType: 'json',
                success: function(data) {
                    ajaxFormHandler(data, dialog, el);
                }
            });
        }
    }
});

function displayAlerts(flashbag)
{
    var result = false;
    for (var type in flashbag) {
        for (var i in flashbag[type]) {
            createAlert(type, flashbag[type][i]);
            
            result = true;
        }
    }
    return result;
}

function createAlert(type, message)
{
    return $('\
    <div class="hide alert alert-block alert-'+type+'">\
        <button type="button" class="close" data-dismiss="alert">&times;</button>\
        <p>'+message+'</p>\
    </div>').appendTo($("#alert-messages")).fadeIn().delay(4000).fadeOut();
}