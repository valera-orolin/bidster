<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/ButtonWhite.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    avatar: user.avatar ? user.avatar : "",
    description: user.description ? user.description : "",
    //user_id: user.user_id,
    email: user.email,
});

const previewImage = ref(user.avatar);

const handleFileUpload = (event) => {
    form.avatar = event.target.files[0];
    previewImage.value = URL.createObjectURL(form.avatar);
};

const triggerFileInput = () => {
    const fileInput = document.getElementById('post-media');
    fileInput.click();
};

let submitForm = () => {
    let formData = new FormData();
    formData.append('name', form.name);
    if (form.avatar) {
        formData.append('avatar', form.avatar);
    }
    if (form.description) {
        formData.append('description', form.description);
    }
    formData.append('email', form.email);
    formData.append('_method', 'PATCH');

    axios.post(route('profile.update'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        },
    }).then(() => {
        form.recentlySuccessful = true;
        setTimeout(() => form.recentlySuccessful = false, 2000);
    }).catch(error => {
        console.error(error);
    });
};
</script>

<template>
    <form @submit.prevent="submitForm" class="mt-6 space-y-6">
        <div class="flex flex-col items-center">
            <div class="w-36 h-36 md:w-60 md:h-60 lg:w-80 lg:h-80 overflow-hidden rounded-2xl">
                <img v-if="previewImage" :src="previewImage" alt="Avatar" class="object-cover min-w-full min-h-full">
                <img v-else src="/images/icon.svg" alt="Avatar" class="min-w-full min-h-full">
            </div>

            <div>
                <input type="file" id="post-media" class="hidden" accept="image/*" @change="handleFileUpload">
                <font-awesome-icon :icon="['fas', 'image']" class="text-my-gray3 text-2xl mt-2 cursor-pointer transition-all duration-500 lg:hover:text-my-lila" @click="triggerFileInput"/>
            </div>
        </div>

        <div>
            <InputLabel for="name" value="Name" />

            <TextInput
                id="name"
                type="text"
                class="mt-3 block w-full"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
                :colorsInversed="true"
            />

            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="description" value="Description"   />

            <TextArea
                id="description"
                type="text"
                class="mt-3 block w-full"
                maxlength="3000"
                rows="5"
                v-model="form.description"
                :colorsInversed="true"
            />

            <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div>
            <InputLabel for="email" value="Email" />

            <TextInput
                id="email"
                type="email"
                class="mt-3 block w-full"
                v-model="form.email"
                required
                autocomplete="username"
                :colorsInversed="true"
            />

            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div v-if="mustVerifyEmail && user.email_verified_at === null">
            <p class="text-sm mt-2 text-gray-800">
                Your email address is unverified.
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Click here to re-send the verification email.
                </Link>
            </p>

            <div
                v-show="status === 'verification-link-sent'"
                class="mt-2 font-medium text-sm text-green-600"
            >
                A new verification link has been sent to your email address.
            </div>
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing" text="Save"/><!--ToDo-->

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-if="form.recentlySuccessful" class="text-sm text-my-gray3">Saved.</p> <!--ToDo-->
            </Transition>
        </div>
    </form>
</template>

<!---
<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
-->