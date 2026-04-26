<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const STATUS = ['correcte', 'error', 'avís', 'informació'] as const;
type Stats = typeof STATUS[number];

type Props = {
	message: string;
	status?: Stats | (string & {});
};

const props = withDefaults(defineProps<Props>(), {
	status: 'informació',
});

const visible = ref(true);
const page = usePage();

const isKnownStatus = (status: string): status is Stats => {
	return (STATUS as readonly string[]).includes(status);
};

const statusForClasses = computed<Stats>(() => {
	return isKnownStatus(props.status) ? props.status : 'correcte';
});

watch(
	() => [props.message, props.status, (page.props.flash as any)],
	(_, __, onCleanup) => {
		visible.value = true;

		const timeoutId = setTimeout(() => {
			visible.value = false;
		}, 5000);

		onCleanup(() => {
			clearTimeout(timeoutId);
		});
	},
	{ immediate: true },
);

const statusClasses = computed(() => {
	const classes: Record<Stats, string> = {
		correcte: 'border-green-200 bg-green-50 text-green-800',
		error: 'border-red-200 bg-red-50 text-red-800',
		'avís': 'border-amber-200 bg-amber-50 text-amber-800',
		'informació': 'border-blue-200 bg-blue-50 text-blue-800',
	};

	return classes[statusForClasses.value];
});

const statusBadgeClasses = computed(() => {
	const classes: Record<Stats, string> = {
		correcte: 'bg-green-100 text-green-800',
		error: 'bg-red-100 text-red-800',
		'avís': 'bg-amber-100 text-amber-800',
		'informació': 'bg-blue-100 text-blue-800',
	};

	return classes[statusForClasses.value];
});
</script>

<template>
	<div
		v-if="visible"
		class="rounded-lg border px-4 py-3 text-sm text-white bg-pmf-primary"
		:class="statusClasses"
		role="status"
		aria-live="polite"
	>
		<div class="flex items-start gap-3">
			<span
				class="rounded px-2 py-0.5 text-xs font-semibold uppercase tracking-wide"
				:class="statusBadgeClasses"
			>
				{{ status }}
			</span>

			<p class="leading-5">
				{{ message }}
			</p>
		</div>
	</div>
</template>