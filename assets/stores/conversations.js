import {defineStore} from 'pinia';
import { Vue } from 'vue';

export const useConversationStore = defineStore({
    id: 'conversations',
    state: () =>( { 
        conversations: []        
    }),
    getters: {
        MESSAGES: state => {
            return  conversationId => state.conversations.find(conversation => conversation.conversationId == conversationId).messages
        }
    },
    actions: {
        async getConversations() {
            
            try {
                const response = await fetch('/conversations');
                this.conversations = await response.json();
            }  catch (error) {
                console.log(error.response.data);
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
                const conversation = this.conversations.find(conversation => conversation.conversationId == conversationId)
                conversation.messages.push(newMessage);
                // Update last message in the conversation. Refactor to a function
                conversation.content = newMessage.content;
                conversation.createdAt = newMessage.createdAt;

                
            }  catch (error) {
                console.log(error.response.data);
            }
        }
    }
})