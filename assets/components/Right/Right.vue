<template>
    <div class="col-7 px-0">
        <div class="px-4 py-5 chat-box bg-white" ref="messagesBody">
                        
            <template v-for="message in MESSAGES(route.params.id)" :key="message.id">
            
                <Message :message="message" /> 
                
            </template>
                   
        </div>
      
        <Input />          
      
    </div>
</template>

<script setup>

    import { ref, onMounted, watch, nextTick } from "vue";
    import Message from './Message.vue';
    import Input from './Input.vue';
    import { useConversationStore } from '../../stores/conversations.js'
    import { useRoute } from 'vue-router';

    const route = useRoute();
    const messagesBody = ref(null);

    const { MESSAGES } = useConversationStore(); // get all the store
    const { getMessages } = useConversationStore();

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