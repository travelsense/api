# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "debian/jessie64"
  config.vm.network "private_network", ip: "172.16.0.101"
  config.vm.synced_folder ".", "/vagrant", type: "virtualbox"

  config.vm.provider "virtualbox" do |vb|
    vb.name = "API Dev"
    vb.memory = "1024"
  end

  config.vm.provision "shell", inline: <<-SHELL
    echo 'deb http://ftp.debian.org/debian jessie-backports main' > /etc/apt/sources.list.d/backports.list
    sudo apt-get update && apt-get -t jessie-backports install "ansible" -y
    cd /vagrant && ansible-playbook ansible/local.yml
  SHELL
end
