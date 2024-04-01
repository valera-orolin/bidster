<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import ButtonLila from '@/Components/ButtonLila.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: 'user1@example.com',
    password: 'password',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <div class="flex justify-center">
            <div class="border-2 border-transparent rounded-2xl my-gradient-bord-black p-4 lg:p-12 text-my-gray4 m-4 lg:my-12 my-animation-in-up animation-md">
                <Link href="/">
                    <img src="/images/icon.svg" alt="Icon" class="w-24 h-24 md:w-32 md:h-32 bg-my-gray2 rounded-2xl p-4" />
                </Link>
                <h1 class="text-4xl md:text-5xl font-bold pt-4 md:pt-8">Log In</h1>
                <h2 class="text-sm md:text-xl pt-4 tracking-widest font-light lg:pr-20">Please fill your email and password to log in</h2>
            
                <form class="pt-8 space-y-6" @submit.prevent="submit">
                    <div>
                        <InputLabel for="email" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-3 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <div class="flex justify-between">
                            <InputLabel for="password" value="Password" />
                            <Link :href="route('password.request')">
                                <h3 class="text-sm tracking-widest underline hover:text-my-lila cursor-pointer duration-500 transition">Forgot password?</h3>
                            </Link>
                        </div>

                        <TextInput
                            id="password"
                            type="password"
                            class="mt-3 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <ButtonLila class="text-sm" :text="'Log In'" />
                </form>

                <h2 class="text-sm md:text-base pt-6 tracking-widest font-light">Already have an account?
                    <Link :href="route('register')">
                        <span class="underline hover:text-my-lila duration-500 transition">Sign up</span>
                    </Link>
                </h2>
            </div>
        </div>
    </GuestLayout>
</template>
