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

    import { ref, onMounted, onBeforeUnmount } from "vue";
    import { storeToRefs } from 'pinia';
    import Message from './Message.vue';
    import Input from './Input.vue';
    import { useConversationStore } from '../../stores/conversations.js'  
    import { useUserStore } from '../../stores/user.js';
    import { useRoute } from 'vue-router';

    const route = useRoute();
    const messagesBody = ref(null);

    const { MESSAGES } = useConversationStore(); // get all the store
    const { getMessages } = useConversationStore();
    const { conversations, CONVERSATIONS, hubURL } = storeToRefs(useConversationStore());
    const { username } = storeToRefs(useUserStore());

    let eventSource = null;

    const scrollDown = () => {
        messagesBody.value.scrollTop = messagesBody.value.scrollHeight;
    } 
    
        /**
     * Adds a message to the conversation and scrolls down.
     *
     * @param {Object} payload - The payload containing the conversation and message data.
     * @return {void} This function does not return a value.
     */
    const addMessage = (payload) => {

        const { message, recipient, conversation } = payload;        
        const targetConversation = conversations.value.find(conv => conv.conversationId == conversation.id);
        
        if (recipient == username.value) {
            // Add the message to the conversation
            targetConversation.messages.push(message);
            // Scroll down to the latest message
            scrollDown();
        }
        
    }

    onMounted(() => {
        // Get the messages of conversation and then scrolldown to the last message.
        getMessages(route.params.id)
        .then(() => {
            scrollDown();

            if (eventSource === null) {
                const url = new URL(hubURL.value);
                //url.searchParams.append('topic', '/conversations/admin');
                url.searchParams.append('topic', `/conversations/${route.params.id}`);     

                eventSource = new EventSource(url, {
                withCredentials: false,
                });
            }            

            eventSource.onmessage = (event) => {
              addMessage(JSON.parse(event.data));
            }
        });
    })

    onBeforeUnmount(() => {
        if (eventSource instanceof EventSource) {
            eventSource.close();
        }
    });

  

</script>