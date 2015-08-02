<div ng-controller="achievmentsController as ac" class="ui segment bottom attached">
    <div class="ui huge breadcrumb">
        <a class="section" href="/">Домены</a>
        <i class="right chevron icon divider"></i>
        <div class="active section">Конфигурация</div>
    </div>
    <div class="ui segment">
        <pre ng-controller="CodeInjectorController as cic">
&lt;link rel="stylesheet" type="text/css" href="http://webachievs.ru/css/WebAchieves.css"&gt;
&lt;script type="application/javascript" src="http://webachievs.ru/js/WebAchieves.js"&gt;&lt;/script&gt;
&lt;script&gt;
    $(function () {
		var wa = new WebAchieves({
			domain_id: {{cic.domain_id}}
		});
	});
&lt;/script&gt;
        </pre>
    </div>
</div>