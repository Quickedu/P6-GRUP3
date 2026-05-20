<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login } from '@/routes';

const navItems = [
    { id: 'resum', label: 'Resum' },
    { id: 'serveis', label: 'Serveis' },
    { id: 'informes', label: 'Informes' },
    { id: 'contacte', label: 'Contacte' },
];

defineOptions({
    layout: null,
});
</script>

<template>
    <Head title="Inici">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&family=Instrument+Sans:wght@400;500;600&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="relative min-h-screen overflow-hidden [scroll-behavior:smooth] bg-gradient-to-br from-[#f6fbfa] via-[#e8f4f2] to-white text-[#003d35]"
    >
        <!-- Orbs decoratius -->
        <div
            class="pointer-events-none absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-[rgba(0,163,142,0.25)] opacity-70 blur-[40px]"
        ></div>
        <div
            class="pointer-events-none absolute -right-8 -bottom-12 h-64 w-64 rounded-full bg-[rgba(0,101,87,0.18)] opacity-70 blur-[40px]"
        ></div>

        <!-- Header -->
        <header
            class="sticky top-0 z-30 border-b border-[rgba(0,101,87,0.12)] bg-white/85 backdrop-blur-md"
        >
            <div class="mx-auto w-full max-w-[72rem] px-6 py-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <!-- Brand -->
                    <div class="flex items-center gap-3">
                        <div
                            class="grid h-[60px] w-[60px] place-items-center rounded-full bg-white/95"
                        >
                            <img
                                src="/images/logo1.png"
                                alt="Logo PMF"
                                class="h-full w-full object-contain"
                            />
                        </div>
                        <div>
                            <p
                                class="m-0 font-['Space_Grotesk'] text-[1.1rem] font-semibold text-[#006557]"
                            >
                                PMF Salut
                            </p>
                            <p class="m-0 text-xs text-[#7a9e99]">
                                Programa de millora de flux
                            </p>
                        </div>
                    </div>

                    <!-- Nav desktop -->
                    <nav
                        class="hidden items-center gap-6 text-sm font-medium md:flex"
                    >
                        <a
                            v-for="item in navItems"
                            :key="item.id"
                            :href="`#${item.id}`"
                            class="text-[#7a9e99] no-underline transition-colors duration-200 hover:text-[#006557]"
                        >
                            {{ item.label }}
                        </a>
                    </nav>

                    <Link
                        :href="login()"
                        class="hidden items-center justify-center rounded-full bg-[#00574b] px-5 py-2 text-sm font-semibold text-[#d4f0eb] no-underline shadow-[0_10px_25px_rgba(0,101,87,0.18)] transition-all duration-200 hover:-translate-y-px hover:bg-[#00a38e] sm:inline-flex"
                    >
                        Àrea personal
                    </Link>
                </div>

                <Link
                    :href="login()"
                    class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-[#00574b] px-5 py-2 text-sm font-semibold text-[#d4f0eb] no-underline shadow-[0_10px_25px_rgba(0,101,87,0.18)] transition-all duration-200 hover:-translate-y-px hover:bg-[#00a38e] sm:hidden"
                >
                    Àrea personal
                </Link>

                <details
                    class="mt-4 rounded-[20px] border border-[rgba(0,101,87,0.12)] bg-white/90 px-4 py-3 shadow-[0_14px_24px_rgba(0,48,41,0.08)] md:hidden"
                >
                    <summary
                        class="flex cursor-pointer items-center justify-between gap-3 text-sm font-semibold text-[#006557] [&::-webkit-details-marker]:hidden"
                    >
                        Menú
                        <span class="relative h-4 w-5">
                            <span
                                class="absolute inset-x-0 top-0 h-0.5 rounded-full bg-[#00574b]"
                            ></span>
                            <span
                                class="absolute inset-x-0 top-1/2 h-0.5 -translate-y-1/2 rounded-full bg-[#00574b]"
                            ></span>
                            <span
                                class="absolute inset-x-0 bottom-0 h-0.5 rounded-full bg-[#00574b]"
                            ></span>
                        </span>
                    </summary>
                    <nav class="mt-3 grid gap-2 text-sm font-medium">
                        <a
                            v-for="item in navItems"
                            :key="item.id"
                            :href="`#${item.id}`"
                            class="rounded-full px-4 py-2 text-[#7a9e99] no-underline transition-colors duration-200 hover:bg-[#d4f0eb] hover:text-[#006557]"
                        >
                            {{ item.label }}
                        </a>
                    </nav>
                </details>
            </div>
        </header>

        <!-- Main -->
        <main class="mx-auto w-full max-w-[72rem] px-6 pt-12 pb-20">
            <!-- Secció Resum -->
            <section
                id="resum"
                class="grid items-center gap-12 lg:grid-cols-[1.05fr_0.95fr]"
            >
                <!-- Copy -->
                <div class="max-w-[34rem]">
                    <p
                        class="inline-flex items-center gap-2 rounded-full bg-[#d4f0eb] px-3 py-1 text-[0.65rem] font-semibold tracking-[0.2em] text-[#003d35] uppercase"
                    >
                        Salut digital
                    </p>
                    <h1
                        class="mt-4 mb-0 font-['Space_Grotesk'] text-[clamp(2.2rem,3vw,3.6rem)] leading-[1.1] text-[#003d35]"
                    >
                        El teu espai per consultar cites i informes mèdics
                    </h1>
                    <p
                        class="mt-4 text-[1.05rem] leading-relaxed text-[#7a9e99]"
                    >
                        Accedeix i podràs veure les cites que tens programades i
                        els resultats mèdics registrats.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-4">
                        <Link
                            :href="login()"
                            class="inline-flex items-center justify-center rounded-full bg-[#00574b] px-7 py-3 text-sm font-semibold text-[#d4f0eb] no-underline shadow-[0_12px_24px_rgba(0,101,87,0.2)] transition-all duration-200 hover:-translate-y-px hover:bg-[#00a38e]"
                        >
                            Àrea personal
                        </Link>
                    </div>
                    <div
                        class="mt-6 grid grid-cols-[repeat(auto-fit,minmax(170px,1fr))] gap-4"
                    >
                        <div
                            class="rounded-[18px] border border-[rgba(0,101,87,0.12)] bg-white/85 p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]"
                        >
                            <p class="m-0 text-xs text-[#7a9e99]">Cites</p>
                            <p class="m-0 mt-1 font-semibold text-[#006557]">
                                Agenda amb les properes cites
                            </p>
                        </div>
                        <div
                            class="rounded-[18px] border border-[rgba(0,101,87,0.12)] bg-white/85 p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]"
                        >
                            <p class="m-0 text-xs text-[#7a9e99]">Informes</p>
                            <p class="m-0 mt-1 font-semibold text-[#006557]">
                                Informes i resultats
                            </p>
                        </div>
                        <div
                            class="rounded-[18px] border border-[rgba(0,101,87,0.12)] bg-white/85 p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]"
                        >
                            <p class="m-0 text-xs text-[#7a9e99]">Suport</p>
                            <p class="m-0 mt-1 font-semibold text-[#006557]">
                                Cancel·la una cita en qualsevol moment
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Visual rail -->
                <div class="flex justify-center">
                    <div
                        class="relative w-[min(360px,100%)] rotate-[-25deg] rounded-[32px] bg-[rgba(0,111,101,0.1)] p-[18px] shadow-[0_30px_60px_rgba(0,48,41,0.18)] max-[900px]:rotate-0"
                    >
                        <div
                            class="grid rotate-[25deg] gap-0 max-[900px]:rotate-0"
                        >
                            <!-- Slice 1 -->
                            <figure
                                class="relative m-0 h-[150px] overflow-hidden bg-white sm:h-[170px] lg:h-[160px]"
                                style="
                                    clip-path: polygon(
                                        0 0,
                                        100% 0,
                                        100% calc(100% - 22px),
                                        0 calc(100% - 6px)
                                    );
                                "
                            >
                                <img
                                    src="/images/grup2.jpeg"
                                    alt="Equip mèdic"
                                    class="block h-full w-full object-cover"
                                />
                            </figure>
                            <!-- Slice 2 -->
                            <figure
                                class="relative m-0 h-[150px] overflow-hidden bg-white sm:h-[170px] lg:h-[160px]"
                                style="
                                    clip-path: polygon(
                                        0 22px,
                                        100% 6px,
                                        100% calc(100% - 6px),
                                        0 calc(100% - 22px)
                                    );
                                "
                            >
                                <img
                                    src="/images/logo.png"
                                    alt="Logo PMF"
                                    class="block h-full w-full object-contain p-6"
                                />
                            </figure>
                            <!-- Slice 3 -->
                            <figure
                                class="relative m-0 h-[150px] overflow-hidden bg-white sm:h-[170px] lg:h-[160px]"
                                style="
                                    clip-path: polygon(
                                        0 6px,
                                        100% 22px,
                                        100% 100%,
                                        0 100%
                                    );
                                "
                            >
                                <img
                                    src="/images/nosaltres1.jpeg"
                                    alt="Equip de programadors"
                                    class="block h-full w-full object-cover"
                                />
                            </figure>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Secció Serveis -->
            <section id="serveis" class="pt-16">
                <div class="flex items-center gap-4">
                    <span class="h-1 w-12 rounded-full bg-[#00c9b1]"></span>
                    <h2
                        class="m-0 font-['Space_Grotesk'] text-2xl text-[#003d35]"
                    >
                        Serveis clau per a pacients
                    </h2>
                </div>
                <div
                    class="mt-6 grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-6"
                >
                    <article
                        class="rounded-[20px] border border-[rgba(0,101,87,0.12)] bg-white p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]"
                    >
                        <div
                            class="grid h-12 w-12 place-items-center rounded-full bg-[#d4f0eb] font-semibold text-[#00574b]"
                        >
                            01
                        </div>
                        <h3 class="mt-4 font-semibold text-[#006557]">
                            Agenda personal
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-[#356058]">
                            Consulta totes les cites, canvis d'hora i detalls
                            amb un sol cop d'ull.
                        </p>
                    </article>
                    <article
                        class="rounded-[20px] border border-[rgba(0,101,87,0.12)] bg-white p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]"
                    >
                        <div
                            class="grid h-12 w-12 place-items-center rounded-full bg-[#d4f0eb] font-semibold text-[#00574b]"
                        >
                            02
                        </div>
                        <h3 class="mt-4 font-semibold text-[#006557]">
                            Informes digitals
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-[#356058]">
                            Descarrega els informes quan vulguis i guarda
                            l'historial al teu espai.
                        </p>
                    </article>
                    <article
                        class="rounded-[20px] border border-[rgba(0,101,87,0.12)] bg-white p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]"
                    >
                        <div
                            class="grid h-12 w-12 place-items-center rounded-full bg-[#d4f0eb] font-semibold text-[#00574b]"
                        >
                            03
                        </div>
                        <h3 class="mt-4 font-semibold text-[#006557]">
                            Seguiment segur
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-[#356058]">
                            Les teves dades es mantenen protegides amb accés
                            controlat i clar.
                        </p>
                    </article>
                </div>
            </section>

            <!-- Secció Informes -->
            <section id="informes" class="pt-16">
                <div class="grid items-center gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div>
                        <h2
                            class="m-0 font-['Space_Grotesk'] text-2xl text-[#003d35]"
                        >
                            Tot el teu historial, sense complicacions
                        </h2>
                        <p
                            class="mt-3 text-[0.95rem] leading-relaxed text-[#7a9e99]"
                        >
                            Revisa resultats, prepara dubtes abans de la visita
                            i comparteix la informació quan calgui. Tot pensat
                            per ser clar i ràpid.
                        </p>
                        <div
                            class="mt-6 grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-4"
                        >
                            <div
                                class="rounded-[18px] border border-[rgba(0,101,87,0.12)] bg-white/85 p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]"
                            >
                                <p class="m-0 text-xs text-[#7a9e99]">
                                    Avisos clars
                                </p>
                                <p
                                    class="m-0 mt-1 font-semibold text-[#006557]"
                                >
                                    Notificacions a temps
                                </p>
                            </div>
                            <div
                                class="rounded-[18px] border border-[rgba(0,101,87,0.12)] bg-white/85 p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]"
                            >
                                <p class="m-0 text-xs text-[#7a9e99]">
                                    Historial complet
                                </p>
                                <p
                                    class="m-0 mt-1 font-semibold text-[#006557]"
                                >
                                    Tot centralitzat
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-[24px] border border-[rgba(0,101,87,0.12)] bg-white/92 p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]"
                    >
                        <div class="flex items-center justify-between">
                            <p
                                class="m-0 text-[0.95rem] font-semibold text-[#006557]"
                            >
                                Informe de prova
                            </p>
                            <span
                                class="rounded-full bg-[#d4f0eb] px-3 py-1 text-[0.7rem] font-semibold text-[#00574b]"
                                >PDF</span
                            >
                        </div>
                        <p class="mt-3 text-sm text-[#7a9e99]">
                            Diagnosi, recomanacions i seguiment en una sola
                            fitxa.
                        </p>
                        <div class="mt-6 grid gap-2">
                            <div
                                class="h-2 w-[85%] rounded-full bg-[#d4f0eb]"
                            ></div>
                            <div
                                class="h-2 w-[70%] rounded-full bg-[#d4f0eb]"
                            ></div>
                            <div
                                class="h-2 w-[55%] rounded-full bg-[#d4f0eb]"
                            ></div>
                        </div>
                        <div class="mt-6 flex items-center gap-3">
                            <span
                                class="h-9 w-9 shrink-0 rounded-full bg-[#d4f0eb]"
                            ></span>
                            <div>
                                <p
                                    class="m-0 text-sm font-semibold text-[#006557]"
                                >
                                    Equip PMF
                                </p>
                                <p class="m-0 text-xs text-[#7a9e99]">
                                    Suport i seguiment
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Secció Contacte / CTA -->
            <section id="contacte" class="pt-16">
                <div
                    class="flex flex-col gap-6 rounded-[24px] border border-[rgba(0,101,87,0.12)] bg-white/92 p-8 shadow-[0_18px_32px_rgba(0,48,41,0.08)] md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <h2
                            class="m-0 font-['Space_Grotesk'] text-2xl text-[#003d35]"
                        >
                            Preparat per gestionar la teva salut?
                        </h2>
                        <p
                            class="mt-3 text-[0.95rem] leading-relaxed text-[#7a9e99]"
                        >
                            Accedeix a l'àrea personal i revisa totes les teves
                            cites i informes.
                        </p>
                    </div>
                    <Link
                        :href="login()"
                        class="inline-flex shrink-0 items-center justify-center rounded-full bg-[#00574b] px-7 py-3 text-sm font-semibold text-[#d4f0eb] no-underline shadow-[0_12px_24px_rgba(0,101,87,0.2)] transition-all duration-200 hover:-translate-y-px hover:bg-[#00a38e]"
                    >
                        Entrar a l'àrea personal
                    </Link>
                </div>
            </section>

            <footer
                class="mt-12 flex flex-col items-start justify-between gap-4 border-t border-[rgba(0,101,87,0.12)] pt-12 text-sm text-[#7a9e99] sm:flex-row sm:items-center"
            >
                <p class="m-0">PMF Salut</p>
                <Link
                    href="/privacitat"
                    class="font-semibold text-[#006557] no-underline transition-colors hover:text-[#00a38e]"
                >
                    Política de privacitat
                </Link>
            </footer>
        </main>
    </div>
</template>
