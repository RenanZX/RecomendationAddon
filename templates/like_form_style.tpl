<style>

.like-form {
    display: none;
    background-color: #FFF;
    padding: 10px;
    width: 570px;
    margin-top: 30px;
}

.like-form #profile {
    padding: 5px;
}

.like-form div:first-child {
    display: block;
    margin-bottom: 35px;
}

.like-form #coment-section {
    display: inline;
}

.like-form #display-coment-button-like {
    float: right;
}

.like-form #like-coment-text {
    rows: 1;
    width: 480px;
    height: 30px;
    resize: none;
    overflow: hidden;
    border-color:transparent;
    border-bottom: 1px solid black;
}

.like-form #display-coment-button-like input {
    background-color: #004080; 
    color: white;
    text-decoration: none;
    border: none;
    padding: 4px;
}

</style>
<script>
function eraseInput(field){
    document.getElementById('display-coment-button-like').style.display = 'block';
}

function Cancelar(){
    var elements = document.getElementsByClassName('like-form');
    Array.from(elements).forEach(element => element.style.display = 'none');
}

</script>