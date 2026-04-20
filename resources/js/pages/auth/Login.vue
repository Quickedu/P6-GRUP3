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
import AuthTabs from '@/components/AuthTabs.vue';
import { IdCard, CreditCard } from 'lucide-vue-next';

defineProps<{
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const authMethod = ref<'dni' | 'targeta'>('dni');

const authOptions = [
    { value: 'dni', label: 'DNI', icon: IdCard },
    { value: 'targeta', label: 'Targeta sanitària', icon: CreditCard }
];
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
        class="flex flex-col gap-5 animate-fade-slide-up-delay-2"
    >
        <!-- Auth method tabs -->
        <AuthTabs
            v-model="authMethod"
            :options="authOptions"
            label="Mètode d'autenticació"
        />

        <!-- Para enviar el valor -->
        <input type="hidden" name="auth_method" :value="authMethod" />

        <!-- DNI field -->
        <div
            v-if="authMethod === 'dni'"
            id="panel-dni"
            class="flex flex-col gap-1.5"
        >
            <Label for="dni">DNI</Label>
            <Input
                id="dni"
                type="text"
                name="dni"
                required
                autofocus
                placeholder="12345678A"
            />
            <InputError :message="errors.dni" />
        </div>

        <!-- Targeta field -->
        <div
            v-else
            id="panel-targeta"
            class="flex flex-col gap-1.5"
        >
            <Label for="nts">Número targeta sanitària</Label>
            <Input
                id="nts"
                type="text"
                name="nts"
                required
                autofocus
                placeholder="ABCD1234567890"
            />
            <InputError :message="errors.nts" />
        </div>

        <!-- Submit -->
        <Button
            type="submit"
            class="submit-btn w-full mt-1"
            :disabled="processing"
            data-test="login-button"
        >
            <Spinner v-if="processing" />
            Accedir
        </Button>
    </Form>
</template>