import {defineStore} from 'pinia';

export const useMessageStore = defineStore({
    id: 'messages',
    state: () =>( {
        messages: []
    }),
    getters: {},
    actions: {
        async getMessages(conversationId) {
            
            try {
                const response = await fetch(`/messages/${conversationId}`);
                this.messages = await response.json();
            }  catch (error) {
                console.log(error.response.data);
            }
        }
    }
})