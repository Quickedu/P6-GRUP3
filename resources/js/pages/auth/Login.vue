<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { IdCard, CreditCard } from 'lucide-vue-next';
import { ref } from 'vue';
import AuthTabs from '@/components/AuthTabs.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { loginpatientStore } from '@/routes';

defineProps<{
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const authMethod = ref<'dni' | 'targeta'>('dni');

const authOptions = [
    { value: 'dni', label: 'DNI', icon: IdCard },
    { value: 'targeta', label: 'Targeta sanitària', icon: CreditCard },
];
</script>

<template>
    <Head title="Iniciar sessió" />

    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Inicia sessió</h1>
        <p class="mt-2 text-sm text-gray-600">
            Introdueix les teves credencials per accedir al portal.
        </p>
    </div>

    <Form
        :action="loginpatientStore.url()"
        method="post"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="animate-fade-slide-up-delay-2 flex flex-col gap-5"
    >
        <AuthTabs
            v-model="authMethod"
            :options="authOptions"
            label="Mètode d'autenticació"
        />

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
                pattern="^\d{8}[A-Za-z]$"
                title="El DNI ha de tenir 8 dígits seguits d'una lletra (p.ex: 12345678A)"
            />
            <InputError :message="errors.dni" />
        </div>

        <!-- Targeta field -->
        <div v-else id="panel-targeta" class="flex flex-col gap-1.5">
            <Label for="nts">Número targeta sanitària</Label>
            <Input
                id="nts"
                type="text"
                name="nts"
                required
                autofocus
                pattern="^[A-Za-z]{4}[0-9]{10}$"
                title="Tiene que contener 4 letras y 10 números"
                placeholder="ABCD1234567890"
            />
            <InputError :message="errors.nts" />
        </div>

        <!-- Submit -->
        <Button
            type="submit"
            class="submit-btn mt-1 w-full"
            :disabled="processing"
            data-test="login-button"
        >
            <Spinner v-if="processing" />
            Accedir
        </Button>
    </Form>
</template>
