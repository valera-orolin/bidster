<script setup>
const props = defineProps({
    text: String
});
</script>

<template>
    <button class="bg-my-gray3 px-8 py-3 rounded-2xl text-my-black hover:bg-my-lila hover:text-my-gray4 transition duration-500 disabled:opacity-50 disabled:hover:bg-my-gray3 disabled:hover:text-my-black">
        <span>{{ text }}</span>
        <slot></slot>
    </button>
</template>