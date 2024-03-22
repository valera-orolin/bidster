<script setup>
import ButtonWhite from '@/Components/ButtonWhite.vue';

const props = defineProps(['items']);

const changePage = (url) => {
    window.history.replaceState({}, '', url);
    location.reload();
}
</script>

<template>
    <div v-if="items.last_page > 1" class="flex items-center justify-center space-x-4">
        <ButtonWhite :disabled="!items.prev_page_url" @click="changePage(items.first_page_url)"><font-awesome-icon :icon="['fas', 'arrow-up']" /></ButtonWhite>

        <ButtonWhite :disabled="!items.prev_page_url" @click="changePage(items.prev_page_url)"><font-awesome-icon :icon="['fas', 'arrow-left']" /></ButtonWhite>

        <ButtonWhite :disabled="!items.next_page_url" @click="changePage(items.next_page_url)"><font-awesome-icon :icon="['fas', 'arrow-right']" /></ButtonWhite>
    </div>
</template>