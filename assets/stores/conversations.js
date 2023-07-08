import {defineStore} from 'pinia';

export const useConversationStore = defineStore({
    id: 'conversations',
    state: () =>( { 
        conversations: [],
        hubURL: null       
    }),
    getters: {
        CONVERSATIONS: state => {
            return state.conversations.sort((a, b) => a.createdAt - b.createdAt)
        },
        MESSAGES: state => {
            return  conversationId => state.conversations.find(conversation => conversation.conversationId == conversationId).messages
        }, 
        HUBURL: state => {
            return state.hubURL
        }
    },
    actions: {
        async getConversations() {
            
            try {
                const response = await fetch('/conversations');
                this.hubURL = response.headers.get('Link').match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];
                this.conversations = await response.json();
            }  catch (error) {
                console.log(error);
            }          
        },
        async getMessages(conversationId) {
            
            try {
                const response = await fetch(`/messages/${conversationId}`, {
                    method: 'GET'
                });
                
                this.conversations.find(conversation => conversation.conversationId == conversationId).messages = await response.json();
                
            }  catch (error) {
                console.log(error.response.data);
            }
        },
        async postMessage(message, conversationId) {
            const messageData = {
                message: message
            };
            
            try {
                const response = await fetch(`/messages/${conversationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(messageData)
                });

                if (response.ok) {
                    const newMessage = await response.json();
                    this.updateConversationWithNewMessage(newMessage, conversationId);
                } else {
                    console.log('Failed to post message');
                }
            }  catch (error) {
                console.log(error.response.data);
            }
        },

        updateConversationWithNewMessage(message, conversationId) {

            const conversation = this.conversations.find(conversation => conversation.conversationId == conversationId);
            conversation.messages.push(message);                
            conversation.content = message.content;
            conversation.createdAt.date = message.createdAt;
        }

    }
})