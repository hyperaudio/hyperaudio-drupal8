<?php

namespace Drupal\ha_player\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for js_example pages.
 *
 * @ingroup js_example
 */
class HaPlayerController extends ControllerBase {

  /**
   * Example info page.
   *
   * @return array
   *   A renderable array.
   */
  public function info() {
    $build['content'] = [
      'first_line' => [
        '#prefix' => '<p>',
        '#markup' => 'Drupal includes jQuery and jQuery UI.',
        '#suffix' => '</p>',
      ],
      'second_line' => [
        '#prefix' => '<p>',
        '#markup' => 'We have two examples of using these:',
        '#suffix' => '</p>',
      ],
      'examples_list' => [
        '#theme' => 'item_list',
        '#items' => [
          'An accordion-style section reveal effect. This demonstrates calling a jQuery UI function using Drupal&#39;s rendering system.',
          'Sorting according to numeric &#39;weight.&#39; This demonstrates attaching your own JavaScript code to individual page elements using Drupal&#39;s rendering system.',
        ],
        '#type' => 'ol',
      ],
    ];

    return $build;
  }

  public function getApiData($method, $id)
  {
    // Retrieve data from the external API
    //$response= drupal_http_request('http://api.hyperaud.io/v1/' . $method . '/' . $id));

    try {
      $response = \Drupal::httpClient()->get('http://api.hyperaud.io/v1/' . $method . '/' . $id, array('headers' => array('Accept' => 'text/plain')));
      $data = (string) $response->getBody();
      //$data = '{content:"NO DATA"}';
      if (empty($data)) {
        return FALSE;
      } else {
        return $data;
      }
    }
    catch (RequestException $e) {
      echo "returning false via exception";
      return FALSE;
    }
  }


  public function getHaPlayer() {

    $transcriptId = $_GET['transcript'];

    $json = json_decode($this->getApiData('transcripts',$transcriptId));

    $transcript = $json->{'content'};
    $media = $json->{'media'}->{'source'}->{'mp4'}->{'url'};
    $title = $json->{'media'}->{'label'};

    $build['myelement'] = array(
      '#theme' => 'ha_player_player',
      '#transcript' => $transcript,
      '#media' => $media,
      '#title' => $title,
    );
    // Add our script. It is tiny, but this demonstrates how to add it. We pass
    // our module name followed by the internal library name declared in
    // libraries yml file.
    $build['myelement']['#attached']['library'][] = 'ha_player/ha_player.player';
    // Return the renderable array.
    return $build;
  }

}
