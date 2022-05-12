<style>

#profile-page h3{
    text-align: center;
}


#title {
  color: #5B5F63;
}

</style>

{{include file="addon/recomendapp/templates/badge_level.tpl"}}

<div id='profile-page' class='generic-page-wrapper'>
<h3 id='title'>{{$title}}</h3>

<br/>

{{if $coments}}
{{foreach $coments as $perfil_comment}}
{{include file='addon/recomendapp/templates/coment.tpl'}}
{{/foreach}}
{{else}}
<div>
<p> Não existem comentarios</p>
</div>
{{/if}}

{{if $show_form}}
{{include file='addon/recomendapp/templates/coment_form.tpl'}}
{{/if}}