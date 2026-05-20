<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import * as CookieConsent from 'vanilla-cookieconsent';
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue';

function accio_ObrirPreferencies() {
    CookieConsent.showPreferences();
}
</script>

<template>
    <Head title="Política de Cookies" />

    <AuthSimpleLayout>
        <main class="container mx-auto px-4 pt-10 pb-16">
            <div class="mb-8 text-center">
                <h1 class="text-cf-text text-4xl font-bold md:text-5xl">
                    Política de Cookies
                </h1>
            </div>

            <div class="mx-auto max-w-5xl space-y-6">
                <!-- Que son i per que les utilitzem -->
                <div
                    class="border-cf-border hover:border-cf-blue overflow-hidden rounded-xl border-2 shadow-lg transition-all duration-300"
                >
                    <div
                        class="from-cf-blue/10 border-cf-border border-b bg-linear-to-r to-transparent px-6 py-4"
                    >
                        <h2 class="text-cf-text m-0 text-xl font-bold">
                            Què són les cookies?
                        </h2>
                    </div>
                    <div class="bg-cf-card px-6 py-5">
                        <p class="text-cf-text-dim text-sm">
                            Les cookies són petits fitxers que el navegador
                            guarda al teu dispositiu per recordar la teva sessió
                            i preferències. A Cloud Farm utilitzem cookies
                            necessàries per al funcionament de la web i,
                            opcionalment, cookies d'anàlisi per millorar el
                            servei.
                        </p>
                    </div>
                </div>

                <!-- Canviar preferencies -->
                <div
                    class="border-cf-border hover:border-cf-blue overflow-hidden rounded-xl border-2 shadow-lg transition-all duration-300"
                >
                    <div
                        class="from-cf-blue/10 border-cf-border border-b bg-linear-to-r to-transparent px-6 py-4"
                    >
                        <h2 class="text-cf-text m-0 text-xl font-bold">
                            Les teves preferències
                        </h2>
                    </div>
                    <div class="bg-cf-card px-6 py-5">
                        <p class="text-cf-text-dim mb-4 text-sm">
                            Pots acceptar o rebutjar les cookies opcionals en
                            qualsevol moment.
                        </p>
                        <button
                            @click="accio_ObrirPreferencies"
                            class="bg-cf-purple hover:bg-cf-purple-deep rounded-xl px-6 py-3 font-bold text-white shadow-lg transition-all"
                        >
                            Gestionar preferències de cookies
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </AuthSimpleLayout>
</template>
