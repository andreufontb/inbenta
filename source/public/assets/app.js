
/**
 * We create a fresh Vue application instance and attach it to
 * the page.
 */

var app = new Vue({
    delimiters: ['${', '}'],
    el: '#chatBoot',
    data: {
        message: null,
        history: []
    },

    /**
     * When mounted/loaded we ask to API for the chat history.
     */
    mounted(){
        axios.get('/chat/history')
            .then( function(response){
                app.history = response.data;
            })
            .catch(function (error) {
                console.log(error);
          });
    },

    methods: {

        /**
         *   On click "send" or press enter key, we send the message to the bot via API
         */
        sentResponse: function(){
            
            if ( this.message != null &&  this.message.length > 0 ){ //Check if there are a message

                //1. Prepare the message to log in local "history chat"
                let sentMessage = {
                    type: "sent",
                    content: this.message
                }
                
                //2. Send the message
                axios.post('/chat', {
                    message: sentMessage.content
                }).then(function (response){
                    for (var i=0; i<response.data.length; i++){
                        app.history.push(response.data[i]);
                    }
                }).catch(function (error){
                    console.log(error);
                });
                
                //3. Message "send", put to local history chat
                if (Object.keys(app.history).length === 0){
                    app.history = [sentMessage];
                } else {
                    app.history.push(sentMessage);
                }

                //3. Clear the message input text
                this.message = null;
            }
        }
    }
  });
