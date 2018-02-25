<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'bigbigads');

// Project repository
set('repository', 'git@git.papamk.com:bigbigads/bigbigads.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);
set('default_stage', 'staging');
set('env_file', '.env.example');

// Hosts

host('120.77.254.230')
    ->user('root')
    ->identityFile('~/.ssh/id_rsa')
    ->stage('production')
    ->set('deploy_path', '/root/src/{{application}}');    
    
host('test')
    ->configFile('./.ssh.config')
    ->stage('staging')
    ->set('deploy_path', '/root/staging/{{application}}');    

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

desc('检查主机环境的nginx, php-fpm, mysql, redis是否正常');
task('check:host', function() {
    writeln('检查nginx, php-fpm, mysql, redis应该处于可用状态');
    // nginx
    $result = intval(run('ps aux | grep nginx | grep worker | wc -l'));
    if ($result <= 1) {
        throw new \Exception('nginx is fail');
    }
    writeln('<info>nginx is ok</info>');

    // php-fpm
    $result = intval(run('ps aux | grep php-fpm | wc -l'));
    if ($result <= 1) {
        throw new \Exception('php-fpm is fail');
    }
    writeln('<info>php-fpm is ok</info>');

    // mysql/mariadb
    $result = intval(run("ps aux | grep '\(mariadb\)\|\(mysqld\)' | wc -l"));
    if ($result <= 1) {
        throw new \Exception('mysql is fail');
    }
    writeln('<info>mysql is ok</info>');

    // redis(如果该主机有安装gitlab会同时出现多个redis，这里主动排除gitlab的干扰
    $result = intval(run("ps aux | grep redis-server | grep  -v 'gitlab' | wc -l"));
    if ($result <= 1) {
        throw new \Exception('redis is fail');
    }
    writeln('<info>redis is ok</info>');

    writeln('检查php的opcache, redis扩展是否打开');
    
    $result = intval(run("php -m | grep -i opcache | wc -l"));
    if ($result < 1) {
        throw new \Exception('php OPcache extension is not opened');
    }

    $result = intval(run("php -m | grep -i redis | wc -l"));
    if ($result < 1) {
        throw new \Exception('php redis extension is not opened');
    }
});

// 检查Docker环境
desc('检查Docker中的nginx, php-fpm, mysql, redis是否有启动');
task('check:docker', function() {
});

task('setup', function() {
    writeln('创建Docker环境');
});

desc('将.env文件传输到目标主机');
task('deploy:env', function() {
    if (!in_array('.env', get('shared_files'))) {
        writeln('.env is not a shared file, nothing to do!!!');
        return;
    }
    $remoteEnvFile = get('deploy_path') . '/shared/.env';
    $remoteEnv = trim(run("cat {$remoteEnvFile}"));
    $host = Task\Context::get()->getHost();
    $config = $host->getConfig();
    $envFile = ".env.{$config->get('stage')}.{$host->getHostname()}";
    writeln("search env file: {$envFile}");
    if (!file_exists($envFile)) {
        writeln("<error>{$envFile} not found, you should make sure host has .env in shared dir</error>");
        return;
    }
    $newEnv = trim(file_get_contents($envFile));
    if ($newEnv != $remoteEnv) {
        upload($envFile, $remoteEnvFile);
        writeln('env changed, override. ' . strlen($newEnv) . ' ' .  strlen($remoteEnv));
    }
});
after('deploy:shared', 'deploy:env');
