<script setup>
import ButtonArrow from '@/Components/ButtonArrow.vue';
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import ManageForm from './Partials/ManageForm.vue';
import dayjs from 'dayjs';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    user: {
    type: Object,
    required: true
  }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex justify-center">
            <div class="space-y-12 my-animation-in-up animation-lg">
                <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260">
                    <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                        Manage
                        <span class="my-gradient-text">{{ user.name }}</span>
                    </div>

                    <div class="w-36 h-36 md:w-60 md:h-60 lg:w-80 lg:h-80 overflow-hidden rounded-2xl">
                        <img v-if="user.avatar" :src="user.avatar" alt="Avatar" class="object-cover min-w-full min-h-full">
                        <img v-else src="/images/icon.svg" alt="Avatar" class="min-w-full min-h-full">
                    </div>

                    <div class="text-base mt-10 pr-10">
                        <Link :href="route('profile.show', user.id)">
                            <ButtonArrow text="See profile" :colorsInversed="true" />
                        </Link>
                    </div>

                    <ManageForm :user="user" />
                </div>

                <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 lg:my-12 w-full lg:w-260">
                    <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-10">
                        Statistics
                    </div>

                    <div class="space-y-6">
                        <p class="font-light text-my-gray3 mt-3">
                            Status: 
                            <span :class="{
                            'text-green-400': user.status === 'Active', 
                            'text-red-400': user.status === 'Banned'
                            }">{{ user.status }}</span>
                        </p>
                        <p class="font-light text-my-gray3">Registration date: {{ dayjs(user.created_at).format('MMMM D, YYYY h:mm A') }}</p>
                        <p class="font-light text-my-gray3">Email: {{ user.email }}</p>
                        <p class="font-light text-my-gray3 mt-3">Auctions count: {{ user.auctions_count }}</p>
                        <p class="font-light text-my-gray3 mt-3">Bids count: {{ user.bids_count }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>