import Fascio from "fascio";

import { pluralize } from "./utils.js";

const compileSCSS = () => {
	return new Promise((resolve, reject) => {
		// Start a timer
		console.time("⏱️  Compiled SCSS in");

		Fascio.scss("src/sass/global.scss", { dest: "dist/css", glob: true, minify: true });

		// Other Global SCSS
		const globalSCSS = glob.sync("src/css/*.scss", { ignore: "src/css/global.scss" });
		globalSCSS.forEach((file) => {
			Fascio.scss(file, { dest: "dist/css", glob: true, minify: true });
		});

		/**
		 * Bundle Component CSS files
		 */
		const componentSCSS = glob.sync("views/components/**/style.scss");
		componentSCSS.forEach((file) => {
			let nameParts = file.split("/");
			delete nameParts[0];
			delete nameParts[1];
			nameParts.pop(); // Remove the filename
			let componentName = nameParts.filter(Boolean).join("/");
			Fascio.scss(file, { minify: true, dest: `dist/components/${componentName}` });
		});

		if ( componentSCSS?.length ) {
			console.log(`🎁 Compiled SCSS for ${componentSCSS.length} ${pluralize('component', componentSCSS.length)}`);
		}

		/**
		 * Bundle Block CSS files
		 */
		const blockSCSS = glob.sync("views/blocks/**/style.scss");
		blockSCSS.forEach((file) => {
			let blockName = file.split("/")[2];
			Fascio.scss(file, { minify: true, dest: `dist/blocks/${blockName}` });
		});

		if ( blockSCSS?.length ) {
			console.log(`🧩 Compiled SCSS for ${blockSCSS.length} ${pluralize('block', blockSCSS.length)}`);
		}

		// Stop the timer
		console.timeEnd("⏱️  Compiled SCSS in");

		resolve();
	});
}

export default compileSCSS;