<?php

/**
 * XMPPHP: The PHP XMPP Library
 * Copyright (C) 2008  Nathanael C. Fritz
 * This file is part of SleekXMPP.
 *
 * XMPPHP is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * XMPPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with XMPPHP; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category  xmpphp
 * @package   XMPPHP
 * @author    Nathanael C. Fritz <JID: fritzy@netflint.net>
 * @author    Stephan Wentz <JID: stephan@jabber.wentz.it>
 * @author    Michael Garvin <JID: gar@netflint.net>
 * @copyright 2008 Nathanael C. Fritz
 */

namespace XMPPHP;

/**
 * XMPPHP XMLObject
 *
 * @package   XMPPHP
 * @author    Nathanael C. Fritz <JID: fritzy@netflint.net>
 * @author    Stephan Wentz <JID: stephan@jabber.wentz.it>
 * @author    Michael Garvin <JID: gar@netflint.net>
 * @copyright 2008 Nathanael C. Fritz
 * @version   $Id$
 */
class XMLObj {

  /**
   * Tag name
   *
   * @var string
   */
  public $name;

  /**
   * Namespace
   *
   * @var string
   */
  public $ns;

  /**
   * Attributes
   *
   * @var array
   */
  public $attrs = array();

  /**
   * Subs?
   *
   * @var array
   */
  public $subs = array();

  /**
   * Node data
   *
   * @var string
   */
  public $data = '';

  /**
   * Constructor
   *
   * @param string $name
   * @param string $ns
   * @param array  $attrs
   * @param string $data
   */
  public function __construct($name, $ns = '', $attrs = array(), $data = '') {

    $this->name = strtolower($name);
    $this->ns   = $ns;

    if (is_array($attrs) AND count($attrs)) {

      foreach ($attrs as $key => $value) {
        $this->attrs[strtolower($key)] = $value;
      }
    }

    $this->data = $data;
  }

  /**
   * Dump this XML Object to output.
   *
   * @param integer $depth
   */
  public function printObj($depth = 0) {

    $tabs = str_repeat("\t", $depth);
    printf("%s%s %s %s\n", $tabs, $this->name, $this->ns, $this->data);

    foreach ($this->subs as $sub) {
      $sub->printObj($depth + 1);
    }
  }

  /**
   * Return this XML Object in xml notation
   *
   * @param string $string
   */
  public function toString($string = '') {

    $string .= sprintf('<%s xmlns="%s"', $this->name, $this->ns);

    foreach ($this->attrs as $key => $value) {

      if ($key != 'xmlns') {
        $value   = htmlspecialchars($value);
        $string .= sprintf(' %s="%s"', $key, $value);
      }
    }

    $string .= '>';

    foreach ($this->subs as $sub) {
      $string .= $sub->toString();
    }

    $body    = htmlspecialchars($this->data);
    $string .= sprintf('%s</%s>', $body, $this->name);

    return $string;
  }

  /**
   * Has this XML Object the given sub?
   *
   * @param string $name
   * @return boolean
   */
  public function hasSub($name, $ns = null) {

    foreach ($this->subs as $sub) {

      $condition1 = ($name == '*' OR $sub->name == $name);
      $condition2 = ($ns == null OR $sub->ns == $ns);

      if ($condition1 AND $condition2) {
        return true;
      }
    }

    return false;
  }

  /**
   * Return a sub
   *
   * @param string $name
   * @param string $attrs
   * @param string $ns
   */
  public function sub($name, $attrs = null, $ns = null) {

    // TODO attrs is ignored
    foreach ($this->subs as $sub) {
      if ($sub->name == $name AND ($ns == null OR $sub->ns == $ns)) {
        return $sub;
      }
    }
  }

  // Find and return one or more sub
  public function getSubs($name = '*', $attrs = null, $ns = null, $stop = false) {

    $subs = false;

    foreach ($this->subs as $sub) {

      $condition1 = ($name == '*' OR $sub->name == $name);
      $condition2 = ($ns == null OR $sub->ns == $ns);
      $condition3 = ($attrs == null OR $sub->hasAttrs($attrs));

      if ($condition1 AND $condition2 AND $condition3) {

        $subs[] = $sub;

        // Stop at first ocurrence
        if ($stop) {
          return $subs;
        }
      }
    }

    return $subs;
  }

  public function hasAttrs($attrs) {

    foreach ($attrs as $attr => $value) {
      if ($this->attrs[strtolower($attr)] != $value) {
        return false;
      }
    }

    return true;
  }
}
