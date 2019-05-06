<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'testPull');

// Project repository
set('repository', 'git@github.com/sercit/tgchat.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys 
set('shared_files', []);
set('shared_dirs', []);

// Writable dirs by web server 
set('writable_dirs', []);
set('allow_anonymous_stats', false);

//set('default_stage', 'production');

set('branch', 'master');

set('release_name', function(){
    return date('YmdHis');
});


// Hosts
set('deploy_path', '/var/www/test');
//host('project.com')
//    ->set('deploy_path', '~/{{application}}');
host('simonrusin.site');
//    ->;


// Tasks
task('build',function(){
//    set('deploy_path', '/root/{{application}}');
    invoke('deploy:info');
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:release');
    invoke('deploy:update_code');
    invoke('deploy:shared');
    invoke('deploy:writable');
    invoke('deploy:clear_paths');
    invoke('deploy:symlink');
    invoke('deploy:unlock');
    invoke('cleanup');
    invoke('success');
});

//task('test', function () {
//    run('cd /var/www && touch test.test');
//    writeln('Hello world');
//});
//task('build', function () {
//    run('cd {{release_path}} && build');
//});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

//before('deploy:symlink', 'artisan:migrate');
task('deploy:unlock', function () {
    run("rm -f {{deploy_path}}/.dep/deploy.lock");//always success
});
