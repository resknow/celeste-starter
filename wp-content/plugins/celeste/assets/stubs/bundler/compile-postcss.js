import Fascio from "fascio";
import glob from "glob";

import { pluralize } from "./utils.js";

const compilePostCSS = () => {
	return new Promise((resolve, reject) => {
		// Start a timer
		console.time("⏱️  Compiled PostCSS in");

		// Global CSS
		Fascio.postcss('./src/css/global.css', { minify: true, dest: 'dist/css' });

		// Other Global CSS
		const globalCSS = glob.sync("src/css/*.css", { ignore: "src/css/global.css" });
		globalCSS.forEach((file) => {
			Fascio.postcss(file, { minify: true, dest: 'dist/css' });
		});

		/**
		 * Bundle Component CSS files
		 */
		const componentCSS = glob.sync("views/components/**/style.css");
		componentCSS.forEach((file) => {
			let nameParts = file.split("/");
			delete nameParts[0];
			delete nameParts[1];
			nameParts.pop(); // Remove the filename
			let componentName = nameParts.filter(Boolean).join("/");
			Fascio.postcss(file, { minify: true, dest: `dist/components/${componentName}` });
		});

		if ( componentCSS?.length ) {
			console.log(`🎁 Compiled PostCSS for ${componentCSS.length} ${pluralize('component', componentCSS.length)}`);
		}

		/**
		 * Bundle Block CSS files
		 */
		const blockCSS = glob.sync("views/blocks/**/style.css");
		blockCSS.forEach((file) => {
			let blockName = file.split("/")[2];
			Fascio.postcss(file, { minify: true, dest: `dist/blocks/${blockName}` });
		});

		if ( blockCSS?.length ) {
			console.log(`🧩 Compiled PostCSS for ${blockCSS.length} ${pluralize('block', blockCSS.length)}`);
		}

		// Stop the timer
		console.timeEnd("⏱️  Compiled PostCSS in");

		resolve();
	})
}

export default compilePostCSS;