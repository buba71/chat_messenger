<template>
    <div class="col-7 px-0">
        <div class="px-4 py-5 chat-box bg-white">
            <Message />        
        </div>
      
        <Input />          
      
    </div>
</template>

<script setup>

import Message from './Message.vue';
import { useMessageStore } from '../../stores/Messages'
import Input from './Input.vue';
import {ref, onMounted} from "vue";
import { storeToRefs } from 'pinia';

const { messages } = storeToRefs(useMessageStore());
const { getMessages } = useMessageStore();

onMounted(() => {
  getMessages();
})



</script>