<?php

namespace Drupal\Tests\hyperaudio\Functional;
use Drupal\Tests\hyperaudio\Functional\HyperaudioBrowserTestBase;
/**
 * Functional tests for the hyperaudio module.
 *
 */

class HyperaudioTest extends HyperaudioBrowserTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('hyperaudio', 'node');

  /**
   * Test all the paths defined by our module.
   */
  public function testHyperaudio() {
    $assert = $this->assertSession();

    $paths = [
      'hyperaudio/pad',
    ];
    foreach ($paths as $path) {
      $this->drupalGet($path);
      $assert->statusCodeEquals(200);
    }
  }
}
