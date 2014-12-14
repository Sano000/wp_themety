module.exports = function(grunt) {


  // Load grunt modules
  require('load-grunt-tasks')(grunt);

  // Filter modified files
  fs = require('fs');
  isModified = function(filepath) {
    var now = new Date;
    var modified =  fs.statSync(filepath).mtime;
    return (now - modified) < 10000
  }


  // Project configuration.
  grunt.initConfig({

    // Load tasks

    pkg: grunt.file.readJSON('package.json'),

    // Check coffee code
    coffeelint: {
      app: [
        'resources/coffee/*.coffee',
        'resources/coffee/**/*.coffee'
      ]
    },

    // Compile coffee scripts
    coffee: {
      build: {
        expand: true,
        cwd: 'resources/coffee',
        src: ['**/*.coffee', '*.coffee'],
        dest: 'source/js',
        ext: '.js',
      },
      compile: {
        expand: true,
        cwd: 'resources/coffee',
        src: ['**/*.coffee', '*.coffee'],
        dest: 'source/js',
        ext: '.js',
        filter: isModified
      }
    },


    phpcs: {
        src: {
            dir: [
              'src/**/*.php'
            ]
        },
        options: {
            standard: 'PSR2',
            ignore: [
              '*.tpl.php'
            ]
        }
    },


    watch: {
      coffee: {
        files : '<%= coffee.compile.src %>',
        tasks : ['coffee:compile']
      }
    }
  });

  // Build
  grunt.registerTask('build', [
    'coffee:build'
  ]);

  // Default task(s).
  grunt.registerTask(
    'default', [
      'watch'
    ]
  );

};
