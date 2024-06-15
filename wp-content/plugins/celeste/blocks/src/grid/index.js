/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from "@wordpress/blocks";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./style.scss";

/**
 * Internal dependencies
 */
import Edit from "./edit";
import save from "./save";
import metadata from "./block.json";

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,

	/**
	 * Variations
	 */
	variations: [
		{
			name: "two-columns",
			title: "Grid: 2 Columns",
			icon: (
				<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<g>
						<path d="M0 0h24v24H0z" fill="none" />
						<path d="M16 7.5a4 4 0 1 0-8 0H6a6 6 0 1 1 10.663 3.776l-7.32 8.723L18 20v2H6v-1.127l9.064-10.802A3.982 3.982 0 0 0 16 7.5z" />
					</g>
				</svg>
			),
			attributes: {
				columns: 1,
				columnsMd: 2,
				columnsLg: null,
			},
		},
		{
			name: "three-columns",
			title: "Grid: 3 Columns",
			icon: (
				<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<g>
						<path d="M0 0h24v24H0z" fill="none" />
						<path d="M18 2v1.362L12.809 9.55a6.501 6.501 0 1 1-7.116 8.028l1.94-.486A4.502 4.502 0 0 0 16.5 16a4.5 4.5 0 0 0-6.505-4.03l-.228.122-.69-1.207L14.855 4 6.5 4V2H18z" />
					</g>
				</svg>
			),
			attributes: {
				columns: 1,
				columnsMd: 3,
				columnsLg: null,
			},
		},
	],
});
