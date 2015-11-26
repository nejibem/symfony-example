symfony.local
=============

# About

A basic symfony project showing the use of User Authentication with using FOSUserBundle.

# Setup

Install Vagrant (https://www.vagrantup.com/downloads.html)
Install Ansible (http://docs.ansible.com/ansible/intro_installation.html)

Checkout source
cd into root of source
run command `vagrant up`

on your host system (your desktop) add the folowing to /etc/host
`192.168.56.190 symfony.local`

once vagrant finished you should be able to go to http://symfony.local in your browser and login with the credentials:
user: root
pass: pass

If you want to change the ip address for the vm make sure to change it in both `Vagrantfile` and `_ansible/hosts`