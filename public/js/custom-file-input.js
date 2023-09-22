$(document).ready(function(){
      $(document).on("change",".inputfile",function(e){
        var fileName = '';
        var label	 = $(this).next();        
			labelVal = label.html();
            if( this.files && this.files.length > 1 )
                fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
            else
                fileName = e.target.value.split( '\\' ).pop();
            if( fileName )
                label.find("span").html(fileName);                
            else
                label.html(labelVal);
    })
  });