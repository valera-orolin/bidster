<script setup>
import { onMounted, ref } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
    colorsInversed: {
        type: Boolean,
        default: false
    },
    defaultValue: {
        type: String,
        default: ''
    }
})

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        :class="[
            'text-base p-5 rounded-full transition duration-500',
            props.colorsInversed ? 
            'bg-my-black border-my-black focus:border-my-violet focus:outline-none focus:ring-0.5 focus:ring-my-violet' : 
            'bg-my-gray2 border-my-gray2 focus:border-my-violet focus:bg-my-black focus:outline-none focus:ring-0.5 focus:ring-my-violet'
        ]"
        v-model="model"
        ref="input"
    />
</template>
