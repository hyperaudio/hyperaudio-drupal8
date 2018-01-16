<?php

namespace Drupal\hyperaudio_player\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

use Symfony\Component\HttpFoundation\Request;
use Masterminds\HTML5;

/**
 * Plugin implementation of the 'hyperaudio_player' formatter.
 *
 * @FieldFormatter(
 *   id = "hyperaudio_player",
 *   label = @Translation("Hyperaudio Player"),
 *   field_types = {
 *     "string_long",
 *     "string",
 *     "computed",
 *     "computed_string",
 *     "computed_string_long",
 *   }
 * )
 */
class HyperaudioPlayerFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      "video" => "0",
      "width" => "400px",
      "height" => "auto",
      "media" => "",
      "transcript" => "",
      "transcript_width" => "100%",
      "transcript_height" => "600px",
    ] + parent::defaultSettings();
  }
  
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['embedded_label'] = [
      '#type' => 'markup',
      '#markup' => '<h3>' . $this->t('Hyperaudio Player') . '</h3>',
    ];
    
    $elements['video'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Video player'),
      '#default_value' => $this->getSetting('video'),
    ];
    
    $elements['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width of media player'),
      '#default_value' => $this->getSetting('width'),
      '#description' => $this->t('You can set sizes in px or percent (ex: 600px or 100%).'),
      '#size' => 10,
    ];
    
    $elements['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height of media player (used only for video)'),
      '#default_value' => $this->getSetting('height'),
      '#description' => $this->t('You can set sizes in px or percent (ex: 600px or 100%).'),
      '#size' => 10,
    ];
    
    $elements['transcript_width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width of transcript container'),
      '#default_value' => $this->getSetting('transcript_width'),
      '#description' => $this->t('You can set sizes in px or percent (ex: 600px or 100%).'),
      '#size' => 10,
    ];
    $elements['transcript_height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height of transcript container'),
      '#default_value' => $this->getSetting('transcript_height'),
      '#description' => $this->t('You can set sizes in px or percent (ex: 600px or 100%).'),
      '#size' => 10,
    ];
    
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $video = $this->getSetting('video');
    if ($video) {
      $summary[] = $this->t('Video: @width x @height', ['@width' => $this->getSetting('width'), '@height' => $this->getSetting('height')]);
    } else {
      $summary[] = $this->t('Audio: @width', ['@width' => $this->getSetting('width')]);  
    }
    
    $summary[] = $this->t('Transcript: @width x @height', ['@width' => $this->getSetting('transcript_width'), '@height' => $this->getSetting('transcript_height')]);

    return $summary;
  }
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $settings = $this->getSettings();

    $video = (int) $settings['video'] ? TRUE : FALSE;
    
    foreach ($items as $delta => $item) {
      $data = json_decode($item->value, TRUE);
      
      $element[$delta] = [
        '#theme' => 'hyperaudio_player_output',
        '#video' => $video,
        '#width' => ['#plain_text' => $settings['width']],
        '#height' => ['#plain_text' => $settings['height']],
        '#transcript_width' => ['#plain_text' => $settings['transcript_width']],
        '#transcript_height' => ['#plain_text' => $settings['transcript_height']],
        '#media' => $data['media']['url'],
        '#poster' => $data['poster']['url'],
        '#transcript' => $this->renderTranscript($data)
      ];
    }
    return $element;
  }

  public function renderTranscript($transcript) {
    $media = $transcript['media']['url'];
    $media_width = $transcript['media']['width'];
    $media_height = $transcript['media']['height'];
    $title = $transcript['title'];
    $poster = $transcript['poster']['url'];
    $poster_width = $transcript['poster']['width'];
    $poster_height = $transcript['poster']['height'];
    $desc = "";
    $current_uri = \Drupal::request()->getUri();
    $t = \Drupal::request()->query->get('t');

    $start = 0;
    $end = 20;
    if ($t !== '') {
      $tt = explode(',', $t);
      if (isset($tt[0])) $start = $tt[0];
      if (isset($tt[1])) $end = $tt[1];
    }
    foreach ($transcript['content']['words'] as $wordData) {
      if (! isset($wordData['start'])) continue;
      if ($wordData['start'] < $start || $wordData['start'] >= $end) {
        continue;
      }
      $desc .= $wordData['text'] . ' ';
    }

    $tempstore = \Drupal::service('user.private_tempstore')->get('hyperaudio_player');
    $tempstore->set('media', $media);
    $tempstore->set('media_width', $media_width);
    $tempstore->set('media_height', $media_height);
    $tempstore->set('poster', $poster);
    $tempstore->set('poster_width', $poster_width);
    $tempstore->set('poster_height', $poster_height);
    $tempstore->set('title', $title);
    $tempstore->set('desc', $desc);
    $tempstore->set('uri', $current_uri);
    
    $html5 = new HTML5();
    $doc = $html5->loadHTML('<html></html>');
    $article = $doc->createElement('article');
    $doc->documentElement->appendChild($article);
    $section = $doc->createElement('section');
    $article->appendChild($section);

    foreach ($transcript['content']['paragraphs'] as $paraData) {
      $para = $doc->createElement('p');

      foreach ($transcript['content']['words'] as $wordData) {
        if (! isset($wordData['start'])) continue; // skip non-timed words

        if ($wordData['start'] < $paraData['start'] || $wordData['start'] >= $paraData['end']) continue;

        $word = $doc->createElement('a');

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

    return $html;
  }

}
