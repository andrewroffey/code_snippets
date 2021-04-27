#!/usr/bin/perl

use YAML::Tiny qw(LoadFile);

## Load configuration variables from YAML file
sub LoadYaml ($) {
    my $load = LoadFile(shift);
    for my $key (keys %{$load}) {
        if (ref($load->{$key}) eq "HASH") {
            %{$key} = %{$load->{$key}};
        } elsif (ref($load->{$key}) eq "ARRAY") {
            @{$key} = @{$load->{$key}};
        } else {
            $$key = $load->{$key};
        }
    }
}

