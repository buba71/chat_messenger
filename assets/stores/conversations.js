import {defineStore} from 'pinia';

export const useConversationStore = defineStore({
    id: 'conversations',
    state: () =>( { 
        conversations: []
    }),
    getters: {},
    actions: {
        async getConversations() {
            
            try {
                const response = await fetch('/conversations');
                this.conversations = await response.json();
            }  catch (error) {
                console.log(error.response.data);
            }          
        }
    }
})