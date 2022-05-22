<style>

/* The Modal Style (background) */
.modal-box-dp {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal-box-dp-content {
  background-color: #fefefe;
  margin: 15% auto; /* 15% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 80%; /* Could be more or less, depending on screen size */
  height: auto;
}

/* The Close Button */
.closebtn {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.closebtn:hover,
.closebtn:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

.modal-box-dp-content ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

.modal-box-dp-content ul li:first-child {
    border-top: 1px solid black;
}

.modal-box-dp-content input[type=submit] {
    background: none;
    border: none;
    color: black;
    text-decoration: none;
}

.modal-box-dp-content ul li {
    padding: 10px;
    border-bottom: 1px solid black;
    cursor: pointer;
}

.modal-box-dp-content ul li:hover {
    padding: 10px;
    background-color: #f4f4f4;
    border-bottom: 1px solid black;
    cursor: pointer;
}

.comment-form {
    position: fixed;
    display: block;
    background-color: #FFF;
    padding: 10px;
    bottom: 0;
    left: 31.1%;
}

#profile {
    padding: 5px;
}

.comment-form div:first-child {
    display: block;
    margin-bottom: 35px;
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

.display-button input {
    background-color: #004080; 
    color: white;
    text-decoration: none;
    border: none;
    padding: 4px;
}

#form2 {
    display: none;
}
</style>
<script>
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

function ShowMore(){
    document.getElementsByClassName('modal-box-dp')[0].style.display = 'block';
}

function closeModal(){
    document.getElementsByClassName('modal-box-dp')[0].style.display = 'none';
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
        <input type='button' value='Comentar' onclick="ShowDPs()"/>
        <input type='button' value='Cancelar' onclick='Cancelar()'/>
    </div>
</div>
<div id='form2'>
  <div style='display:inline-block;'>
    <img id='profile' src={{$profile['photo']}} width='50px' height="50px"/>
     <div class='display-button'>
        <p>Qual disciplina ou projeto {{$profile_name}} mais se destaca:</p>
        <div>
        {{if $dps_user}}
        {{foreach $dps_user as $dp }}
           <input type='submit' name='disciplina' value='{{$dp}}' />
        {{/foreach}}
        <input type='button' value='Outros' onclick="ShowMore()"/>
        {{/if}}
        </div>
    </div>
    <div class='modal-box-dp'>
      <div class="modal-box-dp-content">
        <span class="close" onclick="closeModal()">&times;</span>
        {{if $all_dps}}
        <ul>
        {{foreach $all_dps as $dp}}
            <li><input type='submit' name='disciplina' value='{{$dp}}'/></li>
        {{/foreach}}
        {{/if}}
        </ul>
      </div>
    </div>
  </div>
  <input type='hidden' id='id_user' name='id_user' value='{{$user_id}}'' />
  <input type='hidden' id='type' name='type' value='2' />
</div>
</form>