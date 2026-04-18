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
import { loginworkerStore } from '@/routes';
import { request } from '@/routes/password';

</script>

<template>
    <Head title="Iniciar sessió" />

    <!-- Title -->
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Inicia sessió</h1>
        <p class="mt-2 text-sm text-gray-600">Introdueix les teves credencials per accedir al portal.</p>
    </div>

    <Form
        :action="loginworkerStore.url()"
        method="post"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Correu electrónic</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@exemple.com"
                    class="focus:ring-0 focus:outline-none !focus-visible:ring-0 !focus-visible:shadow-none"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Password -->
            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Contrasenya</Label>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Contrasenya"
                    class="focus:ring-0 focus:outline-none !focus-visible:ring-0 !focus-visible:shadow-none"
                />
                <InputError :message="errors.password" />
                <TextLink
                    :href="request()"
                    class="text-sm forgot-link text-pmf-primary text-right"
                    :tabindex="5"
                >
                    Has oblidat la contrasenya?
                </TextLink>
            </div>

            <!-- Submit -->
            <Button
                type="submit"
                class="submit-btn mt-2 w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Accedir
            </Button>
        </div>
    </Form>
</template>