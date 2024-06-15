import Fascio from "fascio";
import glob from "glob";

import { pluralize } from "./utils.js";

const compileJS = () => {
	return new Promise((resolve, reject) => {
		// Start a timer
		console.time("⏱️  Compiled Javascript in");

		// Global JS
		Fascio.js("./src/js/global.js", { minify: true, dest: 'dist/js' });

		/**
		 * Copy all non-bundled JS files to the dist folder.
		 */
		const nonBundledJS = glob.sync("src/js/*.js", { ignore: "src/js/main.js" });
		nonBundledJS.forEach((file) => {
			Fascio.js(file, { minify: true });
		});

		/**
		 * Bundle Component JS files
		 */
		const componentJS = glob.sync("views/components/**/main.{js,vue.js,react.js,svelte.js}");
		componentJS.forEach((file) => {
			let componentName = file.split("/")[2];
			let framework = determineFramework(file);

			if (framework === 'js') {
				Fascio.js(file, { minify: true, dest: `dist/components/${componentName}` });
			}

			if (framework === 'vue') {
				Fascio.vue(file, { minify: true, dest: `dist/components/${componentName}` });
			}

			if (framework === 'react') {
				Fascio.react(file, { minify: true, dest: `dist/components/${componentName}` });
			}

			if (framework === 'svelte') {
				Fascio.svelte(file, { minify: true, dest: `dist/components/${componentName}` });
			}
		});

		if ( componentJS?.length ) {
			console.log(`🎁 Compiled JS for ${componentJS.length} ${pluralize('component', componentJS.length)}`);
		}

		/**
		 * Bundle Block JS files
		 */
		const blockJS = glob.sync("views/blocks/**/main.js");
		blockJS.forEach((file) => {
			let blockName = file.split("/")[2];
			Fascio.js(file, { minify: true, dest: `dist/blocks/${blockName}` });
		});

		if ( blockJS?.length ) {
			console.log(`🧩 Compiled JS for ${blockJS.length} ${pluralize('block', blockJS.length)}`);
		}

		// Stop the timer
		console.timeEnd("⏱️  Compiled Javascript in");

		resolve();
	});
}

function determineFramework(fileName) {
	if (fileName.endsWith('.vue.js')) {
		return 'vue';
	}

	if (fileName.endsWith('.react.js')) {
		return 'react';
	}

	if (fileName.endsWith('.svelte.js')) {
		return 'svelte';
	}

	return 'js';
}

export default compileJS;