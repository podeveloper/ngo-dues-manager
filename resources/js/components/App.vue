<template>
    <div class="container mx-auto p-4 max-w-4xl">
        <h1 class="text-3xl font-bold text-center mb-8 text-indigo-600">
            NGO Member Portal
        </h1>

        <div v-if="!token">
            <Login @login-success="handleLogin" />
        </div>

        <div v-else>
            <div class="flex justify-between items-center mb-6">
                <span class="text-gray-600">Welcome!</span>
                <button
                    @click="logout"
                    class="text-red-500 hover:text-red-700 text-sm"
                >
                    Logout
                </button>
            </div>
            <Dashboard :token="token" />
        </div>
    </div>
</template>

<script>
import Login from "./Login.vue";
import Dashboard from "./Dashboard.vue";

export default {
    components: { Login, Dashboard },
    data() {
        return {
            token: localStorage.getItem("api_token") || null,
        };
    },
    methods: {
        handleLogin(token) {
            this.token = token;
            localStorage.setItem("api_token", token);
        },
        logout() {
            this.token = null;
            localStorage.removeItem("api_token");
        },
    },
};
</script>
