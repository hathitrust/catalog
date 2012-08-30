use strict;
use DBI;
binmode(STDOUT, ':utf8');

my $db = 'vufind';
my $user = 'vufind';
$user = 'dueberb';
my $pass = 'villanova';
$pass = 'LlrxdaPa';
my $host = 'mysql-sdr';
my $dsn = "DBI:mysql:database=$db;host=$host";
my $dbh = DBI->connect($dsn,$user,$pass);

my $sth = $dbh->prepare("delete from vfsession where expires < unix_timestamp(NOW())");
$sth->execute;

# Now do the tempresults
# NO TEMPRESULTS FOR HT
#$dbh->do("delete from tempresults where expires <  unix_timestamp(NOW())");
