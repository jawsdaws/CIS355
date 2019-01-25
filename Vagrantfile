# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "ubuntu/bionic64"
  config.vm.network "public_network"
  config.vm.hostname = "Prog01CRUD" 

  config.vm.provider "virtualbox" do |v|
    v.name = "Prog01-CRUD"
    v.customize ["modifyvm", :id, "--memory", "4096"]
end

  # Create a private network, which allows host-only access to the machine using a specific IP.
  #config.vm.network "private_network", ip: "192.168.1.35"

  # Share an additional folder to the guest VM. The first argument is the path on the host to the actual folder.
  # The second argument is the path on the guest to mount the folder.
  config.vm.synced_folder "./", "/var/www/html"

  # Define the bootstrap file: A (shell) script that runs after first setup of your box (= provisioning)
  config.vm.provision :shell, path: "bootstrap.sh"

end