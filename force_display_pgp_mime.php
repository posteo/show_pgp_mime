<?php

/*
 * Force Roundcube to display the encrypted part of pgp/mime-formatted messages
 * as text/plain.
 *
 * Copyright (c) 2014 Posteo e.K. See README.md for details.
 */

class force_display_pgp_mime extends rcube_plugin {
  function init() {
    $this->add_texts('localization/');
    $this->add_hook('message_load', array($this, 'change_message'));
  }

  public function change_message($arg) {
    $mail = $arg['object'];
    if (count($mail->parts) == 1 && $mail->parts[0]->realtype == 'multipart/encrypted') {
      $ciphertext = $mail->get_part_content(2);
      $prefix = $this->gettext("force_display_pgp_mime_body_prefix");
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
