#!/bin/bash	

tar xvf link.tar
unzip google-api-php-client-master.zip
tar zxvf PHPMailer_v*.tar.gz
tar zxvf adodb*.tgz
tar zxvf fpdf*.tgz
tar zxvf twitteroauth.tgz
mv twitteroauth twitteroauth.old
mv twitteroauth.old/twitteroauth .
rm -rf twitteroauth.old
#mkdir jpgraph-3.0.7
#cd jpgraph-3.0.7
#tar zxvf ../jpgraph-3.0.7.tar.gz 
#cd ../;
tar zxvf jpgraph-4.0.0.tar.gz

mkdir dynatree
cd dynatree
tar zxvf ../dynatree-* 
cd ../;


mkdir jquery-ui-1.8.21.custom
cd jquery-ui-1.8.21.custom
unzip ../jquery-ui-1.8.21.custom.zip
cd ../;

unzip phpqrcode-2010100721_1.1.4.zip


#cd tigra/tigra_hints;unzip tigra_hints.zip;cd ../../
#cd tigra/tigra_menu;unzip tigra_menu.zip;cd ../../
#cd tigra/tigra_scroller;unzip tigra_scroller.zip;cd ../../
#cd tigra/tigra_tree_menu;unzip tigra_tree_menu.zip;cd ../../

distro=`cat /etc/issue | awk '{if (NR==1 && NF >0) print $1}'`

if [ "$distro" = "Ubuntu" ];then
  group="www-data"
elif [ "$distro" = "CentOS" ];then
  group="apache"
elif [ "$distro" = "Fedora" ];then
  group="apache"
else
  group="webservd"
fi


sudo chgrp -R $group ./

echo "Configuración OK para ${distro} ${group} "
