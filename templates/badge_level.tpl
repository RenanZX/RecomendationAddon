<style>
    .badge-level {
        text-align: center;
    }

    .badge-level img {
        width: 130px;
        height: 70px;
    }
</style>
<script>
window.onload = function() {
    var div = document.createElement('div');
    div.className = 'badge-level';
    div.innerHTML = `
        <h4>Reputação</h4>
        </br>
        <img src='{{$level}}' />
    `;
    document.getElementsByClassName('panel-body')[0].appendChild(div);
}
</script>