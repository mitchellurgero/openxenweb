# OpenVirtServer Web Interface for Debian/Ubuntu based Xen Hypervisors
Web Interface for Xen, QEMU, KVM, etc using libvirt & libvirt-php!

This web interface has only been tested on Debian & Ubuntu.

## Requirements
- Apache / nginx
- PHP5.4
- 64-bit CPU
- libvirt Version 1.2.12(Specific, downgrade if needed.)

## Installation
These instructions may be incomplete as this project is NOT finished yet!

First we need to install some dependencies(Here we install libvirt-dev packages, make sure it is version 1.2.12!):


    apt-get install libvirt-dev xsltproc libxml2-dev libxml2 sudo git gvncviewer expect bc virt-manager


Once that is done, we will need to compile libvirt-php Version 0.4.8 from the libvirtphp project:


    wget http://libvirt.org/sources/php/libvirt-php-0.4.8.tar.gz
    tar -xvf libvirt-php-0.4.8.tar.gz
    cd libvirt-0.4.8
    ./configure
    make && make install
    

Once that is installed, we need to edit a few files:

In /etc/php5/mods-available/libvirt.ini (Which may not exist yet, if it doesn't, just create it):

    extension=libvirt-php.so

Then close that file. Now run the following command to enable libvirt-php in php5:

    php5enmod libvirt

Then run the following command to give www-data sudo access:

    visudo

And add the following to the bottom of the file:

    www-data ALL=(ALL) NOPASSWD:

This is required for the WebUI to run sudo commands. If you are unsure if you should do this, just make sure that apache is not listening on an internet facing interface. Also run the following command to allow apache libvirt access:

    adduser www-data libvirtd


Now make the following dir:


    mkdir /var/iso


This is where you should be storing ISO's for install, for now, later it can be a configured folder.


If you want VNC console access via the web interface, you must install the bin/socketpolicy.pl file as a service. you may use the following [link to help you do that](http://druss.co/2015/06/run-kestrel-in-the-background/) (I know it is for running asp.net on ubuntu, but how they run a script as a service will fit your needs)

Edit /etc/libvirt/qemu.conf and change the following line:


    vnc_listen='0.0.0.0'


This will allow VM's to listen on any interface for VNC access, but be careful, do not allow your hypervisor to be internet facing!