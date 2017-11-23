<?php

namespace Drupal\ha_pad\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Html;
use Masterminds\HTML5;

/**
 * Controller for js_example pages.
 *
 * @ingroup js_example
 */
class HaPadController extends ControllerBase {

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
        '#markup' => 'Hyperaudio Interactive Transcript Pad',
        '#suffix' => '</p>',
      ],
    ];

    return $build;
  }

  public function getApiData($method, $id) {
    // Retrieve data from the external API
    try {
      $response = \Drupal::httpClient()->get('https://api.hyperaud.io/' . $method . '/' . $id . '/?format=json', array('headers' => array('Accept' => 'text/plain')));
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


  public function getHaPad(Request $request, $transcriptId, $variant) {
    if ($transcriptId !== null) {
      $transcript = json_decode($this->getApiData('transcripts', $transcriptId), true);

      $media = $transcript['media']['source']['mp4']['url'];
      $title = $transcript['media']['label'];

      $html5 = new HTML5();
      $doc = $html5->loadHTML('<article></article>');
      $article = $doc->documentElement;

      foreach ($transcript['content']['paragraphs'] as $paraData) {
        $para = $doc->createElement('p');

        // create speaker label
        if (isset($paraData['speaker'])) {
          $speaker = $doc->createElement('span');
          $speaker->setAttribute('class', 'speaker');
          $speaker->appendChild($doc->createTextNode($paraData['speaker']));

          $para->appendChild($speaker);
          $article->appendChild($doc->createTextNode(' ')); // extra space past speaker span
        }

        foreach ($transcript['content']['words'] as $wordData) {
          if (! isset($wordData['start'])) continue; // skip non-timed words
          if (isset($wordData['speaker'])) continue; // skip speaker labels

          if ($wordData['start'] < $paraData['start'] || $wordData['start'] >= $paraData['end']) continue;

          $word = $doc->createElement('span');

          $time = $wordData['start'];
          if (isset($wordData['end'])) $time .= ',' . ($wordData['end'] - $wordData['start']);

          $word->setAttribute('data-t', $time);
          $word->appendChild($doc->createTextNode($wordData['text'] . ' '));
          $para->appendChild($word);
        }
        $article->appendChild($para);
      }

      $html = $html5->saveHTML($doc);
    } else {
      $html = "";
      $media = "";
      $title = "no transcript found";
    }

    $build['myelement'] = array(
      '#theme' => 'ha_pad_pad',
      '#transcript' => $html,
      '#media' => $media,
      '#title' => $title,
      '#variant' => $variant,
    );

    // Add our script. It is tiny, but this demonstrates how to add it. We pass
    // our module name followed by the internal library name declared in
    // libraries yml file.
    $build['myelement']['#attached']['library'][] = 'ha_pad/ha_pad.pad';
    // Return the renderable array.
    return $build;
  }

//  function ogProp(array &$page, $name, $value) {
//    $page['#attached']['html_head'][] = [array('#tag' => 'meta', '#attributes' => array(
//      'property' => 'og:' . $name,
//      'content' => $value,
//    )), 'og' . ucfirst($name)];
//  }
//
//  function your_module_page_attachments(array &$page) {
//    $this->ogProp($page, "locale", "en_US");
//    $this->ogProp($page, "title", "Studs Terkel talks with Jane Stedman (1965)");
//    $this->ogProp($page, "type", "video.other");
//    $this->ogProp($page, "description", "Make way for the prize-men, for the Wise Men they are prize-men - double first men of the university! This is the first big entrance in a Gilbert and Sullivan operetta that was never produced in England by D'Oyly Carte, rarely produce.");
//    $this->ogProp($page, "url", "");
//    $this->ogProp($page, "image", "http://via.placeholder.com/600x400");
//    $this->ogProp($page, "video", "https://s3.amazonaws.com/wfmt-studs-terkel/published/14103.mp4");
//    $this->ogProp($page, "video:width", "1828");
//    $this->ogProp($page, "video:height", "1038");
////    $this->ogProp($page, "", "");
//  }


}
