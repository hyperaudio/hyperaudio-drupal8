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
      "mode" => "1",
      "metadata" => "1",
      "player_class" => "hyperplayer",
      "player_style" => "",
      "player_selector" => ".hyperplayer",
      "media" => "",
      "transcript" => "",
      "transcript_class" => "hypertranscript",
      "transcript_style" => "",
      "transcript_selector" => ".hypertranscript",
      "fb_app_id" => NULL,
      "fb_admins" => NULL,
      "tw_site" => NULL,
      "site_name" => NULL,
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

    $elements['mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Render mode'),
      '#options' => array(t('plain transcript'), t('audio + hypertranscript'), t('video + hypertranscript'), t('external player + hypertranscript')),
      '#default_value' => $this->getSetting('mode'),
    ];

    $elements['player_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class attribute of the media player'),
      '#default_value' => $this->getSetting('player_class'),
      '#size' => 10,
    ];

    $elements['player_selector'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CSS selector to identify the media player'),
      '#default_value' => $this->getSetting('player_selector'),
      '#size' => 10,
    ];

    $elements['player_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Style attribute of the media player'),
      '#default_value' => $this->getSetting('player_style'),
      '#description' => $this->t('Use this for inline styles.'),
      '#size' => 10,
    ];

    $elements['transcript_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class attribute transcript container'),
      '#default_value' => $this->getSetting('transcript_class'),
      '#description' => $this->t('Alter this to accomodate multiple transcripts in the page.'),
      '#size' => 10,
    ];

    $elements['transcript_selector'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CSS selector to identify the transcript container'),
      '#default_value' => $this->getSetting('transcript_selector'),
      '#size' => 10,
    ];

    $elements['transcript_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Style attribute of the transcript container'),
      '#default_value' => $this->getSetting('transcript_style'),
      '#description' => $this->t('Use this for inline styles.'),
      '#size' => 10,
    ];

    $elements['metadata'] = [
      '#type' => 'select',
      '#title' => $this->t('Inject metadata'),
      '#options' => array(t('never'), t('only on links with media fragment (?t=start,stop)'), t('always')),
      '#default_value' => $this->getSetting('metadata'),
    ];

    $elements['fb_app_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook App Id'),
      '#default_value' => $this->getSetting('fb_app_id'),
      '#size' => 10,
    ];

    $elements['fb_admins'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook Admins'),
      '#default_value' => $this->getSetting('fb_admins'),
      '#size' => 10,
    ];

    $elements['tw_site'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Twitter site'),
      '#default_value' => $this->getSetting('tw_site'),
      '#size' => 10,
    ];

    $elements['site_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Open Graph site_name'),
      '#default_value' => $this->getSetting('site_name'),
      '#size' => 10,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    // $player = $this->getSetting('player');
    // $video = $this->getSetting('video');

    // if ($player) {
    //   if ($video) {
    //     $summary[] = $this->t('Video class(@player_class) style(@player_style) selector(@player_selector)', ['@player_class' => $this->getSetting('player_class'), '@player_style' => $this->getSetting('player_style'), '@player_selector' => $this->getSetting('player_selector')]);
    //   } else {
    //     $summary[] = $this->t('Audio class(@player_class) style(@player_style) selector(@player_selector)', ['@player_class' => $this->getSetting('player_class'), '@player_style' => $this->getSetting('player_style'), '@player_selector' => $this->getSetting('player_selector')]);
    //   }
    // } else {
      $summary[] = $this->t('External player selector(@player_selector)', ['@player_selector' => $this->getSetting('player_selector')]);
    // }

    $summary[] = $this->t('Transcript class(@transcript_class) style(@transcript_style) selector(@transcript_selector)', ['@transcript_class' => $this->getSetting('transcript_class'), '@transcript_style' => $this->getSetting('transcript_style'), '@transcript_selector' => $this->getSetting('transcript_selector')]);

    return $summary;
  }
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    \Drupal::service('page_cache_kill_switch')->trigger();

    $element = [];
    $settings = $this->getSettings();

    $t = \Drupal::request()->query->get('t');
    $start = 0;
    $end = 0;
    if ($t != '') {
      $tt = explode(',', $t);
      if (isset($tt[0])) $start = $tt[0];
      if (isset($tt[1])) $end = $tt[1];
    }

    foreach ($items as $delta => $item) {
      $data = json_decode($item->value, TRUE);

      $element[$delta] = [
        '#cache' => ['contexts' => ['url']],
        '#theme' => 'hyperaudio_player_output',
        '#selection' => $start . ',' . $end,
        '#mode' =>  $settings['mode'],
        '#metadata' => $settings['metadata'],
        '#player_class' => ['#plain_text' => $settings['player_class']],
        '#player_style' => ['#plain_text' => $settings['player_style']],
        '#player_selector' => ['#plain_text' => $settings['player_selector']],
        '#transcript_class' => ['#plain_text' => $settings['transcript_class']],
        '#transcript_style' => ['#plain_text' => $settings['transcript_style']],
        '#transcript_selector' => ['#plain_text' => $settings['transcript_selector']],
        '#media' => $data['media']['url'],
        '#poster' => $data['poster']['url'],
        '#transcript' => $this->renderTranscript($data, $settings),
      ];
    }
    return $element;
  }

  public function renderTranscript($transcript, $settings) {
    $mode = $settings['mode'];

    $media = $transcript['media']['url'];
    $media_width = $transcript['media']['width'];
    $media_height = $transcript['media']['height'];
    $title = $transcript['title'];
    $poster = $transcript['poster']['url'];
    $poster_width = $transcript['poster']['width'];
    $poster_height = $transcript['poster']['height'];

    $current_uri = \Drupal::request()->getUri();
    $hash = sha1($current_uri);

    $t = \Drupal::request()->query->get('t');
    $desc = "";
    if (isset($t) && $t != '') {
      $start = 0;
      $end = 0;

      $tt = explode(',', $t);
      if (isset($tt[0])) $start = $tt[0];
      if (isset($tt[1])) $end = $tt[1];

      foreach ($transcript['content']['words'] as $wordData) {
        if (! isset($wordData['start'])) continue;
        if ($wordData['start'] < $start || $wordData['start'] >= $end) {
          continue;
        }
        $desc .= $wordData['text'] . ' ';
      }
    } else {
      $wordCount = 0;
      foreach ($transcript['content']['words'] as $wordData) {
        if (! isset($wordData['start'])) continue;
        $wordCount++;
        $desc .= $wordData['text'] . ' ';
        if ($wordCount > 50) break;
      }
    }

    $tempstore = \Drupal::service('user.private_tempstore')->get('hyperaudio_player~' . $hash);
    $tempstore->set('metadata', $settings['metadata']);
    $tempstore->set('media', $media);
    $tempstore->set('media_width', $media_width);
    $tempstore->set('media_height', $media_height);
    $tempstore->set('poster', $poster);
    $tempstore->set('poster_width', $poster_width);
    $tempstore->set('poster_height', $poster_height);
    $tempstore->set('title', $title);
    $tempstore->set('desc', $desc);

    $tempstore->set('fb_app_id', $settings['fb_app_id']);
    $tempstore->set('fb_admins', $settings['fb_admins']);
    $tempstore->set('tw_site', $settings['tw_site']);
    $tempstore->set('site_name', $settings['site_name']);

    $html5 = new HTML5();
    $doc = $html5->loadHTML('<html></html>');
    $article = $doc->createElement('article');
    $doc->documentElement->appendChild($article);
    $section = $doc->createElement('section');
    $article->appendChild($section);

    foreach ($transcript['content']['paragraphs'] as $paraData) {
      $para = $doc->createElement('p');

      // create speaker label
      if (isset($paraData['speaker'])) {
        $speaker = $doc->createElement('span');
        $speaker->setAttribute('class', 'speaker');
        $speaker->appendChild($doc->createTextNode($paraData['speaker']));

        $para->appendChild($speaker);
        $para->appendChild($doc->createTextNode(' ')); // extra space past speaker element
       }

       if (isset($paraData['start'])) {
         $para->setAttribute('data-tc', gmdate("H:i:s", $paraData['start']));
       }

      foreach ($transcript['content']['words'] as $wordData) {
        if (! isset($wordData['start'])) continue; // skip non-timed words
        if ($wordData['start'] < $paraData['start'] || $wordData['start'] >= $paraData['end']) continue;

        $text = $doc->createTextNode($wordData['text'] . ' ');

        if ($mode != 0) {
          $word = $doc->createElement('a');
          $word->setAttribute('data-m', 1000 * $wordData['start']);
          if (isset($wordData['end'])) $word->setAttribute('data-d', 1000 * ($wordData['end'] - $wordData['start']));

          $word->appendChild($text);
          $para->appendChild($word);
        } else {
          $para->appendChild($text);
        }
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
