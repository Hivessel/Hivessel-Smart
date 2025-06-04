<template>
    <div class="container">
        <form @submit.prevent="login">
            <div class="form-group">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="text"
                    class="form-control"
                    v-model="credentials.email"
                    autocomplete="username"
                />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    class="form-control"
                    v-model="credentials.password"
                    autocomplete="current-password"
                />
            </div>
            <button class="btn btn-secondary mt-2" type="submit" :disabled="credentials.processing">
                Login
            </button>
        </form>
    </div>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3'

const credentials = useForm({
    email: '',
    password: ''
})

const login = () => {
    credentials.post(route('login'), {
        onSuccess: () => {
            router.get(route('client.home'));
        },
        onError: () => {
            credentials.reset('password')
            toastr.error('Credentials did not match to our records!');
        }
    });
}
</script>