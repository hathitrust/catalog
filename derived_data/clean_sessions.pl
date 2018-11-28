use strict;
use DBI;
use YAML::XS 'LoadFile';
use FindBin;
use Cwd 'abs_path';

binmode(STDOUT, ':utf8');

my $dir = abs_path($FindBin::Bin);

    
my $y = LoadFile( $dir .  "/../conf/authspecs.yaml");

my $dbdata = $y->{DSessionDB};

my $host = $dbdata->{host};
my $db = $dbdata->{db};
my $user = $dbdata->{username};
my $pass = $dbdata->{password};

my $dsn = "DBI:mysql:database=$db;host=$host";
my $dbh = DBI->connect($dsn,$user,$pass);

my $sth = $dbh->prepare("delete from vfsession where expires < unix_timestamp(NOW())");
my $res = $sth->execute;

print "Deleted " . $sth->rows . " rows.";
