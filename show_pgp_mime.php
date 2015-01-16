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
  public $task = 'mail|settings';
  private $encrypted_part = null;

  function init() {
    $this->add_texts('localization/');

    $rcmail = rcmail::get_instance();
    if ($rcmail->task == 'mail' && $rcmail->config->get('show_pgp_mime', true)) {
      $this->add_hook('message_load', array($this, 'change_message'));
      $this->add_hook('message_body_prefix', array($this, 'message_body_prefix'));
    }
    else if ($rcmail->task == 'settings') {
      $dont_override = $rcmail->config->get('dont_override', array());
      if (!in_array('show_pgp_mime', $dont_override)) {
        $this->add_hook('preferences_list', array($this, 'prefs_table'));
        $this->add_hook('preferences_save', array($this, 'save_prefs'));
      }
      // set default value
      $rcmail->config->set('show_pgp_mime', $rcmail->config->get('show_pgp_mime', true));
    }
  }

  public function change_message($arg) {
    $mail = $arg['object'];
    if (count($mail->parts) == 1 && $mail->parts[0]->realtype == 'multipart/encrypted') {
      // find the encrypted message payload part
      foreach ($mail->mime_parts as $mime_id => $part) {
        if ($part->mimetype == 'application/octet-stream' || !empty($part->filename)) {
          $this->encrypted_part = $mime_id;
          $mail->parts[0]->mime_id = $mime_id;  // get content from this part
          $mail->parts[0]->size = $mail->mime_parts[$mime_id]->size;
          unset($mail->parts[0]->realtype);
          break;
        }
      }
    }
  }

  public function message_body_prefix($arg) {
    if ($this->encrypted_part) {
        $arg['prefix'] = html::p(
          array('class' => 'hint', 'style' => 'margin-bottom:1em'),
          $this->gettext("show_pgp_mime_body_prefix")
        );
    }
    return $arg;
  }

  function prefs_table($args) {
    if ($args['section'] != 'mailview') {
        return $args;
    }
    $rcmail = rcmail::get_instance();
    $enabled = $rcmail->config->get('show_pgp_mime', true);
    $field_id = 'show_pgp_mime';
    $input = new html_checkbox(array('name' => '_'.$field_id, 'id' => $field_id, 'value' => '1'));

    $args['blocks']['advanced']['options']['show_pgp_mime'] = array(
      'title' => $this->gettext('show_pgp_mime_prefs_label'),
      'content' => $input->show($enabled ? '1' : ''),
    );

    return $args;
  }

  function save_prefs($args) {
    if ($args['section'] == 'mailview') {
      $args['prefs']['show_pgp_mime'] = !empty($_POST['_show_pgp_mime']);
    }
    return $args;
  }
}
