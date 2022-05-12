<style>

.coment-box-section {
    margin-bottom: 50px;
}

.coment-box {
    display: inline;
}

.coment-box img {
    float: left;
}


#profile {
    padding: 5px;
}

#delete {
    border: none;
    background-color: white;
    float: right;
}

.like-cbox {
    margin-left: 50px;
    float:left;
    display: inline;
}

.like-cbox p {
    vertical-align: middle;
    display: inline;
}

.like-cbox p:nth-child(2) {
    margin: auto 5px;
}


.like-cbox #like {
    width: 20px;
    height: 20px;
    vertical-align: middle;
}

.like-cbox #deslike {
    -webkit-transform: scaleX(-1);
    transform: scaleX(-1);
    width: 14px;
    height: 14px;
}

.like-cbox #badge {
    width: 20px;
    height: 25px;
}

#display-content-comment {
    display: block;
    margin-left:2px;
    word-wrap:break-word;
    overflow: hidden;
}

</style>
<div class='coment-box-section'>
<form method="post">
{{if $user_id == $perfil_comment.id_perfil}}
  <input type='submit' id='delete' name='delete' value='&#x22EE;'/>
{{/if}}
<div class='coment-box'>
   <img id='profile' src={{$perfil_comment.photo}} width='50px' height="50px"/>
   <div id='display-content-comemnt'>
    <p><b>{{$perfil_comment.name}}</b></p>
    <p>{{$perfil_comment.comment}}</p>
   </div>
 </div>
 <input type='hidden' id='type' name='type' value='1' />
 <input type='hidden' id='id' name='id' value='{{$user_id}}' /> 
 <input type='hidden' id='id_coment' name='id_coment' value='{{$perfil_comment.id}}' />
 </form>
 <form class='like-cbox' method="post">
    <p><input type='image' id='like' name='like' value='like' src={{$like}} /> {{$perfil_comment.likes}} </p>
    <p><input type='image' id='deslike' name='deslike' value='deslike' src={{$deslike}} /> {{$perfil_comment.deslikes}} </p>
    {{if $perfil_comment.badge}}
    <img id='badge' src={{$perfil_comment.badge}} />
    {{/if}}
    <input type='hidden' id='type' name='type' value='1' />
    <input type='hidden' id='id' name='id' value='{{$user_id}}' />
    <input type='hidden' id='id_profile_coment' name='id_profile_coment' value='{{$perfil_comment.id_perfil}}' /> 
    <input type='hidden' id='id_coment' name='id_coment' value='{{$perfil_comment.id}}' />
</form>
</div>