if (!window.jQuery) {
    document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>')
}
var WebAchives = function (params) {
    var that = this;
    if (typeof $ == 'undefined') {
        var $ = jQuery;
    }
    var DOMAIN = 'http://mobmind.ru/';

    this.templates = {
        achieve: '<div class="achieve">' +
        '<div class="close"><a href="javascript:;" onclick="$(this).parents(\'.achieve\').first().remove()">X</a></div>' +
        '<div class="image"><img src="{image}"/></div>' +
        '<div class="content">' +
        '<div class="header">{title}</div>' +
        '<div class="text"><b>Требование:</b> {text}</div>' +
        '</div>' +
        '</div>'
    };
    this.printf = function (template, data) {
        var result = template;
        $.each(data, function (key, val) {
            result = result.replace('{' + key + '}', val);
        });
        return result;
    };
    this.achievesBox = $('<div id="WebAchievesBox"></div>').appendTo('body');
    this.startCheck = function () {
        $.ajax({
            url: DOMAIN + 'api/check/' + params.domain_id,
            type: 'post',
            data: {url: window.location.href, session_id: that.session},
            dataType: 'json',
            success: function (data) {
                $.each(data, function (key, val) {
                    that.showAchieve(val);
                });
            },
            error: function (data) {
                console.log(data);
            }
        });
    };
    window.addEventListener("message", function(e) {
            var matches = e.data.match(new RegExp('^session=(.*?)$'));
            if (matches) {
                that.session = matches[1];
                that.startCheck();
            }
        },
        false);
    $('body').append('<iframe src="http://mobmind.ru/iframe.html" style="display: none"></iframe>');

    this.showAchieve = function (data) {
        this.achievesBox.append(this.printf(this.templates.achieve, data))
    };
    return {};
};