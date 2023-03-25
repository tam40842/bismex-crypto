<template>
	<v-footer color="#101F2D" class="t-py-3">
		<v-container
			class="xl:t-w-10/12 t-mx-auto t-flex xl:t-flex-row t-flex-col xl:t-justify-between t-items-center"
		>
			<v-btn text to="/" v-if="$route.name !== 'index'">
				<img :src="$image.logo" />
			</v-btn>
			<v-btn text v-else>
				<v-img
					@click="$vuetify.goTo('#introl')"
					:src="$image.logo"
					height="64px"
					contain
				></v-img>
			</v-btn>
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
				<v-list-item to="/register">
					<v-list-item-content class="t-text-custom-425F99"
						>Sign Up</v-list-item-content
					>
				</v-list-item>
				<v-list-item>
					<v-btn rounded color="#03A593" class="white--text" to="/login"
						>Sign In</v-btn
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
				<v-list-item to="/register">
					<v-list-item-content class="t-text-custom-425F99"
						>Sign Up</v-list-item-content
					>
				</v-list-item>
				<v-list-item>
					<v-btn rounded color="#03A593" class="white--text" to="/login"
						>Sign In</v-btn
					>
				</v-list-item>
			</v-list>
		</v-container>
	</v-footer>
</template>

<script>
export default {};
</script>
