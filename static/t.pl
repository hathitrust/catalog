use LWP::Simple;
use strict;

my %lc;
foreach my $line (split("\n", get('https://catalog.hathitrust.org/static/callnoletters.txt'))) {
    my ($letters, $count) = split("\t", $line);
    $lc{$letters} = $count;
}

foreach my $i (sort keys %lc) {
    my $c = $lc{$i};
    print "$i\t$c\n";
}
