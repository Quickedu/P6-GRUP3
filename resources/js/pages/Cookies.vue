<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue';
import AppFooter from '@/components/AppFooter.vue';
import * as CookieConsent from 'vanilla-cookieconsent';

function accio_ObrirPreferencies() {
    CookieConsent.showPreferences();
}
</script>

<template>
    <Head title="Política de Cookies" />

    <AuthSimpleLayout>
        <main class="container mx-auto px-4 pt-10 pb-16">
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-cf-text">Política de Cookies</h1>
            </div>

            <div class="max-w-5xl mx-auto space-y-6">

                <!-- Que son i per que les utilitzem -->
                <div class="border-2 border-cf-border rounded-xl shadow-lg overflow-hidden hover:border-cf-blue transition-all duration-300">
                    <div class="bg-linear-to-r from-cf-blue/10 to-transparent px-6 py-4 border-b border-cf-border">
                        <h2 class="text-xl font-bold text-cf-text m-0">Què són les cookies?</h2>
                    </div>
                    <div class="px-6 py-5 bg-cf-card">
                        <p class="text-cf-text-dim text-sm">
                            Les cookies són petits fitxers que el navegador guarda al teu dispositiu per
                            recordar la teva sessió i preferències. A Cloud Farm utilitzem cookies
                            necessàries per al funcionament de la web i, opcionalment, cookies d'anàlisi
                            per millorar el servei.
                        </p>
                    </div>
                </div>

                <!-- Canviar preferencies -->
                <div class="border-2 border-cf-border rounded-xl shadow-lg overflow-hidden hover:border-cf-blue transition-all duration-300">
                    <div class="bg-linear-to-r from-cf-blue/10 to-transparent px-6 py-4 border-b border-cf-border">
                        <h2 class="text-xl font-bold text-cf-text m-0">Les teves preferències</h2>
                    </div>
                    <div class="px-6 py-5 bg-cf-card">
                        <p class="text-cf-text-dim text-sm mb-4">
                            Pots acceptar o rebutjar les cookies opcionals en qualsevol moment.
                        </p>
                        <button
                            @click="accio_ObrirPreferencies"
                            class="bg-cf-purple text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-cf-purple-deep transition-all"
                        >
                            Gestionar preferències de cookies
                        </button>
                    </div>
                </div>

            </div>
        </main>
    </AuthSimpleLayout>
    <AppFooter/>
</template>
