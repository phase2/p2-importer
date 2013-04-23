<?php

namespace P2Importer\Fetchers;

use P2Importer\AbstractFetcher;

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
   * @throws \Exception
   */
  public function load() {
    if (empty($this->settings['url'])) {
      throw new \Exception("Missing required setting: url");
    }

    $reply = drupal_http_request($this->settings['url']);

    if (!empty($reply->error)) {
      throw new \Exception("{$reply->code}: {$reply->error} url: {$this->settings['url']}");
    }

    return drupal_json_decode($reply->data);
  }
}
