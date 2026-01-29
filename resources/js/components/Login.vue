<template>
    <div
        class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md border border-gray-200"
    >
        <h2 class="text-xl font-semibold mb-4">Login</h2>
        <form @submit.prevent="login">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2"
                    >Email</label
                >
                <input
                    v-model="email"
                    type="email"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                    placeholder="admin@example.com"
                    required
                />
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2"
                    >Password</label
                >
                <input
                    v-model="password"
                    type="password"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                    placeholder="password"
                    required
                />
            </div>
            <button
                type="submit"
                class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-300"
            >
                Login
            </button>
            <p v-if="error" class="text-red-500 text-xs italic mt-2">
                {{ error }}
            </p>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return { email: "", password: "", error: null };
    },
    methods: {
        async login() {
            try {
                const response = await axios.post("/api/login", {
                    email: this.email,
                    password: this.password,
                });
                this.$emit("login-success", response.data.token);
            } catch (err) {
                this.error = "Login failed. Please check your credentials.";
            }
        },
    },
};
</script>
