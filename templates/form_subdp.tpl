
<style>

/* The Modal Style (background) */
.modal-sub {
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
.modal-sub-content {
  background-color: #fefefe;
  margin: 15% auto; /* 15% from the top and centered */
  padding: 20px;
  border-radius: 20px;
  border: 1px solid #888;
  width: 45%; /* Could be more or less, depending on screen size */
  height: auto;
  text-align: center;
}

/* The Close Button */
.closebtn {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.closebtn:hover, .closebtn:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}


#sub {
    display: none;
     background-color: #fefefe;
     margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
     border: 1px solid #888;
     width: 80%; /* Could be more or less, depending on screen size */
     border-radius: 5px;
     text-align: center;
 }

 .modal-sub #form-buttons {
     display: inline;
 }

 .modal-sub #form-buttons input {
    border: 2px solid black;
    border-radius: 20px;
    color: white;
    width: 125px;
    background-color: #004080;
    margin: 20px auto;
 }

 .modal-sub #form-buttons input:hover {
     color: black;
     background-color: #f2f2f2;
 }

 .modal-sub #form-buttons input:nth-child(2){
     margin-left: 20px;
 }

</style>

<script>
function openFormDP(dp){
  document.getElementsByClassName('modal-sub')[0].style.display = 'block';
  document.getElementById('title-sub').innerHTML = 'Deseja matricular na disciplina '+dp+' ?';
}

function closeModalSub(){
  //document.getElementsByClassName('modal')[0].style.display = 'none';
  document.getElementsByClassName('modal-sub')[0].style.display = 'none';
}
</script>

<div class='modal-sub'>
<div class='modal-sub-content'>
<span onclick="closeModalSub()" class="closebtn" title="Close Modal">&times;</span>
<form method='post'>
    <h5><b id='title-sub'></b></h5>
    <div id='form-buttons'>
        <input type='submit' name='acc' id='acc' value='SIM'/>
        <input type='button' onclick="closeModalSub()" name='acc' id='acc' value='NAO'/>
    </div>
    <input type='hidden' id='id_user' name='id_user' value='{{$user_id}}' />
    <input type='hidden' id='type' name='type' value='5' />
</form>
</div>
</div>