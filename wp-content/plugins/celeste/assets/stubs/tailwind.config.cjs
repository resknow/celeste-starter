const _ = require('lodash');
const defaultTheme = require('tailwindcss/defaultTheme');
const plugin = require('tailwindcss/plugin');
const postcss = require('postcss');
const postcssJs = require('postcss-js');
const wp = require('./theme.json');

const colors = {};
const boxShadow = {};
const fontFamily = {};
const fontSize = {};
const spacing = mapCustomTokens(wp.settings.custom.spacing);

wp.settings.color.palette.map((color) => {
	colors[color.slug] = color.color;
});

wp.settings.spacing.spacingSizes.map((space) => {
	spacing[space.slug] = space.size;
});

wp.settings.shadow.presets.map((shadow) => {
	boxShadow[shadow.slug] = shadow.shadow;
});

wp.settings.typography.fontFamilies.map((family) => {
	fontFamily[family.slug] = family.fontFamily;
});

wp.settings.typography.fontSizes.map((size) => {
	fontSize[size.slug] = size.size;
});

function mapCustomTokens(tokens) {
	let mappedTokens = {};
	_.each(tokens, (value, key) => {
		key = key === 'default' ? key.toUpperCase() : key;
		mappedTokens[key] = value;
	});
	return mappedTokens;
}

/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ['./{src,lib,views}/**/**/*.{twig,js,php}'],
	theme: {
		lineHeight: mapCustomTokens(wp.settings.custom.leading),
		letterSpacing: mapCustomTokens(wp.settings.custom.tracking),
		borderRadius: mapCustomTokens(wp.settings.custom.radius),
		fontFamily: fontFamily,
		fontSize: fontSize,
		fontWeight: mapCustomTokens(wp.settings.custom.weight),
		spacing: spacing,
		extend: {
			boxShadow: boxShadow,
			colors: colors,
			maxWidth: mapCustomTokens(wp.settings.custom.maxWidth),
		},
	},
	plugins: [
		plugin(function ({ addUtilities, config, e }) {
			const flowSpaceUtilities = _.map(config('theme.spacing'), (value, key) => {
				return {
					[`.${e(`flow-space-${key}`)} > *`]: {
						'--flow-space': `${value}`,
					},
				};
			});

			addUtilities(flowSpaceUtilities);
		}),
		plugin(function ({ addComponents, config }) {
			let result = '';

			const currentConfig = config();

			const groups = [
				{ key: 'borderRadius', prefix: 'radius' },
				{ key: 'boxShadow', prefix: 'shadow' },
				{ key: 'colors', prefix: 'color' },
				{ key: 'fontFamily', prefix: 'font' },
				{ key: 'fontSize', prefix: 'size' },
				{ key: 'fontWeight', prefix: 'weight' },
				{ key: 'letterSpacing', prefix: 'tracking' },
				{ key: 'lineHeight', prefix: 'leading' },
				{ key: 'spacing', prefix: 'space' },
			];

			groups.forEach(({ key, prefix }) => {
				const group = currentConfig.theme[key];

				if (!group) {
					return;
				}

				Object.keys(group).forEach((key) => {
					let propertyKey = key === 'DEFAULT' ? `--${prefix}` : `--${prefix}-${key.toLowerCase()}`;

					// Handle object values
					if (typeof group[key] === 'object') {
						Object.keys(group[key]).forEach((subKey) => {
							result += `${propertyKey}-${subKey}: ${group[key][subKey]};`;
						});

						return;
					}

					result += `${propertyKey}: ${group[key]};`;
				});
			});

			addComponents({
				':root': postcssJs.objectify(postcss.parse(result)),
			});
		}),
	],
};
