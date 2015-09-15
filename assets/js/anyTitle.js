var anyPopisekClass = "anyTitle";
var anyPopisekIdPlovouciDiv = "plovouciHlaska";

$(document).ready(function()
{
        
    $("."+anyPopisekClass).live("mouseover",function () 
    { 
        var titulek = $(this).attr('popis');
        var animace = $(this).attr('animace');
        
        $("#"+anyPopisekIdPlovouciDiv).html(titulek);
        if(animace == true) $("#"+anyPopisekIdPlovouciDiv).show(100);
        else                $("#"+anyPopisekIdPlovouciDiv).show();
    });
    
    $("."+anyPopisekClass).live("mouseout", function () 
    { 
        var animace = $(this).attr('animace');
        if(animace == true) $("#"+anyPopisekIdPlovouciDiv).hide(100);
        else                $("#"+anyPopisekIdPlovouciDiv).hide();
    });
    
    $("."+anyPopisekClass).live("mousemove", function (e) 
    { 
        var mysX = e.pageX;
        var mysY = e.pageY;
        var obj = document.getElementById(anyPopisekIdPlovouciDiv);
        
        obj.style.top = mysY + "px";
        obj.style.left = mysX + 12 + "px";
    });
    
});