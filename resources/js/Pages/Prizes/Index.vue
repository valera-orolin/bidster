<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Prize from './Partials/Prize.vue'
import Pagination from '@/Components/Pagination.vue';

const props = defineProps(['prizes']);
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:gradientText>
            Prizes
        </template>
        
        <div class="flex flex-col space-y-4 md:space-y-16">
            
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Prize v-for="prize in prizes.data" :key="prize.id" :prize="prize" class="my-animation-in-up" />
            </div>

            <Pagination :items="prizes" />
        </div>
    </AuthenticatedLayout>
</template>