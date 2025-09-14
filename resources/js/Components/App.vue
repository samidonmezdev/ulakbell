<template>
    <div class="app-container">
        <login-component v-if="!isAuthenticated" @login-success="handleLoginSuccess" />
        <messaging-component v-else />
    </div>
</template>

<script>
import LoginComponent from './LoginComponent.vue';
import MessagingComponent from './MessagingComponent.vue';

export default {
    components: { LoginComponent, MessagingComponent },
    data() {
        return {
            isAuthenticated: !!localStorage.getItem('token'),
        };
    },
    methods: {
        handleLoginSuccess(token) {
            this.isAuthenticated = true;
        },
    },
};
</script>

<style>
html, body {
    height: 100%;
    margin: 0;
}

.app-container {
    display: flex;
    justify-content: center;   /* yatay ortalama */
    align-items: center;       /* dikey ortalama */
    height: 100vh;             /* tam ekran yüksekliği */
}
</style>
