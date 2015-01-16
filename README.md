# Roundcube webmail plugin to show PGP/MIME encrypted messages

This Plugin makes Roundcube webmail <http://roundcube.net/> show the encrypted
part of pgp/mime-formatted messages as text/plain.

Tools like Mailvelope <https://www.mailvelope.com/> can handle multiparted
messages fine, but only if they can get their hands on it.

To customize the text that prefixes the displayed message-part see localization/


## Requirements

Confirmed to be working with Roundcube v1.0.x and v1.1.x.


## Install

To use the plugin drop the code into Roundcube's plugins-folder and enable it in Roundcube's config. E.g.:

    cd $roundcube/plugins
    git clone git://github.com/posteo/show_pgp_mime
    vim ../config/config.inc.php

The feature will be enabled for all users if the plugin is activated. You can control
the default by setting the following option in Roundcube config:

    // display of encrypted pgp/mime content in mail view
    $config['show_pgp_mime'] = false;

## Contribution

Any contribution is welcome! Feel free to open an issue or do a pull request at
github.com.

