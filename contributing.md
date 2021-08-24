# Contributing to WSUWP-Plugin-iDonate

#### Table Of Contents

[Getting Started](#getting-started)
  * [Docker](#docker)
  * [Vagrant](#vagrant)

## Getting Started

### Docker
To develop the plugin using a Docker environment, you'll need to first [install Docker](https://docs.docker.com/docker-for-windows/install/). Once Docker is installed, you'll need to clone the [WSUF-Docker-Wordpress](https://github.com/jdcrain/WSUF-Docker-Wordpress) repository.

In the WSUF-Docker-Wordpress repository, run `docker-compose up -d` to start the WordPress site, which will be accessible at http://localhost:800

To run tests, linters, etc., ssh into the plugin container by running `docker exec -it wsuf-docker-wordpress_plugin_1 /bin/bash`. You can then run `gulp serve` to run the tests and linters on file changes.

To stop the containers, type `exit` while you are in the plugin container and then run `docker-compose down --remove-orphans`

### Vagrant
Make sure you enable Virtualization (VT-x and Vb-x) in BIOS. (For HP, it's under security settings)

Make sure Hyper-V is ___not___ enabled on your machine

Follow the installation  steps here: https://varyingvagrantvagrants.org/docs/en-US/installation/
 - For VirtualBox, use the latest 5.1 version (https://www.virtualbox.org/wiki/Download_Old_Builds_5_1)
    - If you have been getting errors while running vagrant up, try using version 5.1.30 of VirtualBox and 2.0.2 of VVV
	- VirtualBox 5.1.30 with VVV 2.1.0 also works

Run 'vagrant up' from an elevated (Administrator) command prompt

The installer will connect the directories on the VM with the VVV local directories on your machine
  - You can use Dsynchronize  to sync your plugin with the VVV plugin (or just do your dev in the VVV directories)
  - You can also try creating a junction (hard link) from your repo dir to VVV
	- Ex: C:\Users\blair.lierman\Repos\VVV\www\wordpress-default\public_html\wp-content\plugins>mklink /J wsuwp-plugin-idonate C:\Users\blair.lierman\Repos\wsuwp-plugin-idonate
	- Mklink info: https://www.techrepublic.com/article/how-to-take-advantage-of-symbolic-links-in-window-10/

To connect to the new VM, run 'vagrant ssh' from an elevated command prompt
  - If Git is installed and you get an ssh error on the previous step, go to System -> Change settings -> Advanced -> Environment Variables -> Path -> Edit and then add C:\Program Files\Git\usr\bin
    - After doing this, close any command prompts, reopen and try vagrant ssh
  - Password is vagrant if prompted

The default sites can be found here: https://varyingvagrantvagrants.org/docs/en-US/references/default-sites/

To finish the setting up your development environment, follow the steps here: Online Giving Plugin Dev Env. Setup