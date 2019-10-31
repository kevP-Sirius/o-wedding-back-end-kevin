var app = {
  
  baseUrl: 'http://92.243.10.85/admin/api/mercure/',
  counter: 0,
  init: function () {

  
    // app.clearFlashMessage();
    $('body').on('submit' , '#ui', app.loadMessage);
    $('body').on('click' , '#clear-button', app.clearMessage);  
    $('body').on('input','#field-message', app.information);
    $('body').on('blur','#field-name' , app.showConnect);
 
  },

 
  
  showConnect: function (){
    
    
    id = $('#user-id').val();
    
    inputName = $('#field-name').val();
    if($('#field-name').val().length>0)
    {
      console.log( 'message sended to the hub' );
      console.log(inputName);
      var jqXHR = $.ajax({
        url: app.baseUrl+'expose/joiner' ,
        method: 'POST',
        dataType: 'json',
        data:{
          
          "name": inputName,
          "id": id
        }
  
      })
  
      jqXHR.done(
        
  
        
        
      );
    }
   

  },

  loadMessage:function(event)
  {
    event.preventDefault();
    app.counter=0
    id = $('#user-id').val();
    console.log(app.counter)
    inputName = $('#field-name').val();
    inputMessage = $('#field-message').val();
    if($('#field-name').val().length>0 && $('#field-message').val().length>0 )
    {
      console.log( 'message sended to the hub' );
      console.log(inputName);
      console.log( inputMessage);
      var jqXHR = $.ajax({
        url: app.baseUrl+'sendtext' ,
        method: 'POST',
        dataType: 'json',
        data:{
          
          "name": inputName,
          "message": inputMessage,
          "id" : id
  
        }
  
      })
  
      jqXHR.done(
       
        $('#field-message').val('')
        
      );
    }else{
      window.alert('message or name is empty');
    }
    
   

    

    

  },

  

  information:function(){
    
    console.log(app.counter)
    console.log(app.counter)
    if($('#field-name').val().length===""|$('#field-name').val().length==""|$('#field-name').val().length==0)
    {

      $('#field-message').val('');
      window.alert('empty name please type one with at least one letter');

    }

    if($('#field-message').val().length>0 && app.counter==0)
    {console.log(app.counter);
     
      app.counter++
      id = $('#user-id').val(); 
       inputName = $('#field-name').val();
       inputMessage = $('#field-message').val();
       
       
       var jqXHR = $.ajax({
         url: app.baseUrl+'before_sendtext/add' ,
         method: 'POST',
         dataType: 'json',
         data:{
           
           "name": inputName,
           "message": inputMessage,
           "id" : id
   
         }
   
       })
   
       jqXHR.done(
        
         
         
       );
     
   
    }

     
    if($('#field-message').val().length==0 && app.counter>0 )
    {
      console.log('hello information counter=0')
      console.log(app.counter);
      app.counter=0
  
      id = $('#user-id').val(); 
      inputName = $('#field-name').val();
      inputMessage = $('#field-message').val();
        
        
      var jqXHR = $.ajax({

        url: app.baseUrl+'before_sendtext/remove' ,
        method: 'POST',
        dataType: 'json',
        data:{
            
          "name": inputName,
          "message": inputMessage,
          "id" : id 
    
        }
    
      })
    
        jqXHR.done(
         
         
        );
    }

    

    


   
    





  },

 

  clearMessage:function () {

    $chat = $('#conversation');
    $chat.text('');
  },

 


  






}



$(app.init);



