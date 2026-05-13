<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login } from '@/routes';
import '../../css/homepage.css';

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

	<div class="home-page">
		<div class="home-orb home-orb--top"></div>
		<div class="home-orb home-orb--bottom"></div>

		<header class="home-header">
			<div class="home-container home-header__inner">
				<div class="home-brand">
					<div class="home-brand__logo">
						<img src="/images/logo1.png" alt="Logo PMF" />
					</div>
					<div class="home-brand__text">
						<p class="home-brand__title">PMF Salut</p>
						<p class="home-brand__subtitle">Programa millora de flux</p>
					</div>
				</div>

				<nav class="home-nav" aria-label="Seccions">
					<a
						v-for="item in navItems"
						:key="item.id"
						:href="`#${item.id}`"
					>
						{{ item.label }}
					</a>
				</nav>

				<Link :href="login()" class="home-login">Area personal</Link>
			</div>

			<div class="home-container home-nav-mobile">
				<a
					v-for="item in navItems"
					:key="item.id"
					:href="`#${item.id}`"
				>
					{{ item.label }}
				</a>
			</div>
		</header>

		<main class="home-container home-main">
			<section id="resum" class="home-summary">
				<div class="home-summary__copy">
					<p class="home-pill">Salut digital</p>
					<h1 class="home-title">
						El teu espai per consultar cites i informes medics
					</h1>
					<p class="home-lead">
						Accedeix i podrás veure les cites que tens programades i els resultats médics registrats.
					</p>
					<div class="home-actions">
						<Link :href="login()" class="home-button">Area personal</Link>
					</div>
					<div class="home-cards">
						<div class="home-card">
							<p class="home-card__label">Cites</p>
							<p class="home-card__value">Agenda amb les properes cites</p>
						</div>
						<div class="home-card">
							<p class="home-card__label">Informes</p>
							<p class="home-card__value">Informes i resultats</p>
						</div>
						<div class="home-card">
							<p class="home-card__label">Suport</p>
							<p class="home-card__value">Cancel·la una cita en qulasevol moment</p>
						</div>
					</div>
				</div>

				<div class="home-summary__visual">
					<div class="summary-rail">
						<div class="summary-rail__inner">
							<figure class="summary-slice slice-1">
								<img src="/images/grup2.jpeg" alt="Equip medic" />
							</figure>
							<figure class="summary-slice slice-2">
								<img src="/images/logo.png" alt="Logo PMF" class="summary-logo" />
							</figure>
							<figure class="summary-slice slice-3">
								<img src="/images/nosaltres1.jpeg" alt="Equip de programadors" />
							</figure>
						</div>
					</div>
				</div>
			</section>

			<section id="serveis" class="home-section">
				<div class="home-section__header">
					<span class="home-section__line"></span>
					<h2 class="home-section__title">Serveis clau per a pacients</h2>
				</div>
				<div class="home-section__grid">
					<article class="home-feature">
						<div class="home-feature__index">01</div>
						<h3 class="home-feature__title">Agenda personal</h3>
						<p class="home-feature__text">
							Consulta totes les cites, canvis d'hora i detalls amb un sol cop d'ull.
						</p>
					</article>
					<article class="home-feature">
						<div class="home-feature__index">02</div>
						<h3 class="home-feature__title">Informes digitals</h3>
						<p class="home-feature__text">
							Descarrega els informes quan vulguis i guarda l'historial al teu espai.
						</p>
					</article>
					<article class="home-feature">
						<div class="home-feature__index">03</div>
						<h3 class="home-feature__title">Seguiment segur</h3>
						<p class="home-feature__text">
							Les teves dades es mantenen protegides amb accés controlat i clar.
						</p>
					</article>
				</div>
			</section>

			<section id="informes" class="home-section">
				<div class="home-section__grid home-section__grid--split">
					<div class="home-informes">
						<h2 class="home-section__title">Tot el teu historial, sense complicacions</h2>
						<p class="home-section__text">
							Revisa resultats, prepara dubtes abans de la visita i comparteix la
							informació quan calgui. Tot pensat per ser clar i ràpid.
						</p>
						<div class="home-cards home-cards--compact">
							<div class="home-card">
								<p class="home-card__label">Avisos clars</p>
								<p class="home-card__value">Notificacions a temps</p>
							</div>
							<div class="home-card">
								<p class="home-card__label">Historial complet</p>
								<p class="home-card__value">Tot centralitzat</p>
							</div>
						</div>
					</div>
					<div class="home-report">
						<div class="home-report__header">
							<p class="home-report__title">Informe de prova</p>
							<span class="home-report__tag">PDF</span>
						</div>
						<p class="home-report__text">
							Diagnosi, recomanacions i seguiment en una sola fitxa.
						</p>
						<div class="home-report__bars">
							<div></div>
							<div></div>
							<div></div>
						</div>
						<div class="home-report__footer">
							<span class="home-report__dot"></span>
							<div>
								<p class="home-report__author">Equip PMF</p>
								<p class="home-report__role">Suport i seguiment</p>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section id="contacte" class="home-section">
				<div class="home-cta">
					<div>
						<h2 class="home-section__title">Preparat per gestionar la teva salut?</h2>
						<p class="home-section__text">
							Accedeix a l'area personal i revisa totes les teves cites i informes.
						</p>
					</div>
					<Link :href="login()" class="home-button">Entrar a area personal</Link>
				</div>
			</section>
		</main>
	</div>
</template>
