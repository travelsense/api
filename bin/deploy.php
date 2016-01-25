#!/usr/bin/env php
<?php
require_once 'cli.php';
$tag = $argc > 1 ? $argv[1] : 'master';
$build = date('YmdHis') . '-' . $tag;
$tmp = '/tmp';
$archive = "$tmp/$build.tar.gz";
$release = sprintf(
        "%s by %s@%s (%s) (php %s)\n",
        $build,
        get_current_user(),
        gethostname(),
        getenv('SSH_CONNECTION'),
        phpversion()
    )
    . "Last commit:\n" . `git log -1` . "\n";
run('git fetch');
run("mkdir $tmp/$build");
run("git archive --format=tar $tag | (cd $tmp/$build && tar xf -)");
chdir("$tmp/$build");
run("composer install --no-dev");
file_put_contents('RELEASE', $release);
chdir($tmp);
run("tar -zcf $archive $build");
run("rm -rf $build");
echo "\n*********************************************************\n\n";
echo "DONE: $archive\n\n";
echo "TO DEPLOY RUN: sudo tar -zxvf $archive -C /www/release/\n\n";
echo "TO SWITCH RUN: sudo ln -sf /www/release/$build /www/current\n\n";
