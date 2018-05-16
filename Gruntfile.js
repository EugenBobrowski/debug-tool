module.exports = function (grunt) {
    grunt.initConfig({
        // Gets the package vars.
        pkg: grunt.file.readJSON( 'package.json' ),
        less: {
            development: {
                options: {
                    compress: true,
                    yuicompress: true,
                    optimization: 2
                },
                files: {
                    "css/style.css": "css/style.less",
                }
            }
        },
        //scp -P22053 simple-review-post.zip dev3:/home/eugen/www/update.dev3.gringo.qix.sx/www/packages/
        compress: {
            main: {
                options: {
                    archive: 'debug-tool.zip'
                },
                files: [
                    {expand: true, src: ['**', '!node_modules/**', '!debug-tool.zip'], dest: '/debug-tool/'}
                ]
            }
        },

        // Create README.md for GitHub.
        wp_readme_to_markdown: {
            options: {
                screenshot_url: 'http://ps.w.org/<%= pkg.name %>/assets/{screenshot}.png'
            },
            dest: {
                files: {
                    'README.md': 'readme.txt'
                }
            }
        },
        watch: {
            styles: {
                files: ['**/*.less'], // which files to watch
                tasks: ['less'],
                options: {
                    nospawn: true
                }
            }
        }
    });
    // grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
    grunt.registerTask('default', ['watch']);
};