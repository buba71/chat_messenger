<template>
      
    <!-- User box -->
    <div class="col-5 px-0">
      <div class="bg-white">
    
        <div class="bg-gray px-4 py-2 bg-light">
          <p class="h5 mb-0 py-1">Recent</p>
        </div>
    
        <div class="messages-box">
          <div class="list-group rounded-0">

            <template v-for="conversation in conversations" :key="conversation.conversationId" >
              <Conversation :conversation="conversation" />
            </template>

          </div>
        </div>
      </div>
    </div>
    
    <!-- User box -->
        
    
</template>

<script setup>

  import { ref, onMounted } from 'vue';
  import { storeToRefs } from 'pinia';
  import Conversation from './Conversation.vue';
  import { useConversationStore } from '../../stores/conversations.js';
  import { useUserStore } from '../../stores/user.js';


  const { conversations, hubURL } = storeToRefs(useConversationStore());
  const { getConversations } = useConversationStore();
  const { username } = storeToRefs(useUserStore());    
  
  const updateConversation = (data) => {
    console.log(data);
  }

  onMounted(() => {

    getConversations()
    .then(() => {
      
      const url = new URL(hubURL.value);
      //url.searchParams.append('topic', '/conversations/admin');
      url.searchParams.append('topic', `/conversations/${username.value}`);     

      const eventSource = new EventSource(url, {
        withCredentials: false,
      });
      
      eventSource.onmessage = (event) => {
        updateConversation(event.data);
      }
    })
  })

</script>