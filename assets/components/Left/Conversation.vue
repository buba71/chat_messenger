<template>
    <div>
        <!-- Conversation-->
        <router-link :to="{name: 'conversation', params: {id:conversation.conversationId }}" class="list-group-item list-group-item-action text-grey rounded-0">
          <div class="media"><img src="https://bootstrapious.com/i/snippets/sn-chat/avatar.svg" alt="user" width="50" class="rounded-circle">
            <div class="media-body ml-4">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <h6 class="mb-0">{{ conversation.username }}</h6><small class="small font-weight-bold">{{ date }}</small>
              </div>
              <p class="font-italic mb-0 text-small">{{ conversation.content }}</p>
            </div>
          </div>
        </router-link>
        
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  conversation: Object
});

const date = computed(() => new Date(props.conversation.createdAt.date).toLocaleTimeString());

</script>