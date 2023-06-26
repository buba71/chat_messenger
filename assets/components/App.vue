<template>
    <div>
        <div class="container py-5 px-4">
          <div class="row rounded-lg overflow-hidden shadow">
            <!-- Users box-->
            <Left />
            <!-- Chat Box-->
            <router-view :key="$route.fullPath"></router-view>
          </div>
        </div>
    </div>
</template>
<script setup>
import Left from './Left/Left.vue';

</script>