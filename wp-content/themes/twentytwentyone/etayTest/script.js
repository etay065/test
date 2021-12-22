$(document).ready(function(){
    $("button").click(function(){
        $.post("endpoint.php", function(data, status){
            console.log(data);
        });
    });
});