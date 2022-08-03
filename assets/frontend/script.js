jQuery(document).ready(function(){

    jQuery(".ishark-color-box").on('click',function(){
      
        console.log("color box clicked with value"+jQuery(this).attr('data-val'));
        jQuery("#pa_color").val(jQuery(this).attr('data-val')).change();
        jQuery(".ishark-selected-color strong").text(jQuery(this).attr('data-name'));
       
      });

      jQuery(".reset_variations").on('click',function(){
        jQuery(".ishark-selected-color strong").text("None");
       
      });


      
  
  });
  