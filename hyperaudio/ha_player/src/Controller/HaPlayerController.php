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
        '#markup' => 'Hyperaudio Interactive Transcript Player',
        '#suffix' => '</p>',
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

    //db_query("DELETE FROM {cache};");

    $transcriptId = \Drupal::request()->query->get('transcript');

    if ($transcriptId !== null) {

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

    } else {

      $build['myelement'] = array(
        '#theme' => 'ha_player_player',
        '#transcript' => "no transcript found",
        '#media' => "",
        '#title' => "no transcript found",
      );

    }

    // Add our script. It is tiny, but this demonstrates how to add it. We pass
    // our module name followed by the internal library name declared in
    // libraries yml file.
    $build['myelement']['#attached']['library'][] = 'ha_player/ha_player.player';
    // Return the renderable array.
    return $build;


  }

}
