<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login } from '@/routes';
import * as CookieConsent from 'vanilla-cookieconsent';

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

	<div class="min-h-screen bg-gradient-to-br from-[#f6fbfa] via-[#e8f4f2] to-white text-[#003d35] relative overflow-hidden [scroll-behavior:smooth]">

		<!-- Orbs decoratius -->
		<div class="absolute pointer-events-none rounded-full blur-[40px] opacity-70 -top-24 left-1/2 -translate-x-1/2 w-72 h-72 bg-[rgba(0,163,142,0.25)]"></div>
		<div class="absolute pointer-events-none rounded-full blur-[40px] opacity-70 -bottom-12 -right-8 w-64 h-64 bg-[rgba(0,101,87,0.18)]"></div>

		<!-- Header -->
		<header class="sticky top-0 z-30 bg-white/85 border-b border-[rgba(0,101,87,0.12)] backdrop-blur-md">
			<div class="w-full max-w-[72rem] mx-auto px-6 py-4">
				<div class="flex flex-wrap items-center justify-between gap-4">
					<!-- Brand -->
					<div class="flex items-center gap-3">
						<div class="w-[60px] h-[60px] grid place-items-center rounded-full bg-white/95">
							<img src="/images/logo1.png" alt="Logo PMF" class="object-contain w-full h-full" />
						</div>
						<div>
							<p class="font-['Space_Grotesk'] text-[1.1rem] font-semibold text-[#006557] m-0">PMF Salut</p>
							<p class="text-xs text-[#7a9e99] m-0">Programa de millora de flux</p>
						</div>
					</div>

					<!-- Nav desktop -->
					<nav class="hidden md:flex items-center gap-6 text-sm font-medium">
						<a
							v-for="item in navItems"
							:key="item.id"
							:href="`#${item.id}`"
							class="text-[#7a9e99] no-underline hover:text-[#006557] transition-colors duration-200"
						>
							{{ item.label }}
						</a>
					</nav>

					<Link
						:href="login()"
						class="hidden sm:inline-flex items-center justify-center px-5 py-2 rounded-full bg-[#00574b] text-[#d4f0eb] text-sm font-semibold shadow-[0_10px_25px_rgba(0,101,87,0.18)] no-underline hover:bg-[#00a38e] hover:-translate-y-px transition-all duration-200"
					>
						Àrea personal
					</Link>
				</div>

				<Link
					:href="login()"
					class="sm:hidden mt-4 inline-flex w-full items-center justify-center px-5 py-2 rounded-full bg-[#00574b] text-[#d4f0eb] text-sm font-semibold shadow-[0_10px_25px_rgba(0,101,87,0.18)] no-underline hover:bg-[#00a38e] hover:-translate-y-px transition-all duration-200"
				>
					Àrea personal
				</Link>

				<details class="md:hidden mt-4 rounded-[20px] border border-[rgba(0,101,87,0.12)] bg-white/90 px-4 py-3 shadow-[0_14px_24px_rgba(0,48,41,0.08)]">
					<summary class="flex items-center justify-between gap-3 cursor-pointer text-sm font-semibold text-[#006557] [&::-webkit-details-marker]:hidden">
						Menú
						<span class="relative w-5 h-4">
							<span class="absolute inset-x-0 top-0 h-0.5 rounded-full bg-[#00574b]"></span>
							<span class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-0.5 rounded-full bg-[#00574b]"></span>
							<span class="absolute inset-x-0 bottom-0 h-0.5 rounded-full bg-[#00574b]"></span>
						</span>
					</summary>
					<nav class="mt-3 grid gap-2 text-sm font-medium">
						<a
							v-for="item in navItems"
							:key="item.id"
							:href="`#${item.id}`"
							class="rounded-full px-4 py-2 text-[#7a9e99] no-underline hover:text-[#006557] hover:bg-[#d4f0eb] transition-colors duration-200"
						>
							{{ item.label }}
						</a>
					</nav>
				</details>
			</div>
		</header>

		<!-- Main -->
		<main class="w-full max-w-[72rem] mx-auto px-6 pt-12 pb-20">

			<!-- Secció Resum -->
			<section id="resum" class="grid gap-12 items-center lg:grid-cols-[1.05fr_0.95fr]">
				<!-- Copy -->
				<div class="max-w-[34rem]">
					<p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#d4f0eb] text-[#003d35] text-[0.65rem] tracking-[0.2em] uppercase font-semibold">
						Salut digital
					</p>
					<h1 class="font-['Space_Grotesk'] text-[clamp(2.2rem,3vw,3.6rem)] leading-[1.1] text-[#003d35] mt-4 mb-0">
						El teu espai per consultar cites i informes mèdics
					</h1>
					<p class="mt-4 text-[1.05rem] leading-relaxed text-[#7a9e99]">
						Accedeix i podràs veure les cites que tens programades i els resultats mèdics registrats.
					</p>
					<div class="flex flex-wrap gap-4 mt-6">
						<Link
							:href="login()"
							class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-[#00574b] text-[#d4f0eb] text-sm font-semibold shadow-[0_12px_24px_rgba(0,101,87,0.2)] no-underline hover:bg-[#00a38e] hover:-translate-y-px transition-all duration-200"
						>
							Àrea personal
						</Link>
					</div>
					<div class="grid gap-4 mt-6 grid-cols-[repeat(auto-fit,minmax(170px,1fr))]">
						<div class="bg-white/85 border border-[rgba(0,101,87,0.12)] rounded-[18px] p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]">
							<p class="text-xs text-[#7a9e99] m-0">Cites</p>
							<p class="mt-1 font-semibold text-[#006557] m-0">Agenda amb les properes cites</p>
						</div>
						<div class="bg-white/85 border border-[rgba(0,101,87,0.12)] rounded-[18px] p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]">
							<p class="text-xs text-[#7a9e99] m-0">Informes</p>
							<p class="mt-1 font-semibold text-[#006557] m-0">Informes i resultats</p>
						</div>
						<div class="bg-white/85 border border-[rgba(0,101,87,0.12)] rounded-[18px] p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]">
							<p class="text-xs text-[#7a9e99] m-0">Suport</p>
							<p class="mt-1 font-semibold text-[#006557] m-0">Cancel·la una cita en qualsevol moment</p>
						</div>
					</div>
				</div>

				<!-- Visual rail -->
				<div class="flex justify-center">
					<div class="relative w-[min(360px,100%)] p-[18px] rounded-[32px] bg-[rgba(0,111,101,0.1)] shadow-[0_30px_60px_rgba(0,48,41,0.18)] rotate-[-25deg] max-[900px]:rotate-0">
						<div class="grid gap-0 rotate-[25deg] max-[900px]:rotate-0">
							<!-- Slice 1 -->
							<figure class="relative overflow-hidden bg-white h-[150px] sm:h-[170px] lg:h-[160px] m-0"
								style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 22px), 0 calc(100% - 6px))">
								<img src="/images/grup2.jpeg" alt="Equip mèdic" class="w-full h-full object-cover block" />
							</figure>
							<!-- Slice 2 -->
							<figure class="relative overflow-hidden bg-white h-[150px] sm:h-[170px] lg:h-[160px] m-0"
								style="clip-path: polygon(0 22px, 100% 6px, 100% calc(100% - 6px), 0 calc(100% - 22px))">
								<img src="/images/logo.png" alt="Logo PMF" class="w-full h-full object-contain p-6 block" />
							</figure>
							<!-- Slice 3 -->
							<figure class="relative overflow-hidden bg-white h-[150px] sm:h-[170px] lg:h-[160px] m-0"
								style="clip-path: polygon(0 6px, 100% 22px, 100% 100%, 0 100%)">
								<img src="/images/nosaltres1.jpeg" alt="Equip de programadors" class="w-full h-full object-cover block" />
							</figure>
						</div>
					</div>
				</div>
			</section>

			<!-- Secció Serveis -->
			<section id="serveis" class="pt-16">
				<div class="flex items-center gap-4">
					<span class="w-12 h-1 rounded-full bg-[#00c9b1]"></span>
					<h2 class="font-['Space_Grotesk'] text-2xl text-[#003d35] m-0">Serveis clau per a pacients</h2>
				</div>
				<div class="grid gap-6 mt-6 grid-cols-[repeat(auto-fit,minmax(220px,1fr))]">
					<article class="bg-white border border-[rgba(0,101,87,0.12)] rounded-[20px] p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]">
						<div class="w-12 h-12 rounded-full bg-[#d4f0eb] text-[#00574b] font-semibold grid place-items-center">01</div>
						<h3 class="mt-4 font-semibold text-[#006557]">Agenda personal</h3>
						<p class="mt-2 text-sm leading-relaxed text-[#356058]">
							Consulta totes les cites, canvis d'hora i detalls amb un sol cop d'ull.
						</p>
					</article>
					<article class="bg-white border border-[rgba(0,101,87,0.12)] rounded-[20px] p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]">
						<div class="w-12 h-12 rounded-full bg-[#d4f0eb] text-[#00574b] font-semibold grid place-items-center">02</div>
						<h3 class="mt-4 font-semibold text-[#006557]">Informes digitals</h3>
						<p class="mt-2 text-sm leading-relaxed text-[#356058]">
							Descarrega els informes quan vulguis i guarda l'historial al teu espai.
						</p>
					</article>
					<article class="bg-white border border-[rgba(0,101,87,0.12)] rounded-[20px] p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]">
						<div class="w-12 h-12 rounded-full bg-[#d4f0eb] text-[#00574b] font-semibold grid place-items-center">03</div>
						<h3 class="mt-4 font-semibold text-[#006557]">Seguiment segur</h3>
						<p class="mt-2 text-sm leading-relaxed text-[#356058]">
							Les teves dades es mantenen protegides amb accés controlat i clar.
						</p>
					</article>
				</div>
			</section>

			<!-- Secció Informes -->
			<section id="informes" class="pt-16">
				<div class="grid gap-6 items-center lg:grid-cols-[1.1fr_0.9fr]">
					<div>
						<h2 class="font-['Space_Grotesk'] text-2xl text-[#003d35] m-0">Tot el teu historial, sense complicacions</h2>
						<p class="mt-3 text-[#7a9e99] text-[0.95rem] leading-relaxed">
							Revisa resultats, prepara dubtes abans de la visita i comparteix la informació quan calgui. Tot pensat per ser clar i ràpid.
						</p>
						<div class="grid gap-4 mt-6 grid-cols-[repeat(auto-fit,minmax(150px,1fr))]">
							<div class="bg-white/85 border border-[rgba(0,101,87,0.12)] rounded-[18px] p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]">
								<p class="text-xs text-[#7a9e99] m-0">Avisos clars</p>
								<p class="mt-1 font-semibold text-[#006557] m-0">Notificacions a temps</p>
							</div>
							<div class="bg-white/85 border border-[rgba(0,101,87,0.12)] rounded-[18px] p-4 shadow-[0_16px_30px_rgba(0,48,41,0.08)]">
								<p class="text-xs text-[#7a9e99] m-0">Historial complet</p>
								<p class="mt-1 font-semibold text-[#006557] m-0">Tot centralitzat</p>
							</div>
						</div>
					</div>
					<div class="bg-white/92 border border-[rgba(0,101,87,0.12)] rounded-[24px] p-6 shadow-[0_18px_32px_rgba(0,48,41,0.08)]">
						<div class="flex items-center justify-between">
							<p class="text-[0.95rem] font-semibold text-[#006557] m-0">Informe de prova</p>
							<span class="bg-[#d4f0eb] text-[#00574b] rounded-full px-3 py-1 text-[0.7rem] font-semibold">PDF</span>
						</div>
						<p class="mt-3 text-[#7a9e99] text-sm">
							Diagnosi, recomanacions i seguiment en una sola fitxa.
						</p>
						<div class="mt-6 grid gap-2">
							<div class="h-2 bg-[#d4f0eb] rounded-full w-[85%]"></div>
							<div class="h-2 bg-[#d4f0eb] rounded-full w-[70%]"></div>
							<div class="h-2 bg-[#d4f0eb] rounded-full w-[55%]"></div>
						</div>
						<div class="mt-6 flex items-center gap-3">
							<span class="w-9 h-9 rounded-full bg-[#d4f0eb] shrink-0"></span>
							<div>
								<p class="m-0 text-sm font-semibold text-[#006557]">Equip PMF</p>
								<p class="m-0 text-xs text-[#7a9e99]">Suport i seguiment</p>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Secció Contacte / CTA -->
			<section id="contacte" class="pt-16">
				<div class="bg-white/92 border border-[rgba(0,101,87,0.12)] rounded-[24px] p-8 shadow-[0_18px_32px_rgba(0,48,41,0.08)] flex flex-col md:flex-row md:items-center md:justify-between gap-6">
					<div>
						<h2 class="font-['Space_Grotesk'] text-2xl text-[#003d35] m-0">Preparat per gestionar la teva salut?</h2>
						<p class="mt-3 text-[#7a9e99] text-[0.95rem] leading-relaxed">
							Accedeix a l'àrea personal i revisa totes les teves cites i informes.
						</p>
					</div>
					<Link
						:href="login()"
						class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-[#00574b] text-[#d4f0eb] text-sm font-semibold shadow-[0_12px_24px_rgba(0,101,87,0.2)] no-underline hover:bg-[#00a38e] hover:-translate-y-px transition-all duration-200 shrink-0"
					>
						Entrar a l'àrea personal
					</Link>
				</div>
			</section>

			<footer class="pt-12 mt-12 border-t border-[rgba(0,101,87,0.12)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-sm text-[#7a9e99]">
				<p class="m-0">PMF Salut</p>
				<Link
					href="/privacitat"
					class="text-[#006557] font-semibold no-underline hover:text-[#00a38e] transition-colors"
				>
					Política de privacitat
				</Link>
			</footer>

		</main>
	</div>
</template>