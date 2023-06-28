<template>
    <div class="col-7 px-0">
        <div class="px-4 py-5 chat-box bg-white" ref="messagesBody">
            <template v-for="message in messages" :key="message.id">
            
                <Message :message="message" /> 
                
            </template>
                   
        </div>
      
        <Input />          
      
    </div>
</template>

<script setup>

import Message from './Message.vue';
import { useMessageStore } from '../../stores/messages.js'
import Input from './Input.vue';
import {ref, onMounted, computed} from "vue";
import { storeToRefs } from 'pinia';
import { useRoute } from 'vue-router';

const route = useRoute();
const messagesBody = ref(null);

const { messages } = storeToRefs(useMessageStore());
const { getMessages } = useMessageStore();

const scrollDown = () => {
    messagesBody.value.scrollTop = messagesBody.value.scrollHeight;
}

onMounted(() => {
    
    // Get the messages of conversation and then scrolldown to the last message.
    getMessages(route.params.id)
    .then(() => {
        scrollDown();
    });
    

})

</script>