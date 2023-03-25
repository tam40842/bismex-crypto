<template></template>

<script>
	import Vue from "vue";
	export default {
		beforeRouteEnter(to, from, next) {
			next((vm) => {
				let accept = vm.$store.state.shareholder.accept;
				if (accept) vm.$router.push("/shareholder/overview");
				else vm.$router.push("/shareholder/join");
			});
			//
		},
	};
</script>

<style>
</style>