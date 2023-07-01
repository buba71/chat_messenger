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
  import { useConversationStore } from '../../stores/conversations'


  const { conversations } = storeToRefs(useConversationStore());
  const { getConversations } = useConversationStore()

  onMounted(() => {
    getConversations()
  })

</script>