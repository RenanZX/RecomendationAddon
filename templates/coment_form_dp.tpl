<style>
.comment-form {
    position: fixed;
    display: block;
    background-color: #FFF;
    padding: 10px;
    bottom: 0;
    left: 31.1%;
    width: 655px;
}

.comment-form div:first-child {
    display: block;
    margin-bottom: 35px;
}


#profile {
    padding: 5px;
}

.comment-form #coment-section {
    display: inline;
}

.comment-form #coment-text {
    rows: 1;
    width: 580px;
    height: 30px;
    resize: none;
    overflow: hidden;
    border-color:transparent;
    border-bottom: 1px solid black;
}

.comment-form .display-coment-button {
    display:none;
    float: right;
}

.display-coment-button input {
    background-color: #004080; 
    color: white;
    text-decoration: none;
    border: none;
    padding: 4px;
}

.display-button {
    display: block;
    margin-left: 30px;
    float: right;
}

.display-button #star {
    width: 300px; 
    height: 50px; 
    object-fit: cover; 
    object-position: 0 100%;
}

#form2 {
    display: none;
}

</style>
<script>
window.onload = function(){
    document.getElementById('star').addEventListener('mousemove', e=>{

        let x = e.layerX;
        let y = e.layerY;
        if(x > 325){
            e.target.style.objectPosition = '0 0%';
        }else if(x <= 325 && x > 252){
            e.target.style.objectPosition = '0 25%';
        }else if(x <= 252 && x > 202){
            e.target.style.objectPosition = '0 50%';
        }else if(x <= 202 && x > 138){
            e.target.style.objectPosition = '0 75%';
        }else if(x <= 138 && x > 0){
            e.target.style.objectPosition = '0 100%';
        }
    });
}

function eraseInput(field){
    document.getElementsByClassName('display-coment-button')[0].style.display = 'block';
}

function Cancelar(){
    document.getElementsByClassName('display-coment-button')[0].style.display = 'none';
}

function ShowDPs(){
    document.getElementById('form1').style.display = 'none';
    document.getElementById('form2').style.display = 'block';
}

</script>

<form class="comment-form" method="post">
<div id='form1'>    
    <div id='coment-section'>
        <img id='profile' src={{$profile['photo']}} width='50px' height="50px"/>

        <textarea id='coment-text' name='coment-text' placeholder="Insira o seu comentario" onclick="eraseInput(this);"></textarea>
    </div>
    <br/>
    <div class='display-coment-button'>
        {{if !$dp_comentada}}
            <input type='button' value='Comentar' onclick="ShowDPs()"/>
        {{else}}
            <input type='button' value='Editar' onclick='ShowDPs()'/>
        {{/if}}
        <input type='button' value='Cancelar' onclick='Cancelar()'/>
    </div>
</div>
<div id='form2'>
  <div style='display:inline-block;'>
    <img id='profile' src={{$profile['photo']}} width='50px' height="50px"/>
     <div class='display-button'>
        <p>O que você achou dessa disciplina:</p>
        <input type='image' id='star' name='star' src='{{$star_comment}}'/>
    </div> 
  </div>
  <input type='hidden' id='id_dp' name='id_dp' value='{{$dp_id}}' />
  <input type='hidden' id='id_user' name='id_user' value='{{$user_id}}' />
  <input type='hidden' id='type' name='type' value='3' />
</div>
</form>