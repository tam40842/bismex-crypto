import fs from "fs";
import path from "path";
import i18n from "./i18n/i18n";

const BASE_API_URL = process.env.API_URL;
const BASE_URL = process.env.APP_URL;
const BASE_STREAM = process.env.STREAM_URL;
const PUSHER_APP_KEY = process.env.PUSHER_APP_KEY;
const APP_PREFIX = process.env.APP_PREFIX;
const APP_COLOR = process.env.APP_COLOR;
const APP_NAME = process.env.APP_NAME;

export default {
	ssr: true,
	// Global page headers: https://go.nuxtjs.dev/config-head
	publicRuntimeConfig: {
		apiUrl: BASE_API_URL,
		baseUrl: BASE_URL,
		pusherKey: PUSHER_APP_KEY,
		streamHost: BASE_STREAM,
	},
	globalName: APP_PREFIX,
	globals: {
		id: (globalName) => `__${globalName}`,
		nuxt: (globalName) => `$${globalName}`,
		context: (globalName) => `__${globalName.toUpperCase()}__`,
		pluginPrefix: (globalName) => globalName,
		readyCallback: (globalName) => `on${globalName}Ready`,
		loadedCallback: (globalName) => `_on${globalName}Loaded`,
	},
	head: {
		title: APP_NAME,
		meta: [
			{ charset: "utf-8" },
			{
				name: "viewport",
				content:
					"width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0 viewport-fit=cover",
			},
			{
				hid: "description",
				name: "description",
				content:
					"Binary options trading is based on the blockchain platform. The price movement of cryptocurrency will decide the winner.",
			},
			{ rel: "preconnect", href: "https://fonts.gstatic.com" },
			{ rel: "preconnect", href: "https://fonts.googleapis.com" },
			{
				rel: "stylesheet",
				href:
					"https://fonts.googleapis.com/css2?family=Ruda:wght@400;500;600;700;800;900&display=swap",
			},
			{
				rel: "stylesheet",
				href: "https://fonts.googleapis.com/css2?family=Material+Icons",
			},
		],
		link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.png" }],
	},

	// Global CSS: https://go.nuxtjs.dev/config-css
	css: [
		"~/assets/style/style.scss",
		"~/assets/style/animation.css",
		"~/assets/style/bitsmex-codes.css",
		"~/assets/style/bitsmex-embedded.css",
		"~/assets/style/bitsmex-ie7-codes.css",
		"~/assets/style/bitsmex-ie7.css",
		"~/assets/style/bitsmex.css",
	],

	// Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
	plugins: [
		// { src: '~/plugins/component.js' },
		{ src: "~/plugins/eventBus.js" },
		{ src: "~/plugins/assets.js" },
		{ src: "~/plugins/api.js" },
		{ src: "~/plugins/format.js" },
		{ src: "~/plugins/copy.js" },
		{ src: "~/plugins/skeleton_load.js", mode: "client" },
		// { src: '~/plugins/vue2editor.js', mode: 'client' },
		{ src: "~/plugins/socket.js", mode: "client" },
		{ src: "~/plugins/ckeditor.js", mode: "client" },
	],

	// Auto import components: https://go.nuxtjs.dev/config-components
	components: true,

	// Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
	buildModules: [
		// https://go.nuxtjs.dev/tailwindcss
		"@nuxtjs/tailwindcss",
		[
			"@nuxtjs/vuetify",
			{
				breakpoint: {},
				icons: {},
				lang: {},
				rtl: false,
				theme: {
					themes: {
						light: {
							"btn-primary": "#eba900",
							asidebg: "#19232a",
							eba900: "#eba900",
							"19232a": "#19232a",
						},
					},
				},
			},
		],
	],

	// Modules: https://go.nuxtjs.dev/config-modules
	modules: [
		// https://go.nuxtjs.dev/axios
		"@nuxtjs/axios",
		// https://go.nuxtjs.dev/pwa
		"@nuxtjs/pwa",
		"@nuxtjs/auth-next",
		"@nuxtjs/toast",
		"@nuxt/components",
		"@nuxtjs/component-cache",
		// 'nuxt-socket-io',
		[
			"@nuxtjs/i18n",
			{
				strategy: "no_prefix",
				vueI18nLoader: true,
				defaultLocale: "English",
				locales: [
					{
						code: "English",
						name: "English",
					},
					{
						code: "Vietnamese",
						name: "Vietnamese",
					},
					{
						code: "Japanese",
						name: "Japanese",
					},
					{
						code: "Korean",
						name: "Korean",
					},
					{
						code: "Chinese",
						name: "Chinese",
					},
				],
				vueI18n: i18n,
			},
		],
	],

	// Auth
	// auth: {
	//   redirect: {
	//     login: '/login',
	//     logout: '/login',
	//     home: '/trading',
	//     // callback: '/login'
	//   },
	//   strategies: {
	//     local: {
	//       token: {
	//         property: 'access_token',
	//         required: true,
	//         type: 'bearer'
	//       },
	//       user: {
	//         property: 'user',
	//         autoFetch: true
	//       },
	//       endpoints: {
	//         login: { url: 'auth/login', method: 'post' },
	//         logout: { url: 'auth/logout', method: 'post' },
	//         user: {
	//           url: 'profile/user',
	//           method: 'get',
	//           // propertyName: false,
	//           // autoFetch: true,
	//         }
	//       }
	//     }
	//   }
	// },
	auth: {
		redirect: {
			login: "/login",
			logout: "/login",
			home: "/trading",
			// callback: '/login'
		},
		strategies: {
			local: {
				scheme: "refresh",
				provider: "laravel/jwt",
				url: BASE_API_URL + "/api",
				user: {
					property: "user",
				},
				endpoints: {
					login: {
						url: "/auth/login",
					},
					refresh: {
						url: "/auth/refresh",
					},
					logout: {
						url: "/auth/logout",
					},
					user: {
						url: "/profile/user",
					},
				},
				token: {
					property: "access_token",
					maxAge: 60 * 60,
				},
				refreshToken: {
					maxAge: 20160 * 60,
				},
				autoLogout: true,
			},
		},
	},
	// Router Custom
	router: {
		// base:'/dist',
		middleware: ["auth"],
		extendRoutes(routes, resolve) {
			routes.push(
				...[
					{
						name: "login",
						path: "/login",
						component: resolve(__dirname, "pages/auth/login.vue"),
					},
					{
						name: "register",
						path: "/register",
						component: resolve(__dirname, "pages/auth/register.vue"),
					},
					{
						name: "forgot",
						path: "/forgot",
						component: resolve(__dirname, "pages/auth/forgot.vue"),
					},
					{
						name: "secure",
						path: "/secure",
						component: resolve(__dirname, "pages/auth/secure.vue"),
					},
					{
						name: "reset",
						path: "/reset",
						component: resolve(__dirname, "pages/auth/reset.vue"),
					},
					{
						name: "verify",
						path: "/verify",
						component: resolve(__dirname, "pages/auth/verify.vue"),
					},
				],
			);
		},
	},
	// Axios module configuration: https://go.nuxtjs.dev/config-axios
	axios: {
		headers: {
			Accept: "application/json",
			"Content-Type": "application/json",
		},
	},
	toast: {
		position: "top-right",
		duration: 3000,
	},
	// PWA module configuration: https://go.nuxtjs.dev/pwa
	pwa: {
		manifest: {
			lang: "en",
			name: APP_NAME,
			short_name: APP_NAME,
			useWebmanifestExtension: false,
			description: `${APP_NAME} - Binary options trading is based on the blockchain platform. The price movement of cryptocurrency will decide the winner.`,
			background_color: APP_COLOR,
			theme_color: APP_COLOR,
			publicPath: `/${APP_PREFIX}/`,
			start_url: "/",
		},
		icon: {
			// accessibleIcons: false
			// targetDir: '../icons'
			// iconFileName: 'pwa.png'
			source: "~/static/pwa.png",
		},
	},
	// Server Configuration
	server: {
		port: process.env.PORT,
		host: "0.0.0.0",
		// host: 'example.test',
		// https: {
		//   key: fs.readFileSync(path.resolve(__dirname, 'server.key')),
		//   cert: fs.readFileSync(path.resolve(__dirname, 'server.crt'))
		// }
	},

	// Build Configuration: https://go.nuxtjs.dev/config-build
	build: {
		loaders: {
			vue: {
				transformAssetUrls: {
					audio: "src",
				},
			},
		},
		publicPath: `/${APP_PREFIX}/`,
		extractCSS: true,
		analyze: false,
		extend(config, ctx) {
			if (ctx.isDev) {
				config.devtool = ctx.isClient ? "source-map" : "inline-source-map";
			}
			config.module.rules.push({
				test: /\.(ogg|mp3|wav|mpe?g)$/i,
				loader: "file-loader",
				options: {
					name: "[path][name].[ext]",
				},
			});
		},
		html: {
			minify: {
				collapseBooleanAttributes: true,
				decodeEntities: true,
				minifyCSS: true,
				minifyJS: true,
				processConditionalComments: true,
				removeEmptyAttributes: true,
				removeRedundantAttributes: true,
				trimCustomFragments: true,
				useShortDoctype: true,
			},
		},
	},

	optimization: {
		minimize: true,
		minimizer: [
			// terser-webpack-plugin
			// optimize-css-assets-webpack-plugin
		],
		splitChunks: {
			chunks: "all",
			automaticNameDelimiter: ".",
			name: undefined,
			cacheGroups: {},
		},
	},
	// buildDir: APP_PREFIX
};
