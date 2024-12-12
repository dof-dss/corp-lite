const fs = require('fs');
const path = require('path');
const UglifyJS = require('uglify-js');

// Input and output directories
const inputDir = 'web/themes/custom/nicsdru_corplite_theme/src/js';
const outputDir = 'web/themes/custom/nicsdru_corplite_theme/js';

// Ensure the output directory exists
if (!fs.existsSync(outputDir)){
  fs.mkdirSync(outputDir);
}

// Function to minify JS files
function minifyFiles() {
  fs.readdir(inputDir, (err, files) => {
    if (err) {
      console.error('Error reading directory:', err);
      return;
    }

    files.forEach(file => {
      const filePath = path.join(inputDir, file);

      // Ensure it's a JavaScript file
      if (path.extname(file) === '.js') {
        const fileContent = fs.readFileSync(filePath, 'utf8');

        // Minify the file content
        const minified = UglifyJS.minify(fileContent);

        if (minified.error) {
          console.error(`Error minifying ${file}:`, minified.error);
          return;
        }

        // Write the minified content to the output directory
        const outputFilePath = path.join(outputDir, file);
        fs.writeFileSync(outputFilePath, minified.code, 'utf8');
        console.log(`Successfully minified ${file} to ${outputFilePath}`);
      }
    });
  });
}

// Run the minify function
minifyFiles();
