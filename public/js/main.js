$(function () {

    /**
     * For future records and to avoid headaches hover is not an event, its just
     * the jQuery handler for the events mouseenter and mouseleave
     */

    // $(".image-del-container").on('mouseenter touchmove', function(){
    //     //console.log('touchmove');
    //     $(this).find('img').css({border: "2px solid black"});
    //     $('i.delete_img').show();
    // });

    $('i.delete_img').on("touchstart click",function(){ 
       $(this).parent().fadeOut('slow');
       $('input[name="imagen"]').remove();
   });

    // $('.image-del-container').bind('mouseleave',
    // function(){
    //     $('i.delete_img').hide();
    //     $(this).find('img').css({borderStyle: "none"});
    // });
});