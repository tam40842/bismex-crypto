<template></template>

<script>
	export default {
		beforeRouteEnter(to, from, next) {
			next({ name: "commission-overview" });
		},
	};
</script>

<style>
</style>