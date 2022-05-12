<div id='profile-page' class='generic-page-wrapper'>
<h3>Disciplinas</h3>

<!--p>Nenhuma disciplina disponivel</p-->
{{if $dps}}
{{foreach $dps as $dp}}
<h4><a href='/recomendapp/disciplinas/id/{{$dp.ID}}'>{{$dp.Nome}}</a></h4>
{{/foreach}}
{{else}}
<h4>Não há disciplinas cadastradas</h4>
{{/if}}
</div>