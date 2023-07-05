import { defineStore } from 'pinia';

export const useUserStore = defineStore({
    id: 'users',
    state: () =>( { 
        username: null 
    }),
    getters: {
        USERNAME: state => {
            return state.username
        }
    },
    actions: {
        
    }
})