<style>

/* The Modal Style (background) */
.modal-feed {
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
.modal-feed-content {
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
.closebtn-feed {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.closebtn-feed:hover, .closebtn-feed:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

 .modal-feed-content input[type=submit]:first-child{
    border: 2px solid black;
    border-radius: 20px;
    background-color: #004080;
 }

 #feed {
    width: 200px; 
    height: 60px; 
    object-fit: cover; 
    object-position: 0 0%; 
 }

 .modal-feed-content input[type=submit]{
    border: 2px solid black;
    border-radius: 20px;
    background-color: #004080;
 }

</style>

<script>
window.onload = function(){
    console.log(document.getElementById('feed'));
    document.getElementById('feed').addEventListener('mousemove', e=>{
        let rect = e.target.getBoundingClientRect();
        let x = e.clientX - rect.left; //x position within the element.
        let y = e.clientY - rect.top;  //y position within the element.
        //document.getElementById('position-face').innerHTML = 'x:'+x;
        if(x <= 56){
            e.target.style.objectPosition = '0 33.5%';
        }else if(x > 74 && x <= 130){
            e.target.style.objectPosition = '0 67%';
        }else if(x > 143 && x <= 200){
            e.target.style.objectPosition = '0 100%';
        }
    });
    document.getElementById('feed').addEventListener('mouseleave', e=>{
        e.target.style.objectPosition = '0 0%';
    })
}


function closeFeed(){
    document.getElementsByClassName('modal-feed')[0].style.display = 'none';
}

function displayFormFeed(){
    document.getElementsByClassName('modal-feed')[0].style.display = 'block';
}

</script>

<a onclick="displayFormFeed()">Dar feedback</a>

<div class='modal-feed'>
<div class='modal-feed-content'>
<span onclick="closeFeed()" class="closebtn-feed" title="Close Modal">&times;</span>
<form method='post'>
    <h5><b>O que achou das recomendações?</b></h5>
    <p>Escolha seu nível de satisfação</p>
    <div style='text-align:center;'>
        <input type='image' id='feed' name='feed' src='{{$feedb}}'>
    </div>
    <input type='hidden' id='id_user' name='id_user' value='{{$user_id}}' />
    <input type='hidden' id='type' name='type' value='4' />
</form>
</div>
</div>