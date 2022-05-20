<style>

#profile-page {
  overflow-y: auto;
}

#coment-page-section {
  overflow-y: auto;
}

#profile-page h3{
    text-align: center;
}

#title {
  color: #5B5F63;
}

</style>

{{if $show_level}}
{{include file="addon/recomendapp/templates/badge_level.tpl"}}
{{/if}}

<div id='profile-page' class='generic-page-wrapper'>
<h3 id='title'><b>{{$title}}</b></h3>

<br/>
<div id='coment-page-section'>
{{if $coments}}
{{foreach $coments as $perfil_comment}}
{{include file='addon/recomendapp/templates/coment.tpl'}}
{{/foreach}}
{{else}}
<div>
<p> Não existem comentarios no momento.</p> 
{{if $show_form}}
<p> Comente alguma experiência com seu colega em uma disciplina ou monitoria.</p>
{{/if}}
</div>
{{/if}}
</div>

{{if $show_form}}
{{include file='addon/recomendapp/templates/coment_form.tpl'}}
{{/if}}
</div>