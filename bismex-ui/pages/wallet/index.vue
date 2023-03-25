<template></template>

<script>
	export default {
		// mounted() {
		//   if(this.$auth.user.admin_setup) {
		//     this.$router.push('wallet/transfer');
		//   }else {
		//     this.$router.push('wallet/deposit');
		//   }
		// }
		beforeRouteEnter(to, from, next) {
			next("/wallet/overview");
		},
	};
</script>

<style>
</style>
