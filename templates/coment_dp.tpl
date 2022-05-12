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
   </div>
 </div>
</div>