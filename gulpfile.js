/**
 * Gulpfile.
 *
 * Gulp with WordPress.
 *
 * Implements:
 *      1. Live reloads browser with BrowserSync.
 *      2. CSS: Sass to CSS conversion, error catching, Autoprefixing, Sourcemaps,
 *         CSS minification, and Merge Media Queries.
 *      3. JS: Concatenates & uglifies Vendor and Custom JS files.
 *      4. Images: Minifies PNG, JPEG, GIF and SVG images.
 *      5. Watches files for changes in CSS or JS.
 *      6. Watches files for changes in PHP.
 *      7. Corrects the line endings.
 *      8. InjectCSS instead of browser page reload.
 *      9. Generates .pot file for i18n and l10n.
 *
 * Based on Ahmad Awais excellent Gulp workflow
 * @version 1.0.3
 */

/**
 * Project Configuration for gulp tasks
 */

// START Editing Project Variables.
// Project related.
var project                 = 'Base_Install'; // Project Name.
var projectURL              = 'dev8'; // Project URL. Could be something like localhost:8888.
var productURL              = './'; // Theme/Plugin URL. Leave it like it is, since our gulpfile.js lives in the root folder.

// Translation related.
var text_domain             = 'WPGULP'; // Your textdomain here.
var destFile                = 'WPGULP.pot'; // Name of the transalation file.
var packageName             = 'WPGULP'; // Package name.
var bugReport               = 'https://www.mikejandreau.net/contact/'; // Where can users report bugs.
var lastTranslator          = 'First Last <your_email@email.com>'; // Last translator Email ID.
var team                    = 'WPTie <your_email@email.com>'; // Team's Email ID.
var translatePath           = './languages' // Where to save the translation files.

// Style related.
var styleSRC                = './assets/sass/style.scss'; // Path to main .scss file.
var styleDestination        = './'; // Path to place the compiled CSS file at the root folder

// JavaScript related.
// var scriptSRC             = './assets/js/vendor/*.js'; // Path to JS folder if you don't care about concat order
var scriptSRC             = [
                              // './assets/js/vendor/jquery-2.2.4.js', // jQuery is optional
                              './assets/js/vendor/class-helpers.js', // Pure JS class toggling
                              './assets/js/vendor/skip-link-focus-fix.js', // WP skip link
                              './assets/js/vendor/prism.js', // syntax highlighter for code blocks (optional, has associated SASS file for styles/themes)
                              './assets/js/custom/*.js' // menu-controls.js, scroll-to-top.js, etc.
                            ]; // Path to JS vendor and custom files in order.
var scriptDestination     = './assets/js/'; // Path to place the compiled JS vendors file.
var scriptFile            = 'scripts'; // Compiled JS file name.

// Images
var imagesSRC               = './assets/img/raw/**/*.{png,jpg,gif,svg}'; // Source folder of unoptimized images
var imagesDestination       = './assets/img/'; // Destination folder of optimized images

// Watch file paths
var styleWatchFiles         = './assets/sass/**/*.scss'; // Path to all *.scss files inside css folder and inside them.
var scriptJSWatchFiles      = ['./assets/js/vendor/*.js', './assets/js/custom/*.js']; // Path to all JS files.
var projectPHPWatchFiles    = './**/*.php'; // Path to all PHP files.

// Browsers you care about for autoprefixing.
// Browserlist https://github.com/ai/browserslist
const AUTOPREFIXER_BROWSERS = [
    'last 2 versions',
    '> 1%',
    'ie >= 9',
    'ie_mob >= 10',
    'ff >= 30',
    'chrome >= 34',
    'safari >= 7',
    'opera >= 23',
    'ios >= 7',
    'android >= 4',
    'bb >= 10'
  ];

// STOP Editing Project Variables.

/**
 * Load gulp plugins and assign them semantic names.
 */
var gulp         = require('gulp'); // Gulp of-course

// CSS related plugins.
var sass         = require('gulp-sass'); // Gulp pluign for Sass compilation.
var minifycss    = require('gulp-uglifycss'); // Minifies CSS files.
var autoprefixer = require('gulp-autoprefixer'); // Autoprefixing magic.
var mmq          = require('gulp-merge-media-queries'); // Combine matching media queries into one media query definition.

// JS related plugins.
var jshint       = require('gulp-jshint'); // Checks JS for errors
var concat       = require('gulp-concat'); // Concatenates JS files
var uglify       = require('gulp-uglify'); // Minifies JS files

// Image realted plugins.
var imagemin     = require('gulp-imagemin'); // Minify PNG, JPEG, GIF and SVG images with imagemin.

// Utility related plugins.
var rename       = require('gulp-rename'); // Renames files E.g. style.css -> style.min.css
var lineec       = require('gulp-line-ending-corrector'); // Consistent Line Endings for non UNIX systems.
var filter       = require('gulp-filter'); // Enables you to work on a subset of the original files by filtering them using globbing.
var sourcemaps   = require('gulp-sourcemaps'); // Maps code in a compressed file
var notify       = require('gulp-notify'); // Sends message notification to you
var browserSync  = require('browser-sync').create(); // Reloads browser and injects CSS. Time-saving synchronised browser testing.
var reload       = browserSync.reload; // For manual browser reload.
var wpPot        = require('gulp-wp-pot'); // For generating the .pot file.
var sort         = require('gulp-sort'); // Recommended to prevent unnecessary changes in pot-file.

