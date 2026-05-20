<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { home } from '@/routes';
</script>

<template>
    <Head title="Política de privacitat">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&family=Instrument+Sans:wght@400;500;600&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#f6fbfa] via-[#e8f4f2] to-white text-[#003d35]"
    >
        <div
            class="pointer-events-none absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-[rgba(0,163,142,0.25)] opacity-70 blur-[40px]"
        ></div>
        <div
            class="pointer-events-none absolute -right-8 -bottom-12 h-64 w-64 rounded-full bg-[rgba(0,101,87,0.18)] opacity-70 blur-[40px]"
        ></div>

        <header
            class="sticky top-0 z-30 border-b border-[rgba(0,101,87,0.12)] bg-white/85 backdrop-blur-md"
        >
            <div
                class="mx-auto flex w-full max-w-[72rem] items-center justify-between gap-6 px-6 py-4"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="grid h-[52px] w-[52px] place-items-center rounded-full bg-white/95"
                    >
                        <img
                            src="/images/logo1.png"
                            alt="Logo PMF"
                            class="h-full w-full object-contain"
                        />
                    </div>
                    <div>
                        <p
                            class="m-0 font-['Space_Grotesk'] text-[1rem] font-semibold text-[#006557]"
                        >
                            PMF Salut
                        </p>
                        <p class="m-0 text-xs text-[#7a9e99]">
                            Programa de millora de flux
                        </p>
                    </div>
                </div>
                <Link
                    :href="home()"
                    class="inline-flex items-center justify-center rounded-full bg-[#00574b] px-4 py-2 text-xs font-semibold text-[#d4f0eb] no-underline shadow-[0_10px_25px_rgba(0,101,87,0.18)] transition-all duration-200 hover:-translate-y-px hover:bg-[#00a38e] sm:text-sm"
                >
                    Tornar a l'inici
                </Link>
            </div>
        </header>

        <main class="mx-auto w-full max-w-[72rem] px-6 pt-12 pb-20">
            <div class="max-w-[46rem]">
                <p
                    class="inline-flex items-center gap-2 rounded-full bg-[#d4f0eb] px-3 py-1 text-[0.65rem] font-semibold tracking-[0.2em] text-[#003d35] uppercase"
                >
                    Informació legal
                </p>
                <h1
                    class="mt-4 mb-0 font-['Space_Grotesk'] text-[clamp(2rem,3vw,3.2rem)] leading-[1.1] text-[#003d35]"
                >
                    Política de privacitat
                </h1>
                <p class="mt-4 text-[1rem] leading-relaxed text-[#7a9e99]">
                    Aquesta política descriu com PMF Salut tracta les dades
                    personals de pacients i professionals per gestionar les
                    cites, els informes mèdics i l'accés a l'àrea personal.
                </p>
            </div>

            <div class="mt-10 grid gap-6">
                <section
                    class="rounded-[24px] border border-[rgba(0,101,87,0.12)] bg-white/92 p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]"
                >
                    <h2
                        class="m-0 font-['Space_Grotesk'] text-xl text-[#003d35]"
                    >
                        Dades que tractem
                    </h2>
                    <ul class="mt-4 grid gap-2 text-[0.95rem] text-[#356058]">
                        <li>
                            Identificatives i de contacte: nom, NTS, DNI,
                            adreça, telèfon, correu electrònic i data de
                            naixement.
                        </li>
                        <li>
                            Dades assistencials: cites, data i hora, temps,
                            estat, urgència, descripció i proves associades.
                        </li>
                        <li>
                            Dades sanitàries: informes, resultats i imatges
                            associades als informes.
                        </li>
                        <li>
                            Dades professionals: NSS, adreça laboral, DNI i
                            número de llicència del personal.
                        </li>
                        <li>
                            Dades d'accés: usuari, rol, contrasenya xifrada i
                            elements de doble factor.
                        </li>
                        <li>
                            Dades tècniques: sessions, adreça IP i agent
                            d'usuari.
                        </li>
                    </ul>
                </section>

                <section
                    class="rounded-[24px] border border-[rgba(0,101,87,0.12)] bg-white/92 p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]"
                >
                    <h2
                        class="m-0 font-['Space_Grotesk'] text-xl text-[#003d35]"
                    >
                        Finalitats del tractament
                    </h2>
                    <ul class="mt-4 grid gap-2 text-[0.95rem] text-[#356058]">
                        <li>
                            Gestionar l'agenda de cites, canvis d'hora i
                            necessitats assistencials.
                        </li>
                        <li>
                            Facilitar l'accés als informes i a l'historial
                            clínic de manera segura.
                        </li>
                        <li>
                            Coordinar l'atenció amb el personal sanitari i
                            administratiu.
                        </li>
                        <li>
                            Assegurar el control d'accés, la seguretat i el
                            registre d'activitat.
                        </li>
                        <li>
                            Complir les obligacions legals i de conservació
                            aplicables.
                        </li>
                    </ul>
                </section>

                <section class="grid gap-6 md:grid-cols-2">
                    <div
                        class="rounded-[24px] border border-[rgba(0,101,87,0.12)] bg-white/92 p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]"
                    >
                        <h2
                            class="m-0 font-['Space_Grotesk'] text-xl text-[#003d35]"
                        >
                            Conservació i destinataris
                        </h2>
                        <p class="mt-4 text-[0.95rem] text-[#356058]">
                            Conservem les dades mentre siguin necessàries per a
                            l'assistència i per complir les obligacions legals.
                            Només hi accedeix el personal autoritzat i els
                            proveïdors tècnics imprescindibles.
                        </p>
                    </div>
                    <div
                        class="rounded-[24px] border border-[rgba(0,101,87,0.12)] bg-white/92 p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]"
                    >
                        <h2
                            class="m-0 font-['Space_Grotesk'] text-xl text-[#003d35]"
                        >
                            Seguretat i drets
                        </h2>
                        <p class="mt-4 text-[0.95rem] text-[#356058]">
                            Apliquem mesures de seguretat i control d'accés per
                            protegir la confidencialitat. Pots exercir els teus
                            drets d'accés, rectificació, supressió, limitació,
                            oposició i portabilitat contactant amb el centre.
                        </p>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>
