<template>
    <div>
        <!-- Typing area -->
        <form action="#" class="bg-light">
          <div class="input-group">
            <input
             type="text" placeholder="Type a message"
             v-model="content"
             @keypress.enter.prevent="sendMessage"
             aria-describedby="button-addon2"
             class="form-control rounded-0 border-0 py-4 bg-light"
            >
            <div class="input-group-append">
              <button id="button-addon2" type="button" class="btn btn-link"><i class="fa fa-paper-plane"></i></button>
            </div>
          </div>
        </form> 
        
    </div>
</template>

<script setup>
  import { ref } from 'vue';
  import { useConversationStore } from '../../stores/conversations.js';
  import { useRoute } from 'vue-router';

  const route = useRoute();
  const { postMessage } = useConversationStore();


  const content = ref('');

  const sendMessage = () => {
    postMessage(content.value, route.params.id);
    content.value = '';
  };
</script>