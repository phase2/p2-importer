<?php

namespace P2Importer\Fetchers;

use P2Importer\AbstractFetcher;
use P2Importer\P2Exception;

/**
 * Class to get JSON
 *
 * settings
 *  - url: url endpoint
 */
class Json extends AbstractFetcher {

  /**
   * Get a request object
   *
   * @throws \P2Importer\P2Exception
   */
  public function load() {
    if (empty($this->settings['url'])) {
      throw new P2Exception("Missing required setting: url");
    }

    $reply = drupal_http_request($this->settings['url']);

    if ($reply !== 200) {
      throw new \Exception("{$reply->code}: {$reply->error}");
    }

    return drupal_json_decode($reply->data);
  }
}
