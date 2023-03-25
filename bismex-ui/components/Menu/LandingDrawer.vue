<template>
	<v-navigation-drawer
		v-model="open"
		fixed
		temporary
		class="t-z-20"
		color="#03020250"
	>
		<v-list class="t-pt-16 white--text" v-if="$route.name === 'index'">
			<v-list-item
				class="t-cursor-pointer"
				@click.native="[$vuetify.goTo('#about'), , (open = false)]"
			>
				<v-list-item-content class="white--text">About</v-list-item-content>
			</v-list-item>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="[$vuetify.goTo('#start'), , (open = false)]"
			>
				<v-list-item-content class="white--text">Get Start</v-list-item-content>
			</v-list-item>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="[$vuetify.goTo('#profit'), , (open = false)]"
			>
				<v-list-item-content class="white--text">Profit</v-list-item-content>
			</v-list-item>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="[$vuetify.goTo('#adventages'), , (open = false)]"
			>
				<v-list-item-content class="white--text"
					>Adventages</v-list-item-content
				>
			</v-list-item>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="[$vuetify.goTo('#features'), , (open = false)]"
			>
				<v-list-item-content class="white--text">Features</v-list-item-content>
			</v-list-item>
			<hr />
			<v-list-item to="/register" v-if="!$auth.loggedIn">
				<v-list-item-content class="t-text-custom-425F99"
					>Sign Up</v-list-item-content
				>
			</v-list-item>
			<v-list-item v-if="!$auth.loggedIn">
				<v-btn rounded color="#03A593" class="white--text" to="/login"
					>Sign In</v-btn
				>
			</v-list-item>
			<v-list-item v-else>
				<v-btn rounded color="#03A593" class="white--text" to="/trading"
					>Go to trading</v-btn
				>
			</v-list-item>
		</v-list>
		<v-list class="t-pt-16 white--text" v-else>
			<v-list-item to="/">
				<v-list-item-content class="white--text">index</v-list-item-content>
			</v-list-item>
			<v-list-item to="/">
				<v-list-item-content class="white--text">Features</v-list-item-content>
			</v-list-item>
			<v-list-item to="/">
				<v-list-item-content class="white--text"
					>Top Crypto</v-list-item-content
				>
			</v-list-item>
			<v-list-item to="/">
				<v-list-item-content class="white--text">Contact</v-list-item-content>
			</v-list-item>
			<v-list-item to="/">
				<v-list-item-content class="white--text">About</v-list-item-content>
			</v-list-item>
			<hr />
			<v-list-item to="/register" v-if="!$auth.loggedIn">
				<v-list-item-content class="t-text-custom-425F99"
					>Sign Up</v-list-item-content
				>
			</v-list-item>
			<v-list-item v-if="!$auth.loggedIn">
				<v-btn rounded color="#03A593" class="white--text" to="/login"
					>Sign In</v-btn
				>
			</v-list-item>
			<v-list-item v-else>
				<v-btn rounded color="#03A593" class="white--text" to="/trading"
					>Go to trading</v-btn
				>
			</v-list-item>
		</v-list>
	</v-navigation-drawer>
</template>

<script>
export default {
	props: {
		menu: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		open: {
			get() {
				return this.menu;
			},
			set(v) {
				this.close(v);
			},
		},
	},
	methods: {
		close(v) {
			this.$emit("close", v);
		},
	},
};
</script>

<style></style>
