$(document).ready(function(){
    $(".inputPsc").keyup(function(e){
        var str = $(this).val();
        if( !(/^-?[0-9]+$/).test(str) ){
            $(this).val(str.substr(0, str.length-1));
        }
        str = $(this).val();
        if(str.length > 4){
            $(this).val(str.substr(0, 5));
        }
    });
});
