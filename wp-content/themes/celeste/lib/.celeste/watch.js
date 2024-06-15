import { Watcher } from 'fascio';
import getPort from 'get-port';

import compileJS from './compile-js.js';
import compilePostCSS from './compile-postcss.js';
import copyAssets from './copy-assets.js';
import startServer, { emitter } from './reload.js';

import { clearConsole, writeToFile } from './utils.js';

const compileCSSAndJS = [
    'php', 'twig', 'json', 'css', 'js', 'scss'
];

// Get port number
const port = await getPort({ port: 7354 });
await writeToFile('./lib/.celeste/port.txt', port);

await clearConsole();
startServer(port);

const shouldRun = (path) => {
    switch (true) {
        case path.startsWith('src'):
        case path.startsWith('views'):
        case path.includes('tailwind.config.cjs'):
        case path.includes('theme.json'):
        case path.includes('.php'):
            return true;
        default:
            return false;
    }
}

new Watcher({
    dir: '.',
    callback: async ({ changedFile }) => {

        // Ignore the dist directory to avoid infinite loop
        if (shouldRun(changedFile?.path) === false) {
            return;
        }

        if (compileCSSAndJS.includes(changedFile?.type)) {
            let js = await compileJS();
            let css = await compilePostCSS();

            Promise.all([js, css]).then(() => {
                emitter.emit('reload', [js, css]);
            });
        }

        if (changedFile.path.startsWith('src/assets')) {
            await copyAssets();
            emitter.emit('reload');
        }
    }
});