import fs from 'fs';

import compileJS from './compile-js.js';
import compilePostCSS from './compile-postcss.js';
import copyAssets from './copy-assets.js';

import { clearConsole } from './utils.js';

await clearConsole();

fs.rmSync('dist', { recursive: true });

compileJS();
compilePostCSS();
copyAssets();