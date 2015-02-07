if $yaml_values == undef { $yaml_values = loadyaml('/vagrant/puphpet/config.yaml') }
if $additional_folders == undef { $additional_folders = $yaml_values['additionnal_folders'] }

if $additional_folders and count($additional_folders) > 0{
  each($additional_folders) |$folder|{
    if ! defined(File[$folder]){
      exec{"exec mkdir -p ${folder}":
        command => "mkdir -p ${folder}",
        creates => $folder
      }
      file{$folder:
        ensure => directory,
        owner => vagrant,
        group => vagrant,
        require => Exec["exec mkdir -p ${folder}"]
      }
    }
  }
}