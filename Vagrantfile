# -*- mode: ruby -*-
# vi: set ft=ruby :

hostname = "afup.dev"
virtualbox_ip = "192.168.42.42"

VAGRANTFILE_API_VERSION = "2"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    # Hostmanager default config
    # Using the hostmanager plugin. If missing, install it:
    #   vagrant plugin install vagrant-hostmanager
    config.hostmanager.enabled            = true
    config.hostmanager.manage_host        = true
    config.hostmanager.ignore_private_ip  = false
    config.hostmanager.include_offline    = true

    # AFUP's machine
    config.vm.define hostname do |web|

        # Which box?
        web.vm.box = "boxcutter/debian82"
        web.vm.box_url = "boxcutter/debian82"

        # VMWare Fusion customization
        web.vm.provider :vmware_fusion do |vmware, override|

            # Customize VM
            vmware.vmx["memsize"] = "1024"
            vmware.vmx["numvcpus"] = "1"

        end

        # Virtualbox customization
        web.vm.provider :virtualbox do |virtualbox, override|

            # Customize VM
            virtualbox.customize ["modifyvm", :id, "--memory", "1024", "--cpus", "1", "--pae", "on", "--hwvirtex", "on", "--ioapic", "on"]

            # Network
            override.vm.network :private_network, ip: virtualbox_ip

        end

        # Network
        web.vm.hostname                    = hostname

        # Synced folders
        # On OSX we use some tips to boost the nfs ;)
        if (/darwin/ =~ RUBY_PLATFORM)
            web.vm.synced_folder "", "/vagrant", nfs: true,
                mount_options: ["nolock", "async"],
                bsd__nfs_options: ["alldirs","async","nolock"]
            web.vm.synced_folder "", "/var/www/afup", nfs: true,
                mount_options: ["nolock", "async"],
                bsd__nfs_options: ["alldirs","async","nolock"]
        else
            web.vm.synced_folder "", "/vagrant", nfs: true,
                mount_options: ["nolock", "async"]
            web.vm.synced_folder "", "/var/www/afup", nfs: true,
                mount_options: ["nolock", "async"]
        end

        # Hostmanager
        web.vm.provision :hostmanager

        # Provision with Chef-Solo
        web.vm.provision :chef_solo do |chef|
            chef.version = "12.10.40"
            chef.cookbooks_path = "provisionning/chef/cookbooks"
            chef.add_recipe("afup")

            # Parameters
            chef.json = {
                :global => {
                    :domain => hostname,
                },
                :mysql => {
                    :user => "afup_dev",
                    :password => "p455w0rd",
                    :root_password => "mysql",
                    :db_name => "afup_dev",
                },
            }
        end

    end

end

