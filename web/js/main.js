
$( "a.heart" ).click(function() {

	var $this = $(this);
	 var id=  $this.attr('id');
     var urli=Routing.generate('ajouter_favoris',{'id':id});
	$.ajax({
	                 url: urli,                   
	                 beforeSend: function() {
	                       $($this).parent().append('<img id="loading" class="img-circle" src="https://github.com/devandclick/Ecommerce/blob/v0.28/web/img/loading.gif?raw=true" width="20" height="20" >');
	            	   
	                },
	                success: function( data) {
	                	if(data.success==true){
	  		    		   $('#loading').remove();
	  		    		  $this.children('#clr').css('color','#FF69B4');
	  			    	  }
	  		    	  else{

	  		    		 $('#loading').remove();
	  		    		 $('#favMod').modal('show');	  		    	  
	  			    	  }
	                                  
	                              }
 });
		});