/**
 * Task: `browser-sync`.
 *
 * Live Reloads, CSS injections, Localhost tunneling.
 *
 * This task does the following:
 *    1. Sets the project URL
 *    2. Sets inject CSS
 *    3. You may define a custom port
 *    4. You may want to stop the browser from opening automatically
 */
gulp.task( 'browser-sync', function() {
  browserSync.init( {

    // For more options
    // @link http://www.browsersync.io/docs/options/

    // Project URL.
    proxy: projectURL,

    // `true` Automatically open the browser with BrowserSync live server.
    // `false` Stop the browser from automatically opening.
    open: true,

    // Inject CSS changes.
    // Comment it to reload browser for every CSS change.
    injectChanges: true,

    // Use a specific port (instead of the one auto-detected by Browsersync).
    // port: 7000,
    
  } );
});


/**
 * Task: `styles`.
 *
 * Compiles Sass, Autoprefixes it and Minifies CSS.
 *
 * This task does the following:
 *    1. Gets the source scss file
 *    2. Compiles Sass to CSS
 *    3. Writes Sourcemaps for it
 *    4. Autoprefixes it and generates style.css
 *    5. Renames the CSS file with suffix .min.css
 *    6. Minifies the CSS file and generates style.min.css
 *    7. Injects CSS or reloads the browser via browserSync
 */

// compile scss, add vendor prefixes, minify, save to root directory
 gulp.task('styles', function () {
    gulp.src( styleSRC )
    .pipe( sourcemaps.init() )
    .pipe( sass( {
      errLogToConsole: true,
      // outputStyle: 'compact',
      // outputStyle: 'compressed',
      // outputStyle: 'nested',
      outputStyle: 'expanded',
      precision: 10
    } ) )
    .on('error', console.error.bind(console))
    .pipe( sourcemaps.write( { includeContent: false } ) )
    .pipe( sourcemaps.init( { loadMaps: true } ) )
    .pipe( autoprefixer( AUTOPREFIXER_BROWSERS ) )

    .pipe( sourcemaps.write ( styleDestination ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( styleDestination ) )

    .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
    .pipe( mmq( { log: true } ) ) // Merge Media Queries only for .min.css version.

    .pipe( browserSync.stream() ) // Reloads style.css if that is enqueued.

    .pipe( rename( { suffix: '.min' } ) )
    .pipe( minifycss( {
      maxLineLen: 10
    }))
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( styleDestination ) )

    .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
    .pipe( browserSync.stream() )// Reloads style.min.css if that is enqueued.
    .pipe( notify( { message: 'TASK: "styles" Completed! 💯', onLast: true } ) )
 });


 /**
  * Task: `scripts`.
  *
  * Concatenate and uglify vendor and custom JS scripts.
  *
  * This task does the following:
  *     1. Gets the source folder for JS vendor files
  *     2. Concatenates all the files and generates vendors.js
  *     3. Renames the JS file with suffix .min.js
  *     4. Uglifes/Minifies the JS file and generates vendors.min.js
  */
 gulp.task( 'scripts', function() {
  gulp.src( scriptSRC )
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'))
    .pipe( concat( scriptFile + '.js' ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( scriptDestination ) )
    .pipe( rename( {
      basename: scriptFile,
      suffix: '.min'
    }))
    .pipe( uglify() )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( scriptDestination ) )
    .pipe( notify( { message: 'TASK: "scripts" Completed! 💯', onLast: true } ) );
 });


 /**
  * Task: `images`.
  *
  * Minifies PNG, JPEG, GIF and SVG images.
  *
  * This task does the following:
  *     1. Gets the source of images raw folder
  *     2. Minifies PNG, JPEG, GIF and SVG images
  *     3. Generates and saves the optimized images
  *
  * This task will run only once, if you want to run it
  * again, do it with the command `gulp images`.
  */
 gulp.task( 'images', function() {
  gulp.src( imagesSRC )
    .pipe( imagemin( {
          progressive: true,
          optimizationLevel: 3, // 0-7 low-high
          interlaced: true,
          svgoPlugins: [{removeViewBox: false}]
        } ) )
    .pipe(gulp.dest( imagesDestination ))
    .pipe( notify( { message: 'TASK: "images" Completed! 💯', onLast: true } ) );
 });


 /**
  * WP POT Translation File Generator.
  *
  * * This task does the following:
  *     1. Gets the source of all the PHP files
  *     2. Sort files in stream by path or any custom sort comparator
  *     3. Applies wpPot with the variable set at the top of this file
  *     4. Generate a .pot file of i18n that can be used for l10n to build .mo file
  */
 gulp.task( 'translate', function () {
     return gulp.src( projectPHPWatchFiles )
         .pipe(sort())
         .pipe(wpPot( {
             domain        : text_domain,
             destFile      : destFile,
             package       : packageName,
             bugReport     : bugReport,
             lastTranslator: lastTranslator,
             team          : team
         } ))
        .pipe(gulp.dest(translatePath))
        .pipe( notify( { message: 'TASK: "translate" Completed! 💯', onLast: true } ) )

 });


 /**
  * Watch Tasks.
  *
  * Watches for file changes and runs specific tasks.
  */
 gulp.task( 'default', ['styles', 'scripts', 'images', 'browser-sync'], function () {
  gulp.watch( projectPHPWatchFiles, reload ); // Reload on PHP file changes.
  gulp.watch( styleWatchFiles, [ 'styles' ] ); // Reload on SCSS file changes.
  gulp.watch( scriptJSWatchFiles, [ 'scripts', reload ] ); // Reload on scripts file changes.
 });