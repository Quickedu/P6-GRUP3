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

defineProps<{
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Iniciar sessió" />
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
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Password -->
            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Contrasenya</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm forgot-link"
                        :tabindex="5"
                    >
                        Has oblidat la contrasenya?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Contrasenya"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span class="text-sm" style="color: var(--pmf-grey-light)">Recordar sessió</span>
                </Label>
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