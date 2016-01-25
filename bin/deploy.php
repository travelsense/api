#!/usr/bin/env php
<?php

$deploy = new Deployer();
$deploy->run($argv);

class Deployer
{
    const COMMANDS = ['build', 'deploy'];

    public function run(array $args = null)
    {
        if (count($args) < 2 || ! in_array($cmd = $args[1], self::COMMANDS)) {
            $this->terminate("Available commands: " . implode(", ", self::COMMANDS));
            die(1);
        }
        call_user_func_array([$this, $cmd], array_slice($args, 2));
    }

    public function build($tag = 'master')
    {
        $build = date('YmdHis').'-'.$tag;
        $archive = "/tmp/$build.tar.gz";
        $this->exec('git fetch');
        $this->exec("rm -rf /tmp/build");
        $this->exec("git archive --format=tar --prefix=build/ $tag | (cd /tmp/ && tar xf -)");
        chdir("/tmp/build");
        $this->exec("composer install --no-dev");
        $release = sprintf("%s by %s@%s (php %s)", $build, get_current_user(), gethostname(), phpversion());
        $this->exec("echo '$release' > RELEASE");
        $this->exec("tar -zcvf $archive .");
        chdir("/tmp");
        $this->exec("rm -rf /tmp/build");
        echo "\n*********************************************************\n\n";
        echo "DONE: $archive\n\n";
        echo "TO DEPLOY RUN: sudo tar -zxvf $archive -C /www/release/$build\n\n";
        echo "TO SWITCH RUN: sudo ln -sf /www/release/$build /www/current\n\n";
    }

    public function deploy(array $args)
    {

    }

    private function exec($cmd)
    {
        exec($cmd, $out, $ret);
        $this->dumpOutput($out);
        if ($ret != 0) {
            $this->terminate("ERROR: Command returned $ret");
        }
    }

    private function dumpOutput(array $out)
    {
        echo implode("\n", $out)."\n";
    }

    private function terminate($msg, $code = 1)
    {
        echo $msg . "\n";
        die($code);
    }
}