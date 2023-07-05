import {defineStore} from 'pinia';
import { Vue } from 'vue';

export const useConversationStore = defineStore({
    id: 'conversations',
    state: () =>( { 
        conversations: [],
        hubURL: null       
    }),
    getters: {
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
                const newMessage = await response.json();
                const conversation = this.conversations.find(conversation => conversation.conversationId == conversationId);
                conversation.messages.push(newMessage);
                // Update last message in the conversation. Refactor to a function
                conversation.content = newMessage.content;
                conversation.createdAt = newMessage.createdAt;

                
            }  catch (error) {
                console.log(error.response.data);
            }
        },

    }
})