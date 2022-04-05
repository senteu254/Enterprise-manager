function expandNode(id)
{
    if ($("#" + id + " + ul").css('display') == 'none') 
    {
      $("#" + id).css("background-position", "-72px 0px");
    }
    else
    {
        $("#" + id).css("background-position", "-54px 0px");
    }
   // $("#" + id + " + ul").slideToggle("slow",
    $("#" + id + " + ul").slideToggle("slow");
    //function(){$("#" + id + " + ul").css("background-image", "rtl.gif"); $("#" + id + " + ul").css("background-position", "-90px 0px"); }
    $("#" + id + " + ul li").css("background-image", "rtl.gif"); 
    $("#" + id + " + ul li").css("background-position", "-90px 0px"); 
    
}