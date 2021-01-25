$(document).ready(function(){  
     
    load_data();  
    $('#action').val("Insert");  
    function load_data()  
    {  
         var action = "Load";  
         $.ajax({  
              url:"action.php",  
              method:"POST",  
              data:{action:action},  
              success:function(data)  
              {  
                   $('#user_table').html(data);  
                   delete_data();
              
              }  
         });  
    }  


    $('#button').on('click', function(event){  
         event.preventDefault();  
         var name = $('#name').val();  
         var num= $('#number').val();  
         if(name != '' && num != '')  
         {  
              $.ajax({  
                   url:"action.php",  
                   method:"POST",  
                   data:{name:name,num:num},   
                   success:function(res)  
                   {  
                        window.location = "index.php";
                        console.log(res);  
                      
                   }  
              })  
         }  
         else  
         {  
              alert("All Fields are Required");  
         }  
    });  

    function delete_data()  
    {  

    $('.delete').click(function(){
      
        var el = this;
       
    
        var deleteid = $(this).data('id');
   
           $.ajax({
             url: 'action.php',
             type: 'POST',
             data: { id:deleteid },
             success: function(response){
              console.log("response",response);
               if(response){
               console.log("deleted");
               window.location = "index.php";
               }else{
                console.log('Invalid ID.');
               }
     
             }
           });
     
      });

    }

     $('#search').keyup(function(e) {
         let number=e.currentTarget.value;
        $.ajax({
            url: 'action.php',
            type: 'POST',
            data: { number:number },
            success: function(response){
                $('#user_table').html('');
                if(number==''){console.log("empty");   load_data();  }else{
                $('#user_table').html(response);
            }
    
            }
          });
    });
 
    function setInputFilter(textbox, inputFilter) {
        ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
          textbox.addEventListener(event, function() {
            if (inputFilter(this.value)) {
              this.oldValue = this.value;
              this.oldSelectionStart = this.selectionStart;
              this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
              this.value = this.oldValue;
              this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
              this.value = "";
            }
          });
        });
      }
    
      setInputFilter(document.getElementById("search"), function(value) {
        return /^\d*\.?\d*$/.test(value);
      }); 

});  


