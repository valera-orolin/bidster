<script setup>
import ButtonGradient from '@/Components/ButtonGradient.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const isOpen = ref(false);
const toggleMenu = () => {
  isOpen.value = !isOpen.value;
};
</script>

<template>
    <div class="bg-my-black flex items-center justify-between px-4 md:px-32 py-4 lg:py-6">

        <Link :href="route('admin.welcome')">
            <img src="/images/logo.png" alt="Bidster" class="h-8"/>
        </Link>

        <div class="lg:flex hidden space-x-8 text-my-gray4 text-base items-center font-ligh justify-end">

            <Link :href="route('admin.auctions.index')">
                <div class="hover:text-my-lila transition duration-500">Auctions</div>
            </Link>

            <Link :href="route('admin.users.index')">
                <div class="hover:text-my-lila transition duration-500">Users</div>
            </Link>

            <Link :href="route('categories.index')">
                <div class="hover:text-my-lila transition duration-500">Categories</div>
            </Link>

            <Link :href="route('admin.requests.index')">
                <div class="hover:text-my-lila transition duration-500">Requests</div>
            </Link>

            <Link :href="route('admin.analytics.index')">
                <div class="hover:text-my-lila transition duration-500">Analytics</div>
            </Link>

            <Link :href="route('logout')" method="post" as="button">
                <ButtonGradient text="Sign out" />
            </Link>
        </div>

        <div class="lg:hidden inline-flex items-center justify-center space-x-4">
                
            <Link :href="route('logout')" method="post" as="button">
                <font-awesome-icon class="text-my-gray3" :icon="['fas', 'right-from-bracket']" />
            </Link>

            <button @click="toggleMenu" data-collapse-toggle="navbar-hamburger" type="button" class="items-center justify-center p-2 w-10 h-10 text-sm text-my-gray3 rounded-lg focus:outline-none focus:ring-2 focus:ring-my-gray2" aria-controls="navbar-hamburger" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="lg:hidden flex">
        <div id="menu" v-show="isOpen" class="absolute p-8 bg-my-gray2 border border-transparent text-my-gray3 rounded-2xl left-2 right-2 my-gradient-bord">
            <div class="space-y-6">
                <div>
                    <Link :href="route('admin.auctions.index')">
                        <div class="hover:text-my-lila transition duration-500 flex">Auctions</div>
                    </Link>
                </div>

                <div>
                    <Link :href="route('admin.users.index')">
                        <div class="hover:text-my-lila transition duration-500 flex">Users</div>
                    </Link>
                </div>

                <div>
                    <Link :href="route('categories.index')">
                        <div class="hover:text-my-lila transition duration-500 flex">Categories</div>
                    </Link>
                </div>

                <div>
                    <Link :href="route('admin.requests.index')">
                        <div class="hover:text-my-lila transition duration-500 flex">Requests</div>
                    </Link>
                </div>

                <div>
                    <Link :href="route('admin.analytics.index')">
                        <div class="hover:text-my-lila transition duration-500 flex">Analytics</div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>