<style>
    #recomend {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    #recomend td, #recomend th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    #recomend tr:nth-child(even){background-color: #f2f2f2;}

    #recomend tr:hover {
      background-color: #ddd;
      cursor: pointer;  
    }

    #recomend tr th:hover{
      background-color: #ddd;
    }

    #recomend tr a {
      text-decoration: none;
      color: black;
    }

    #recomend tr a:hover {
      color: black;
    }

    #recomend th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #FFF;
      color: black;
      border-bottom: #000;
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

{{include file="addon/recomendapp/templates/form_subdp.tpl"}}

<div id='profile-page' class='generic-page-wrapper'>
{{include file="addon/recomendapp/templates/feedback_rec_form.tpl"}}
<h3 id='title'><b>Recomendados</b></h3>

<table id="recomend">
    <tr>
    {{if $comunidade}}
    <th>Disciplinas recomendadas pela comunidade</th>
    {{else}}
    <th>Disciplinas que você pode gostar</th>
    {{/if}}
    </tr>
    {{if $Recomendados}}
    {{foreach $Recomendados as $recomendado}}
      </tr><td><a onclick="openFormDP('{{$recomendado.Nome}}')">{{$recomendado.Nome}}</a></td></tr>
    {{/foreach}}
    {{else}}
      <tr><td>Não existem recomendados no momento</td></tr>
    {{/if}}
</table>

<table id="recomend">
    <tr><th>Disciplinas que talvez você goste</th></tr>
    {{if $NRecomendados}}
    {{foreach $NRecomendados as $recomendado}}
      <tr><td><a onclick="openFormDP('{{$recomendado.Nome}}')">{{$recomendado.Nome}}</a></td></tr>
    {{/foreach}}
    {{else}}
      <tr><td>Não existem recomendados no momento</td></tr>
    {{/if}}
</table>

<table id="recomend">
    <tr><th>Projetos que você vai gostar</th></tr>
    <tr><td>Intelgência de Dados</td></tr>
</table>

</div>