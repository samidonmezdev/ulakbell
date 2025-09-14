<template>
    <div class="messaging">
        <div class="header">
            <h2>Mesajlar</h2>
            <button @click="getMessages" class="refresh-btn">Yenile</button>
        </div>

        <div class="messages-container">
            <div class="messages" ref="messagesContainer">
                <div v-for="msg in messages" :key="msg.id" class="message-item">
                    <strong>{{ (msg.user && msg.user.name) ? msg.user.name : 'ulakbell' }}:</strong>
                    {{ renderContent(msg.content) }}
                </div>
            </div>
        </div>

        <div class="input-area">
            <input
                v-model="newMessage"
                @keyup.enter="sendMessage"
                placeholder="Mesaj yaz..."
                class="message-input"
            />
            <button @click="sendMessage" :disabled="isMessageInvalid" class="send-btn">Gönder</button>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted, computed, nextTick, onUnmounted} from "vue";
import axios from "axios";

const messages = ref([]);
const newMessage = ref("");
const messagesContainer = ref(null);

const isMessageInvalid = computed(() => newMessage.value.length < 1);

function renderContent(content) {
    if (typeof content === 'string') return content;
    if (content && typeof content === 'object') {
        return content.text || content.message || JSON.stringify(content);
    }
    return '';
}

const getMessages = async () => {
    try {
        const response = await axios.get("/api/messages");
        const payload = response.data?.data;

        messages.value = Array.isArray(payload?.data)
            ? payload.data
            : Array.isArray(payload)
                ? payload
                : [];

        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error("Mesajlar alınamadı:", error);
    }
};

const sendMessage = async () => {
    if (isMessageInvalid.value) return;

    try {
        await axios.post("/api/messages", {content: newMessage.value});
        newMessage.value = "";
        // Mesaj gönderildikten sonra mesajları yenile
        await getMessages();
    } catch (error) {
        console.error("Mesaj gönderilemedi:", error);
    }
};

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const addMessage = (message) => {
    console.log('Yeni mesaj ekleniyor:', message);

    // Mesajın zaten mevcut olup olmadığını kontrol et
    const exists = messages.value.find(m => m.id === message.id);
    if (!exists) {
        messages.value.push(message);
        nextTick(() => scrollToBottom());
    }
};

onMounted(() => {
    getMessages();

    if (window.Echo) {
        console.log('Echo başlatılıyor...');

        window.Echo.channel('messages')
            .listen('.message.sent', (e) => {
                console.log('message.sent eventi geldi:', e);

                try {
                    let messageData = null;

                    // Eğer e.data string ise parse et
                    if (typeof e.data === 'string') {
                        const parsed = JSON.parse(e.data);
                        messageData = parsed.message;
                    }
                    // Eğer e.data object ise direkt kullan
                    else if (e.data && e.data.message) {
                        messageData = e.data.message;
                    }
                    // Eğer e direkt mesaj objesi ise
                    else if (e.message) {
                        messageData = e.message;
                    }
                    // Son çare olarak e'nin kendisi
                    else {
                        messageData = e;
                    }

                    console.log('İşlenmiş mesaj verisi:', messageData);

                    if (messageData && messageData.id) {
                        addMessage(messageData);
                    } else {
                        console.warn('Geçersiz mesaj verisi:', messageData);
                    }

                } catch (err) {
                    console.error('Event parse hatası:', err, 'Raw data:', e);
                }
            })
            // Alternatif olarak tüm eventleri dinle
            .listenAny((eventName, e) => {
                console.log('Herhangi bir event geldi:', eventName, e);

                if (eventName === 'message.sent') {
                    try {
                        let messageData = null;

                        // String data'yı parse et
                        if (typeof e.data === 'string') {
                            const parsed = JSON.parse(e.data);
                            messageData = parsed.message;
                        } else if (e.message) {
                            messageData = e.message;
                        } else if (e.data && e.data.message) {
                            messageData = e.data.message;
                        }

                        console.log('listenAny ile işlenmiş mesaj:', messageData);

                        if (messageData && messageData.id) {
                            addMessage(messageData);
                        }
                    } catch (err) {
                        console.error('listenAny parse hatası:', err);
                    }
                }
            });

    } else {
        console.error('Echo mevcut değil!');
    }
});

onUnmounted(() => {
    if (window.Echo) {
        console.log('Echo kanalından ayrılıyor...');
        window.Echo.leaveChannel('messages');
    }
});
</script>

<style scoped>
.messaging {
    display: flex;
    flex-direction: column;
    height: 100vh;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.header h2 {
    margin: 0;
    color: #333;
}

.refresh-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.refresh-btn:hover {
    background: #0056b3;
}

.messages-container {
    flex: 1;
    min-height: 0;
    margin-bottom: 20px;
}

.messages {
    height: 100%;
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    background: #f9f9f9;
    display: flex;
    flex-direction: column;
}

.message-item {
    margin-bottom: 10px;
    padding: 8px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.message-item:last-child {
    margin-bottom: 0;
}

.input-area {
    display: flex;
    gap: 10px;
    align-items: center;
}

.message-input {
    flex: 1;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
}

.message-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.send-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.send-btn:hover:not(:disabled) {
    background: #218838;
}

.send-btn:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.messages::-webkit-scrollbar {
    width: 6px;
}

.messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
