#!/bin/bash
#Check root:
# Make sure only root can run our script
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi
source installer_functions.sh

## First we check depends....

echo "[INFO] Installing Apache and its dependencies..."
#hide_output apt-get update
apt_install apache2 php5 libapache2-mod-php5 php5-mcrypt php5-curl php5-gd libvirt-dev xsltproc libxml2-dev libxml2 sudo git gvncviewer expect bc php5-dev libvirt-bin

echo "[INFO] Downloading and building libvirt-php v0.4.8..."
hide_output wget http://libvirt.org/sources/php/libvirt-php-0.4.8.tar.gz
echo "[INFO] Extracting and building..."
hide_output tar -xvf libvirt-php-0.4.8.tar.gz
cd libvirt-php-0.4.8
chmod +x configure
./configure && make && make install 
cd ..
hide_output echo "extension=libvirt-php.so" > /etc/php5/mods-available/libvirt.ini

hide_output php5enmod libvirt

echo "[INFO] Adding www-data to libvirtd group..."

hide_output adduser www-data libvirtd

echo "[INFO] Modify libvirt, qemu configuration files..."

hide_output echo vnc_listen = \"0.0.0.0\" >> /etc/libvirt/qemu.conf

echo "[INFO] Adding www-data to suders with NOPASSWD enabled..."

hide_output echo www-data ALL=\(ALL\) NOPASSWD\:ALL >> /etc/sudoers

echo "[INFO] Make new ISO directory..."

hide_output mkdir /var/iso

hide_output chown -R www-data:www-data /var/iso

echo "[INFO] Setting up VNC Socket Policy..."

hide_output cp fvncpolicy /etc/init.d/fvncpolicy

hide_output chmod +x fvncpolicy

hide_output ln -s /etc/init.d/fvncpolicy /etc/rc2.d/S02fvncpolicy

hide_output update-rc.d fvncpolicy enable

hide_output mkdir /var/sockpol

hide_output cp socketpolicy.pl /var/sockpol

hide_output chmod +x /var/sockpol/socketpolicy.pl

hide_output chown -R www-data:www-data /var/sockpol

echo "[INFO] Finished installing dependencies, restarting all services. Please note: You should reboot the server..."

restart_service libvirtd

restart_service apache2

restart_service fvncpolicy

echo "[INFO] Exiting script, last ouput was: SUCCESS. Please enjoy OVS!"

