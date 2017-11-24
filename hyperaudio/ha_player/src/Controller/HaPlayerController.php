<?php

namespace Drupal\ha_player\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Masterminds\HTML5;

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

  public function getApiData($method, $id, $format) {
    try {
      $response = \Drupal::httpClient()->get('https://api.hyperaud.io/' . $method . '/' . $id . '/?format=' . $format, array('headers' => array('Accept' => 'text/plain')));
      $data = (string) $response->getBody();
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


  public function getHaPlayer(Request $request, $transcriptId, $variant, $t) {
    ///
//    $t = $query = $request->query->get('t');
    $transcript = json_decode($this->getApiData('transcripts', $transcriptId, 'json'), TRUE);

    $media = $transcript['media']['source']['mp4']['url'];
    $title = $transcript['media']['label'];
    $desc = "";
    $current_uri = $request->getUri();

    if ($t !== '') {
      $tt = explode(',', $t);
      foreach ($transcript['content']['words'] as $wordData) {
        if (! isset($wordData['start'])) continue;
        if ($wordData['start'] < $tt[0] || $wordData['start'] >= $tt[1]) {
          continue;
        }
        $desc .= $wordData['text'] . ' ';
      }
    }

    $tempstore = \Drupal::service('user.private_tempstore')->get('ha_player');
    $tempstore->set('media', $media);
    $tempstore->set('title', $title);
    $tempstore->set('desc', $desc);
    $tempstore->set('uri', $current_uri);
    ///
    $json = TRUE;
    if (! $json) {
          if ($transcriptId !== null) {

            $json = json_decode($this->getApiData('transcripts', $transcriptId, 'html'));

            $transcript = $json->{'content'};
            $media = $json->{'media'}->{'source'}->{'mp4'}->{'url'};
            $title = $json->{'media'}->{'label'};

          } else {

            $transcript = array();
            $media = "";
            $title = "no transcript found";

          }

//        $transcript = "<article><section><p data-tc=\"00:00:00\"><a class=\"speaker\" data-m=\"420\" data-d=\"1\">[Studs Terkel] </a><a data-m=\"420\" data-d=\"330\">\"Make </a><a data-m=\"750\" data-d=\"210\">way </a><a data-m=\"960\" data-d=\"150\">for </a><a data-m=\"1110\" data-d=\"120\">the </a><a data-m=\"1230\" data-d=\"780\">prize-men, </a><a data-m=\"2040\" data-d=\"180\">for </a><a data-m=\"2220\" data-d=\"120\">the </a><a data-m=\"2340\" data-d=\"1920\">Wise </a><a data-m=\"4260\" data-d=\"960\">Men </a><a data-m=\"5220\" data-d=\"480\">they </a><a data-m=\"5700\" data-d=\"240\">are </a><a data-m=\"5940\" data-d=\"120\">prize-men </a><a data-m=\"6060\" data-d=\"60\">- </a></p></section></article>";


      $build['myelement'] = array(
            '#theme' => 'ha_player_player',
            '#transcript' => $transcript,
            '#media' => $media,
            '#title' => $title,
            '#variant' => $variant,
          );

          $build['myelement']['#attached']['library'][] = 'ha_player/ha_player.player';
          return $build;
    }

    // JSON !!!
    if ($transcriptId !== null) {
      $transcript = json_decode($this->getApiData('transcripts', $transcriptId, 'json'), true);

      $media = $transcript['media']['source']['mp4']['url'];
      $title = $transcript['media']['label'];

      $html5 = new HTML5();
      $doc = $html5->loadHTML('<html></html>');
      $article = $doc->createElement('article');
      $doc->documentElement->appendChild($article);
      $section = $doc->createElement('section');
      $article->appendChild($section);

      foreach ($transcript['content']['paragraphs'] as $paraData) {
        $para = $doc->createElement('p');

        // create speaker label
//        if (isset($paraData['speaker'])) {
//          $speaker = $doc->createElement('a');
//          $speaker->setAttribute('class', 'speaker');
//          $speaker->appendChild($doc->createTextNode($paraData['speaker']));
//
//          $speaker->setAttribute('data-m', 0);
//          $speaker->setAttribute('data-d', 0);
//
//          $para->appendChild($speaker);
////          $article->appendChild($doc->createTextNode(' ')); // FIXME extra space past speaker span
//        }

        foreach ($transcript['content']['words'] as $wordData) {
          if (! isset($wordData['start'])) continue; // skip non-timed words
//          if (isset($wordData['speaker'])) continue; // skip speaker labels

          if ($wordData['start'] < $paraData['start'] || $wordData['start'] >= $paraData['end']) continue;

          $word = $doc->createElement('a');

//          $time = $wordData['start'];
//          if (isset($wordData['end'])) $time .= ',' . ($wordData['end'] - $wordData['start']);
//          $word->setAttribute('data-t', $time);
          $word->setAttribute('data-m', 1000 * $wordData['start']);
          if (isset($wordData['end'])) $word->setAttribute('data-d', 1000 * ($wordData['end'] - $wordData['start']));

          $word->appendChild($doc->createTextNode($wordData['text'] . ' '));
          $para->appendChild($word);
        }
        $section->appendChild($para);
      }

      $html = $html5->saveHTML($doc);
      $html = str_replace('<!DOCTYPE html>', '', $html);
      $html = str_replace('<html>', '', $html);
      $html = str_replace('</html>', '', $html);

//      $html = "<article><section><p data-tc=\"00:00:00\"><a class=\"speaker\" data-m=\"420\" data-d=\"1\">[Studs Terkel] </a><a data-m=\"420\" data-d=\"330\">\"Make </a><a data-m=\"750\" data-d=\"210\">way </a><a data-m=\"960\" data-d=\"150\">for </a><a data-m=\"1110\" data-d=\"120\">the </a><a data-m=\"1230\" data-d=\"780\">prize-men, </a><a data-m=\"2040\" data-d=\"180\">for </a><a data-m=\"2220\" data-d=\"120\">the </a><a data-m=\"2340\" data-d=\"1920\">Wise </a><a data-m=\"4260\" data-d=\"960\">Men </a><a data-m=\"5220\" data-d=\"480\">they </a><a data-m=\"5700\" data-d=\"240\">are </a><a data-m=\"5940\" data-d=\"120\">prize-men </a><a data-m=\"6060\" data-d=\"60\">- </a></p></section></article>";

    } else {
      $html = "";
      $media = "";
      $title = "no transcript found";
    }

    $build['myelement'] = array(
      '#theme' => 'ha_player_player',
      '#transcript' => $html,
      '#media' => $media,
      '#title' => $title,
      '#variant' => $variant,
    );

    // Add our script. It is tiny, but this demonstrates how to add it. We pass
    // our module name followed by the internal library name declared in
    // libraries yml file.
//    $build['myelement']['#attached']['library'][] = 'ha_pad/ha_pad.pad';
    $build['myelement']['#attached']['library'][] = 'ha_player/ha_player.player';
    // Return the renderable array.
    return $build;
  }
}
