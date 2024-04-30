<script setup>
import Message from './Message.vue';
import MessageForm from './MessageForm.vue';
import { ref, onMounted, onUpdated, nextTick } from 'vue';

const props = defineProps(['auction_id', 'messages']);

const scrollContainer = ref(null);
const isAtBottom = ref(true);
let messages = ref(props.messages);

onMounted(async () => {
    await nextTick();
    scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight;
    scrollContainer.value.addEventListener('scroll', handleScroll);

    var channel = window.Echo.channel(`chat.${props.auction_id}`);
    channel.listen('.message-sent', async function(data) {
        messages.value.push(data.message);
        await scrollToBottom();
    });
});

const handleScroll = () => {
    const { scrollTop, scrollHeight, clientHeight } = scrollContainer.value;
    isAtBottom.value = Math.abs(scrollTop + clientHeight - scrollHeight) < 300;
};

const scrollToBottom = async () => {
    await nextTick();
    scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight;
    isAtBottom.value = true;
};

const addNewMessage = async (message) => {
    messages.value.push(message);
    await scrollToBottom();
};
</script>

<template>
    <div class="relative border-2 border-transparent rounded-2xl my-gradient-bord-black text-my-gray4 h-160 lg:h-200">
        <div class="overflow-auto h-full flex flex-col scrollbar-hide p-4 lg:p-12" ref="scrollContainer">
            <Message v-for="message in messages"
                    :key="message.id"
                    :message="message" />
        </div>
        <button v-if="!isAtBottom" class="absolute bottom-4 right-4 bg-my-gray2 text-my-gray3 rounded-full text-lg py-2 px-3" @click="scrollToBottom">
            <font-awesome-icon :icon="['fas', 'chevron-down']" />
        </button>
    </div>

    <MessageForm  @message-created="addNewMessage" :auction_id="auction_id" />
</template>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>