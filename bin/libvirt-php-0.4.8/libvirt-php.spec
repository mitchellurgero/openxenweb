%define		req_libvirt_version 0.6.2

%if 0%{?suse_version} 
%define		php_confdir %{_sysconfdir}/php5/conf.d
%define		php_extdir  %{_libdir}/php5/extensions
%else
%define		php_confdir %{_sysconfdir}/php.d 
%define		php_extdir  %{_libdir}/php/modules
%endif

Name:		libvirt-php
Version:	0.4.8
Release:	3%{?dist}%{?extra_release}
Summary:	PHP language binding for Libvirt

%if 0%{?suse_version}  
Group:		Development/Libraries/PHP
%else
Group:		Development/Libraries
%endif
License:	PHP
URL:		http://libvirt.org/php
Source0:	http://libvirt.org/sources/php/libvirt-php-%{version}.tar.gz
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root

BuildRequires:	php-devel
BuildRequires:	libvirt-devel >= %{req_libvirt_version}
BuildRequires:	libxml2-devel
BuildRequires:	libxslt
%if 0%{?suse_version}  
BuildRequires:	xhtml-dtd
%else
BuildRequires:	xhtml1-dtds
%endif
Requires:	libvirt >= %{req_libvirt_version}
%if 0%{?suse_version}  
Requires:	php5
%else
Requires:	php
%endif

%description
PHP language bindings for Libvirt API. 
For more details see: http://www.libvirt.org/php/

%package -n libvirt-php-doc
Summary:	Document of libvirt-php
Group:		Development/Libraries/PHP
BuildArch:	noarch
Requires:	libvirt-php = %{version}

%description -n libvirt-php-doc
PHP language bindings for Libvirt API. 
For more details see: http://www.libvirt.org/php/ http://www.php.net/

This package contain the document for libvirt-php.

%prep
%setup -q -n libvirt-php-%{version}

%build
%configure
./configure --with-html-dir=%{_datadir}/doc --with-html-subdir=%{name}-%{version}/html --libdir=%{php_extdir}
make %{?_smp_mflags}

%install
rm -rf %{buildroot}
make install DESTDIR=%{buildroot}
chmod +x %{buildroot}%{php_extdir}/libvirt-php.so

%clean
rm -rf %{buildroot}

%files
%defattr(-,root,root,-)
%{php_extdir}/libvirt-php.so
%config(noreplace) %{php_confdir}/libvirt-php.ini

%files -n libvirt-php-doc
%defattr(-,root,root,-)
%doc
%dir %{_datadir}/doc/%{name}-%{version}
%{_datadir}/doc/%{name}-%{version}/html

%changelog
* Mon Aug 22 2011 Michal Novotny <minovotn@redhat.com> - 0.4.4
- Several bugfixes for VNC and updated SPEC file

* Thu Aug 11 2011 Michal Novotny <minovotn@redhat.com> - 0.4.3
- Rebase to 0.4.3 from master branch

* Tue Apr 19 2011 Michal Novotny <minovotn@redhat.com> - 0.4.1-5
- Minor memory leak fixes
- Several bug fixes

* Mon Apr 11 2011 Michal Novotny <minovotn@redhat.com> - 0.4.1-4
- Add new storagepool API functions
- Add optional xPath argument for *_get_xml_desc() functions
- Add new network API functions
- Add new API functions to add/remove disks

* Wed Mar 23 2011 Michal Novotny <minovotn@redhat.com> - 0.4.1-3
- Add connection information function
- Add coredump support
- Add snapshots support
- Improve error reporting for destructors

* Thu Mar 10 2011 Michal Novotny <minovotn@redhat.com> - 0.4.1-2
- Changes done to comply with Fedora package policy

* Tue Feb 8 2011 Michal Novotny <minovotn@redhat.com> - 0.4.1
- Initial commit (from github)
