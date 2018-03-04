Vagrant.configure("2") do |config|
  config.vm.box = "provolution/packer-lemp"
  config.vm.box_version = "1.0.2"

  config.vm.hostname = "polution-map.de"

  config.vm.network :forwarded_port, guest: 80, host: 7778

  # mail
  config.vm.network :forwarded_port, guest: 1080, host: 1081

  config.vm.network :private_network, ip: "192.168.222.123"
  config.vm.synced_folder ".", "/var/www/project", :nfs => true
  config.ssh.forward_agent = true
  config.ssh.insert_key = false
  config.vm.provider "virtualbox" do |v|
      v.memory = 4096
      v.cpus = 2
  end

  #config.vm.provision "shell", privileged:false, path: "vagrant/setup.sh"
end
