# Upgrade the server
# ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
execute "apt-get update"
execute "apt-get -y upgrade"

# Apache
# ¯¯¯¯¯¯
package "apache2"

# Set the configuration
template "/etc/apache2/sites-available/00-afup.dev" do
    source "virtualhost.erb"
end

# Activate modules and site
execute "a2enmod rewrite expires headers"
execute "a2dissite 000-default"
execute "a2ensite 00-afup.dev"

# PHP
# ¯¯¯
# Use dotdeb
cookbook_file "dotdeb.list" do
  path "/etc/apt/sources.list.d/dotdeb.list"
  action :create_if_missing
end
execute "wget http://www.dotdeb.org/dotdeb.gpg -O /tmp/dotdeb.gpg"
execute "apt-key add /tmp/dotdeb.gpg"
execute "apt-get update"

# Install PHP
package "php5-cli"
package "php5-common"
package "php5-mysqlnd"

# Install and activate the apache2 module
package "libapache2-mod-php5"
execute "a2enmod php5"

# Mariadb
# ¯¯¯¯¯¯¯
cookbook_file "mariadb.list" do
  path "/etc/apt/sources.list.d/mariadb.list"
  action :create_if_missing
end
execute "apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xcbcb082a1bb943db"
execute "apt-get update"
package "mariadb-server"

# Set the root password
execute "setup root password" do
  command "mysqladmin -u root password #{node[:mysql][:root_password]}"
  action :run
  only_if "mysql -u root -e 'show databases;'"
end

# Create the database
execute "create database" do
    command "mysqladmin -uroot -p#{node[:mysql][:root_password]} create #{node[:mysql][:db_name]}"
    action :run
    only_if "test ! `mysql -uroot -p#{node[:mysql][:root_password]} -e 'use #{node[:mysql][:db_name]}' && echo $?`"
end

# Create the user
execute "setup user" do
    command "mysql -uroot -p#{node[:mysql][:root_password]} -e \"GRANT ALL ON *.* TO '#{node[:mysql][:user]}'@'localhost' IDENTIFIED BY '#{node[:mysql][:password]}' WITH GRANT OPTION;\""
    action :run
    only_if "test ! `mysql -u#{node[:mysql][:user]} -p#{node[:mysql][:password]} -e 'use #{node[:mysql][:db_name]}' && echo $?`"
end

# Mailcatcher
# ¯¯¯¯¯¯¯¯¯¯¯
package "libsqlite3-dev"
package "ruby1.9.1-dev"
execute "gem install mailcatcher" do
    command "gem install mailcatcher"
    action :run
    only_if "test ! `gem list mailcatcher -i > /dev/null && echo $?`"
end

# Use as a Service and start on boot
cookbook_file "mailcatcher.initd" do
    path "/etc/init.d/mailcatcher"
    action :create_if_missing
    mode "0755"
end
execute "update-rc.d mailcatcher defaults"

# Restart services
# ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
service "mysql" do
    action :restart
end
service "apache2" do
    action :restart
end
service "mailcatcher" do
    action :restart
end

