import Fascio from "fascio";

const copyAssets = () => {
    return new Promise((resolve, reject) => {

        // Start a timer
        console.time("⏱️  Copied Assets in");

        Fascio.copy("src/assets");

        // Stop the timer
        console.timeEnd("⏱️  Copied Assets in");

        resolve();

    });
}

export default copyAssets;