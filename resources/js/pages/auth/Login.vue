<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { loginpatientStore } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { ref } from 'vue';

defineProps<{
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const authMethod = ref<'dni' | 'targeta'>('dni');
</script>

<template>
    <Head title="Iniciar sessió" />

    <!-- Title -->
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Inicia sessió</h1>
        <p class="mt-2 text-sm text-gray-600">Introdueix les teves credencials per accedir al portal.</p>
    </div>

    <Form
        :action="loginpatientStore.url()"
        method="post"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <!-- Auth method tabs -->
        <div class="auth-tab-group">
            <label class="auth-tab-option" :class="{ active: authMethod === 'dni' }">
                <input
                    type="radio"
                    name="auth_method"
                    value="dni"
                    v-model="authMethod"
                    class="sr-only"
                />
                <span class="auth-tab-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3m-3 3h3m-3 3h3m-6 1c-.306-.613-.933-1-1.618-1H7.618c-.685 0-1.312.387-1.618 1M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm7 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                    </svg>
                </span>
                DNI
            </label>

            <label class="auth-tab-option" :class="{ active: authMethod === 'targeta' }">
                <input
                    type="radio"
                    name="auth_method"
                    value="targeta"
                    v-model="authMethod"
                    class="sr-only"
                />
                <span class="auth-tab-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M6 14h2m3 0h5M3 7v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Z"/>
                    </svg>
                </span>
                Targeta sanitària
            </label>
        </div>

        <!-- DNI field -->
        <div v-if="authMethod === 'dni'" class="flex flex-col gap-1.5">
            <Label for="dni">DNI</Label>
            <Input
                id="dni"
                type="text"
                name="dni"
                required
                autofocus
                :tabindex="1"
                placeholder="12345678A"
            />
            <InputError :message="errors.dni" />
        </div>

        <!-- Targeta field -->
        <div v-else class="flex flex-col gap-1.5">
            <Label for="nts">Número targeta sanitària</Label>
            <Input
                id="nts"
                type="text"
                name="nts"
                required
                autofocus
                :tabindex="1"
                placeholder="ABCD1234567890"
            />
            <InputError :message="errors.nts" />
        </div>

        <!-- Submit -->
        <Button
            type="submit"
            class="submit-btn w-full mt-1"
            :tabindex="4"
            :disabled="processing"
            data-test="login-button"
        >
            <Spinner v-if="processing" />
            Accedir
        </Button>
    </Form>
</template>