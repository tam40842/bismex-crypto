module.exports = {
	mode: 'jit',
	purge: [
		'src/*.vue',
		'src/**/*.vue',
		'src/**/**/*.vue',
		'./public/**/*.html',
		'./src/**/*.{js,jsx,ts,tsx,vue}',
	],

	darkMode: false, // or 'media' or 'class'
	important: true,
	prefix: 't-',
	theme: {
		extend: {
			colors: {
				custom: {
					// Bitsmex
					EFEFEF: '#EFEFEF',
					FFE600: '#EFEFEF',
					EBA900: '#EBA900',
					'1E4968': '#1E4968',
					'49D3FF': '#49D3FF',
					'02C076': '#02C076',
					CF304A: '#CF304A',
					'303030': '#303030',
					EBA900: '#EBA900',
					'425F99': '#425F99',
					'07131C': '#07131C',
					'03A593': '#03A593',

					// old
					'8ADDCE': '#8ADDCE',
					'0E2F4A': '#0E2F4A',
					'2BB99F': '#2BB99F',
					'01C176': '#01C176',
					E2E2E2: '#E2E2E2',
					'144874': '#144874',
					'062036': '#062036',
					FB5F60: '#FB5F60',
					RGBSELL: '#FE0150',
					RGBBUY: '#01C36D',
					'4F4F5028': '#4F4F5028',
				},
			},
		},
	},
	variants: {
		extend: {},
	},
	plugins: [],
}
