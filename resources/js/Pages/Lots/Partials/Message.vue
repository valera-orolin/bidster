<script setup>
import { format, isToday } from 'date-fns'
import { usePage } from '@inertiajs/vue3';

const props = defineProps(['message', 'auction']);
const user = usePage().props.auth.user;

const formatDate = (date) => {
    if (isToday(new Date(date))) {
      return format(new Date(date), 'HH:mm')
    } else {
      return format(new Date(date), 'dd/MM/yyyy HH:mm')
    }
}
</script>

<template>
    <div v-if="message.user.id == user.id" class="flex flex-col space-y-1 justify-self-end my-8">
        <div class="px-6 py-4 max-w-3xl bg-my-velvet rounded-tl-xl rounded-tr-xl rounded-bl-xl flex items-center self-end">
            <p class="text-my-gray3">{{ message.content }}</p>
            <div class="text-my-gray3 self-end text-xs ml-3 mt-3">{{ formatDate(message.created_at) }}</div>
        </div>
    </div>

    <div v-else class="flex flex-col space-y-1 justify-start items-start my-8">
        <div class="px-6 py-4 max-w-3xl bg-my-gray2 rounded-tl-xl rounded-tr-xl rounded-br-xl flex flex-col items-end">
            <div v-if="message.user.id == auction.seller_id" class="self-start space-y-3 overflow-auto break-words w-56 md:w-fit">
                <span class="my-gradient-text font-semibold">{{ message.user.name }}</span> 
                <span class="font-light text-gray-500">   ·   Seller</span>
            </div>
            <div v-else class="text-my-orange font-semibold self-start">{{ message.user.name }}</div>

            <div class="flex justify-between w-full items-center self-start mt-1">
                <p class="text-my-gray3">{{ message.content }}</p>
                <div class="text-gray-500 self-end text-xs ml-3 mt-3">{{ formatDate(message.created_at) }}</div>
            </div>
        </div>
    </div>
</template>