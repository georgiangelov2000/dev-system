const mix = require('laravel-mix');
const fs = require('fs');


const pathJs = './resources/js';
const pathCss = './resources/sass';


function discover(dir, type) {
    let jsFiles = [];
    fs.readdirSync(dir).forEach(file => {
        let fileName = `${dir}/${file}`;
        if(fs.statSync(fileName).isFile()) {
            if (fileName.endsWith(type)) {
                jsFiles.push(fileName);
            }
        } else {
            jsFiles = jsFiles.concat(discover(fileName, type));
        }
    });
    return jsFiles;
};

discover(pathJs, '.js').forEach(file => {
    mix.js(file, 'public/js')
});

discover(pathCss, '.scss').forEach(file => {
    mix.sass(file, 'public/css')
});
