<template>
	<div class="xl:t-block t-hidden">
		<v-list
			class="t-grid lg:t-grid-flow-col t-grid-flow-row t-auto-col-fr t-auto-row-fr t-gap-1"
			color="transparent"
			v-if="$route.name === 'index'"
		>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="$vuetify.goTo('#about')"
			>
				<v-list-item-content class="t-text-custom-425F99"
					>About</v-list-item-content
				>
			</v-list-item>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="$vuetify.goTo('#start')"
			>
				<v-list-item-content class="t-text-custom-425F99"
					>Get Start</v-list-item-content
				>
			</v-list-item>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="$vuetify.goTo('#profit')"
			>
				<v-list-item-content class="t-text-custom-425F99"
					>Profit</v-list-item-content
				>
			</v-list-item>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="$vuetify.goTo('#adventages')"
			>
				<v-list-item-content class="t-text-custom-425F99"
					>Adventages</v-list-item-content
				>
			</v-list-item>
			<v-list-item
				class="t-cursor-pointer"
				@click.native="$vuetify.goTo('#features')"
			>
				<v-list-item-content class="t-text-custom-425F99"
					>Features</v-list-item-content
				>
			</v-list-item>
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
		<v-list
			class="t-grid lg:t-grid-flow-col t-grid-flow-row t-auto-col-fr t-auto-row-fr t-gap-1"
			color="transparent"
			v-else
		>
			<v-list-item to="/">
				<v-list-item-content class="t-text-custom-425F99"
					>About</v-list-item-content
				>
			</v-list-item>
			<v-list-item to="/">
				<v-list-item-content class="t-text-custom-425F99"
					>Get Start</v-list-item-content
				>
			</v-list-item>
			<v-list-item to="/">
				<v-list-item-content class="t-text-custom-425F99"
					>Profit</v-list-item-content
				>
			</v-list-item>
			<v-list-item to="/">
				<v-list-item-content class="t-text-custom-425F99"
					>Adventages</v-list-item-content
				>
			</v-list-item>
			<v-list-item to="/">
				<v-list-item-content class="t-text-custom-425F99"
					>Features</v-list-item-content
				>
			</v-list-item>
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
	</div>
</template>

<script>
export default {};
</script>

<style></style>
