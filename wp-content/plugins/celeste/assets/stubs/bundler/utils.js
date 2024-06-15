import { spawn } from 'child_process';
import { promises as fs } from 'fs';
import os from 'os';
import path from 'path';
import { dirname } from 'path';
import { fileURLToPath } from 'url';

/**
 * Pluralize a word based on a count.
 *
 * Super basic, but we only need it for 2 words so :)
 *
 * @param {string} word
 * @param {number} count
 * @returns {string}
 */
const pluralize = (word, count) => {
    return count === 1 ? word : `${word}s`;
}

const clearConsole = async () => {
    return new Promise((resolve, reject) => {
        const clearCommand = os.platform() === 'win32' ? 'cls' : 'clear';
        const child = spawn(clearCommand, {
            stdio: 'inherit',
            shell: true
        });

        child.on('error', (err) => {
            reject(`Error: ${err.message}`);
        });

        child.on('exit', (code, signal) => {
            if (code !== 0) {
                reject(`Process exited with code: ${code}`);
            } else if (signal) {
                reject(`Process killed with signal: ${signal}`);
            } else {
                resolve();
            }
        });
    });
}

const writeToFile = async function(path, content) {
    let contentAsString = content.toString();

    try {
        // Check if the file exists
        await fs.access(path);

        // File exists, append content
        await fs.writeFile(path, contentAsString);
    } catch (error) {
        if (error.code === 'ENOENT') {
            await fs.writeFile(path, contentAsString);
        } else {
            // Some other error occurred
            console.error('An error occurred:', error);
        }
    }
}

export { clearConsole, pluralize, writeToFile };