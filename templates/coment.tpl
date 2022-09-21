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

#formlike {
    display: none;
}

.like-cbox .like-form {
    display: none;
    margin-top: 20px;
    margin-bottom: 20px;
}

</style>
<script>
    function PostLike(event, id){ 
        let val = Math.floor(Math.random() * 2)
        let deslikeform = document.getElementById(`deslike-form-${id}`);
        let likeform = document.getElementById(`like-form-${id}`);
        if(deslikeform.style.display == 'block' || likeform.style.display == 'block'){
          event.preventDefault();
        }
        if(val){
          event.preventDefault();
          deslikeform.style.display = 'none';
          likeform.style.display = 'block';
        }
    }
    function PostDeslike(event, id){ 
        let val = Math.floor(Math.random() * 2)
        let deslikeform = document.getElementById(`deslike-form-${id}`);
        let likeform = document.getElementById(`like-form-${id}`);
        if(deslikeform.style.display == 'block' || likeform.style.display == 'block'){
          event.preventDefault();
        }
        if(val){
          event.preventDefault();
          likeform.style.display = 'none';
          deslikeform.style.display = 'block';
        }
    }
</script>
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
    <p><input type='image' id='like' name='like' value='like' src={{$like}} onclick='PostLike(event, {{$perfil_comment.id}})' /> {{$perfil_comment.likes}} </p>
    <p><input type='image' id='deslike' name='deslike' value='deslike' src={{$deslike}} onclick='PostDeslike(event, {{$perfil_comment.id}})' /> {{$perfil_comment.deslikes}} </p>
    {{if $perfil_comment.badge}}
    <img id='badge' src={{$perfil_comment.badge}} />
    {{/if}}
    <div class='like-form' id='like-form-{{$perfil_comment.id}}'>    
        <div id='coment-section'>
            <img id='profile' src={{$profile['photo']}} width='50px' height="50px"/>
            <textarea id='like-coment-text' name='like-coment-text' placeholder="Justifique a adição ou remoção do seu like..." onclick="eraseInput(this);"></textarea>
        </div>
        <br/>
        <div id='display-coment-button-like'>
            <input type='submit' id='justificarLike' name='justificarLike' value='Justificar'/>
            <input type='button' value='Cancelar' onclick='Cancelar()'/>
        </div>
    </div>
    <div class='like-form' id='deslike-form-{{$perfil_comment.id}}'>    
        <div id='coment-section'>
            <img id='profile' src={{$profile['photo']}} width='50px' height="50px"/>
            <textarea id='like-coment-text' name='like-coment-text' placeholder="Justifique a adição ou remoção do seu deslike..." onclick="eraseInput(this);"></textarea>
        </div>
        <br/>
        <div id='display-coment-button-like'>
            <input type='submit' id='justificarDeslike' name='justificarDeslike' value='Justificar'/>
            <input type='button' value='Cancelar' onclick='Cancelar()'/>
        </div>
    </div>
    <input type='hidden' id='type' name='type' value='1' />
    <input type='hidden' id='type_coment' name='type_coment' value='1' />
    <input type='hidden' id='id' name='id' value='{{$user_id}}' />
    <input type='hidden' id='id_profile_coment' name='id_profile_coment' value='{{$perfil_comment.id_perfil}}' /> 
    <input type='hidden' id='id_coment' name='id_coment' value='{{$perfil_comment.id}}' />
</form>
</div>