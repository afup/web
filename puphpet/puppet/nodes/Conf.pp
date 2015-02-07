file{'/var/www/configs/application/config.php':
  ensure => file,
  source => '/var/www/configs/application/config.php.dist',
  owner => 'vagrant',
  group => 'vagrant'
}