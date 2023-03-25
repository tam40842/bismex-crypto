<template>
	<v-app id="landing">
		<!-- <BannerInstall /> -->
		<HeaderLanding @open="open" />
		<v-main>
			<Nuxt />
		</v-main>
		<Footer />
		<MenuLandingDrawer :menu="menu" @close="open" />
	</v-app>
</template>

<script>
export default {
	data() {
		return {
			menu: false,
		}
	},
	methods: {
		open(v) {
			this.menu = v
		},
	},
	mounted() {
		this.$i18n.locale = localStorage.getItem('language') || 'English'
	},
}
</script>
<style lang="scss" scoped>
#landing {
	background-color: #102131;
}
</style>
