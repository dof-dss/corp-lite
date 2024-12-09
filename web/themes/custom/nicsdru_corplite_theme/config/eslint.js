const { ESLint } = require('eslint');
const fs = require('fs');
const path = require('path');

// Input directory containing JS files to lint
const inputDir = 'web/themes/custom/nicsdru_corplite_theme/src/images';

// Lint multiple files using ESLint
async function lintFiles() {
  // Initialize ESLint
  const eslint = new ESLint();

  // Read all files from the input directory
  fs.readdir(inputDir, async (err, files) => {
    if (err) {
      console.error('Error reading directory:', err);
      return;
    }

    // Filter for JavaScript files only
    const jsFiles = files.filter(file => path.extname(file) === '.js');

    // Create file paths for each JS file
    const filePaths = jsFiles.map(file => path.join(inputDir, file));

    try {
      // Lint the JS files
      const results = await eslint.lintFiles(filePaths);

      // Format the results and print to the console
      const formatter = await eslint.loadFormatter('stylish');
      const resultText = formatter.format(results);

      console.log(resultText);

      // Optionally, fix issues automatically if the "fix" flag is set
      const fixResults = await eslint.lintFiles(filePaths);
      await ESLint.outputFixes(fixResults);
    } catch (err) {
      console.error('Error linting files:', err);
    }
  });
}

// Run the lintFiles function
lintFiles();
