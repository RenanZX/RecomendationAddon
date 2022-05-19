<style>

.coment-box-section {
    margin-bottom: 50px;
}

.coment-box {
    display: inline;
}


#profile {
    padding: 5px;
}

.coment-box img {
    float: left;
}

.coment-box #star_coment {
    width: 80px; 
    height: 12.5px; 
    margin-right: 5px;
    object-fit: cover; 
    object-position: 0 50%;
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
    float: right;
}

#display-content-comment {
    display: block;
    margin-left:2px;
    word-wrap:break-word;
    overflow: hidden;
}


</style>

<script>

function Display_Avaliation(stars, element){
    let x = stars == 5? ('0 0%') : stars == 4? ('0 25%') : stars == 3? ('0 50%') : stars == 2? ('0 75%') : ('0 100%');
    element.style.objectPosition = x;
}
</script>

<div class='coment-box-section'>
 <div class='coment-box'>
   <img id='profile' src={{$perfil_comment.photo}} width='50px' height="50px"/>
   <div style='display: block;'>
    <img id='star_coment' src='{{$star_comment}}' onload="Display_Avaliation({{$perfil_comment.stars}}, this)" />
    <p><b>  {{$perfil_comment.name}}</b></p>
    <p>{{$perfil_comment.comment}}</p>
    <form class='like-cbox' method="post">
        <p><input type='image' id='like' name='like' value='like' src={{$like}} /> {{$perfil_comment.likes}} </p>
        <p><input type='image' id='deslike' name='deslike' value='deslike' src={{$deslike}} /> {{$perfil_comment.deslikes}} </p>
        {{if $perfil_comment.badge}}
        <img id='badge' src={{$perfil_comment.badge}} />
        {{/if}}
        <input type='hidden' id='type' name='type' value='1' />
        <input type='hidden' id='type_coment' name='type_coment' value='2' />
        <input type='hidden' id='id' name='id' value='{{$user_id}}' />
        <input type='hidden' id='id_coment' name='id_coment' value='{{$perfil_comment.id}}' />
    </form>
   </div>
 </div>
</div>