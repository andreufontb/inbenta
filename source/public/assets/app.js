
/**
 * We create a fresh Vue application instance and attach it to
 * the page.
 */

new Vue({
    delimiters: ['${', '}'],
    el: '#chatBoot',
    data: {
        message: null,
        history: [
            {
                type: "received",
                loading: true
            }
        ]
    },

    /**
     * When mounted/loaded we ask to API for the chat history.
     */
    mounted(){
        axios.get('/chat/history')
            .then(response => this.history = response.data)
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
                }).then(response => this.history.push(response.data)).catch(function (error){
                    console.log(error);
                });
                
                //3. Message "send", put to local history chat
                this.history.push(sentMessage);

                //3. Clear the message input text
                this.message = null;
            }
        }
    }
  });
