#!/usr/local/bin/perl
use strict;
use JSON::XS;
use LWP::Simple;
use Encode qw(encode_utf8);

my $json = new JSON::XS;
$json->utf8(1);

my ($facet, $include_count, $q) = @ARGV;

binmode STDOUT, ":utf8";

my $select = "http://solr-sdr-catalog:9033/catalog/select";


$q ||= '*:*';


my $url = "$select?q=$q&rows=0&facet=true&facet.mincount=1&facet.limit=-1&facet.field=$facet&wt=json&json.nl=arrarr&indent=1";


my $json = $json->decode(encode_utf8(get($url)));

foreach my $a (@{$json->{facet_counts}{facet_fields}{$facet}}) {
    my $val = $a->[0];
    print $a->[1], "\t" if ($include_count);
    print $a->[0];

    print "\n";
}
