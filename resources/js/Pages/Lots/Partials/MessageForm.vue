<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import Button from '@/Components/ButtonLila.vue';
import 'emoji-picker-element';
import { useForm } from '@inertiajs/vue3';

const props = defineProps(['auction']);

const form = useForm({
    content: '',
});

const emit = defineEmits(['message-created']);

const showEmojiPicker = ref(false);

const addEmoji = (event) => {
    form.content += event.detail.unicode;
};

const toggleEmojiPicker = (event) => {
    event.stopPropagation();
    showEmojiPicker.value = !showEmojiPicker.value;
};

let handleClickOutside;
onMounted(() => {
    handleClickOutside = (event) => {
        const emojiPicker = document.querySelector('.emoji-picker');
        if (emojiPicker && !emojiPicker.contains(event.target)) {
            showEmojiPicker.value = false;
        }
    };
    document.addEventListener('click', handleClickOutside);
});
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const sendMessage = () => {
    let formData = new FormData();
    formData.append('content', form.content);

    axios.post(route('messages.store', props.auction.id), formData)
    .then((response) => {
        form.reset()
        emit('message-created', response.data)
    });
}
</script>

<template>
        <div class="w-full bg-my-black rounded-2xl my-6 space-y-2">
            <form @submit.prevent="sendMessage">
                <textarea maxlength="500" v-model="form.content" class="w-full text-my-gray3 text-sm p-5 rounded-full transition duration-500 resize-none bg-my-gray2 border-my-gray2 focus:border-my-violet focus:bg-my-black focus:outline-none focus:ring-0.5 focus:ring-my-violet" placeholder="Type a message..." ></textarea>

                <div class="flex justify-end flex-row space-x-4 mt-1">
                    <div class="flex items-center space-x-8">
                        <div class="relative inline-block z-10 emoji-picker">
                            <div class="text-my-lila text-2xl transition-all duration-500 cursor-pointer lg:hover:text-my-gray3" @click="toggleEmojiPicker">
                                <font-awesome-icon :icon="['fas', 'face-smile']" />
                            </div>
                            <emoji-picker v-show="showEmojiPicker" @emoji-click="addEmoji"
                                class="absolute top-0 left-0 transform -translate-x-1/2 -translate-y-88 md:-translate-x-full md:-translate-y-full mt-1 shadow-2xl scale-75 md:scale-100"></emoji-picker>
                        </div>
                    </div>

                    <Button type="submit" text="Send message" />
                </div>
            </form>
        </div>
</template>