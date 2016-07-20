<?php

namespace Drupal\ymca_mindbody\Controller;

use Drupal\mindbody\MindbodyException;
use Drupal\ymca_mindbody\Form\MindbodyPTForm;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for "Mindbody results" page.
 */
class MindbodyResultsController extends ControllerBase {

  /**
   * Set page content.
   */
  public function content() {
    $query = \Drupal::request()->query->all();
    $values = array(
      'location' => !empty($query['location']) && is_numeric($query['location']) ? $query['location'] : NULL,
      'program' => !empty($query['program']) && is_numeric($query['program']) ? $query['program'] : NULL,
      'session_type' => !empty($query['session_type']) && is_numeric($query['session_type']) ? $query['session_type'] : NULL,
      'trainer' => !empty($query['trainer']) ? $query['trainer'] : NULL,
      'start_time' => !empty($query['start_time']) ? $query['start_time'] : NULL,
      'end_time' => !empty($query['end_time']) ? $query['end_time'] : NULL,
      'start_date' => !empty($query['start_date']) ? $query['start_date'] : NULL,
      'end_date' => !empty($query['end_date']) ? $query['end_date'] : NULL,
    );
    if (isset($query['context'])) {
      $values['context'] = $query['context'];
    }

    $form = MindbodyPTForm::create(\Drupal::getContainer());
    try {
      $search_results = $form->getSearchResults($values);
    }
    catch (MindbodyException $e) {
      $logger = \Drupal::getContainer()->get('logger.factory')->get('ymca_mindbody');
      $logger->error('Failed to get the results: %msg', ['%msg' => $e->getMessage()]);
      return [
        '#prefix' => '<div class="row mindbody-search-results-content">
          <div class="container">
            <div class="day col-sm-12">',
        '#markup' => $form->getDisabledMarkup(),
        '#suffix' => '</div></div></div>',
      ];
    }

    return [
      '#markup' => render($search_results),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Set Title.
   */
  public function setTitle() {
    return t('Personal Trainer Schedules');
  }

}
