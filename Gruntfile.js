module.exports = function (grunt) {
    grunt.initConfig({
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
        // compress: {
        //     main: {
        //         options: {
        //             archive: 'simple-review-post.zip'
        //         },
        //         files: [
        //             {expand: true, src: ['**', '!node_modules/**', '!simple-review-post.zip'], dest: '/simple-review-post/'}
        //         ]
        //     }
        // },
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

    grunt.registerTask('default', ['watch']);
};