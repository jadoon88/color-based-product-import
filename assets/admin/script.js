jQuery(document).ready(function(){

    jQuery("#ishark_file_submit").on('click',function(){
      
       file_data = jQuery("#ishark_file_upload").prop('files')[0];
       delimiter = jQuery("#ishark_delimiter").val();
       form_data = new FormData();
       form_data.append('delimiter', delimiter);
       form_data.append('file', file_data);
       form_data.append('action', 'file_upload');
       form_data.append('security', cbpi_file_handle.security);
            jQuery.ajax({
                url: cbpi_file_handle.ajaxurl,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) {
                    //$this.val('');
                    window.location.reload();
                }
                });



      });
  
  });
  