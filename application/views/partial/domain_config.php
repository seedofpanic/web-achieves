<div ng-class="{show: domain && !(domain.id > 0)}" class="protected">
    <div class="ui message error">
        У вас нет доступа к данному разделу
    </div>
</div>
<div ng-controller="achievmentsController as ac" class="ui segment bottom attached">
    <div class="ui huge breadcrumb">
        <a class="section" href="/">Домены</a>
        <i class="right chevron icon divider"></i>
        <div class="active section">{{domain.name}} - Конфигурация</div>
    </div>
    <div class="ui segment">
        <pre ng-controller="CodeInjectorController as cic">
&lt;link rel="stylesheet" type="text/css" href="http://webachievs.ru/css/WebAchieves.css"&gt;
&lt;script type="application/javascript" src="http://webachievs.ru/js/WebAchieves.min.js"&gt;&lt;/script&gt;
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