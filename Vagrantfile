current_dir = File.dirname(__FILE__)
require 'yaml'
conf = YAML.load_file("#{current_dir}/_ansible/vars/dev.yml")

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    # use vagrantcloud.com box
    config.vm.box = "geerlingguy/ubuntu1604"
    config.vm.box_url = "geerlingguy/ubuntu1604"

    config.vm.define :dev_server do |dev_config|

        dev_config.vm.provider :virtualbox do |v|
            v.customize [ "modifyvm", :id, "--memory", "1024" ]
        end

        dev_config.vm.provider "vmware_fusion" do |v|
          v.vmx["memsize"] = "1024"
        end

        dev_config.vm.host_name = "dev-server"

        dev_config.vm.synced_folder "./", "/var/www/#{conf['site_url']}",
            :owner => "vagrant",
            :group => "www-data"

        dev_config.vm.network :private_network, ip: "192.168.56.190"

        dev_config.vm.provision :ansible do |ansible|
            ansible.playbook = "_ansible/dev_all.yml"
            ansible.limit = "dev-server"
            ansible.inventory_path = "_ansible/hosts"
        end

    end

end
