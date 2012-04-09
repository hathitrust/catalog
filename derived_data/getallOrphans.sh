HOST=`hostname | perl -ne '@h = split(/[-.]/); print $h[0]'`

if [ $HOST == 'koolaid' ]
then
  DIR=/htapps/dueberb.catalog/web/derived_data
else
  DIR=/htapps/catalog/web/derived_data
fi


#RIGHTS=orphcand
RIGHTS=opb


perl $DIR/gfv.pl language 0 ht_rightscode:$RIGHTS > $DIR/orphans/language.txt
perl $DIR/gfv.pl format 0 ht_rightscode:$RIGHTS > $DIR/orphans/format.txt
php -f  $DIR/getHTNamespaceMap.php > $DIR/orphans/ht_namespaces.php