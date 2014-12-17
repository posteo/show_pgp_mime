<?php

/*
 * Copyright (c) 2014 The "Show PGP/MIME plugin for Roundcube" Authors
 *
 * Licensed under GNU Affero General Public License v3
 * <https://gnu.org/licenses/agpl>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 *
 * This Plugin makes Roundcube show the encrypted part of
 * pgp/mime-formatted messages as text/plain.
 *
 */

class show_pgp_mime extends rcube_plugin {
  function init() {
    $this->add_texts('localization/');
    $this->add_hook('message_load', array($this, 'change_message'));
  }

  public function change_message($arg) {
    $mail = $arg['object'];
    if (count($mail->parts) == 1 && $mail->parts[0]->realtype == 'multipart/encrypted') {
      $ciphertext = $mail->get_part_content(2);
      $prefix = $this->gettext("show_pgp_mime_body_prefix");
      if (!empty($prefix)) {
        $text = "[$prefix]\n\n$ciphertext";
      } else {
        $text = $ciphertext;
      }
      $mail->parts[0]->body = $text;
      $mail->parts[0]->size = strlen($text);
      unset($mail->parts[0]->realtype);
    }
    return $mail;
  }
}
