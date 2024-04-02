<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    request: {
    type: Object,
    required: true
  }
});
</script>

<template>
    <Link :href="route(request.type === 'Create' ? 'admin.requests.create-auction' : 'admin.requests.edit-auction', request.id)">
        <div class="p-4 text-my-gray4 rounded-2xl border-0.5 border-my-gray2 hover:border-my-lila transition duration-500 cursor-pointer lg:hover:-translate-y-1 flex items-center justify-between md:space-x-10">
            <div class="w-full">
                <p class="text-sm font-light text-my-gray3 mb-4">
                    Type: 
                    <span :class="{
                    'text-teal-400': request.type === 'Create', 
                    'text-fuchsia-400': request.type === 'Edit', 
                    }">{{ request.type }}</span>
                </p>
                <h2 class="text-base md:text-xl font-bold">{{ request.lot.title }}</h2>
                <p class="text-sm font-light text-my-gray3 mt-4">{{ request.created_at }}</p>
            </div>
            <img v-if="request.lot.images[0]" :src="request.lot.images[0].image_path" alt="Lot image" class="w-32 h-32 md:min-w-48 md:min-h-48 object-cover ml-4 rounded-2xl">
            <img v-else src="/images/icon.svg" alt="Lot image" class="w-32 h-32 md:w-48 md:h-48 ml-4 rounded-2xl">
        </div>
    </Link>
</template>