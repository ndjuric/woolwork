# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "geerlingguy/ubuntu1604"

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: "192.168.168.192"

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  config.vm.provider "virtualbox" do |vb|
    vb.memory = "1024"
    vb.customize ["modifyvm", :id, "--cableconnected1", "on"]
  end

  # Configure synced folder
  config.vm.synced_folder "./engine", "/var/www/engine",
    nfs: true

  # Ansible provisioning
  config.vm.provision "ansible" do |ansible|
    #ansible.verbose = "v"
    ansible.playbook = "ansible/playbook.yml"
    ansible.inventory_path = "ansible/servers"
    ansible.limit = "development"
  end
end
