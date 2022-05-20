<style>

#coment-section {
    overflow-y: auto;
}

</style>

<div id='profile-page' class='generic-page-wrapper'>
<h3>{{$title}}</h3>
<p><b>Decricao:</b> {{$description}}</p>

<br/>
<h4>Comentários</h4>

<div id='coment-section'>
{{if $coments}}
{{foreach $coments as $perfil_comment}}
{{include file='addon/recomendapp/templates/coment_dp.tpl'}}
{{/foreach}}
{{else}}
<div>
<p> Não existem comentarios</p>
</div>
{{/if}}
</div>

{{include file='addon/recomendapp/templates/coment_form_dp.tpl'}}

</div>