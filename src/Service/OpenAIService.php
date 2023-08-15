<?php

namespace Drupal\openai_connection\Service;

use GuzzleHttp\Client;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service class for OpenAI integration.
 */
class OpenAIService {

  /**
   * The OpenAI API base URI.
   */
  const BASE_URI = 'https://api.openai.com/v1/';

  /**
   * The OpenAI API key.
   *
   * @var string
   */
  protected $apiKey;

  /**
   * The Guzzle HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * ConfigFactoryInterface client.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * OpenAIService constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
    $config = $this->configFactory->get('openai_connection.settings');
    $this->apiKey = ltrim($config->get('openai_api_key'));
    $this->client = new Client();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Generates text using GPT-4.
   *
   * @param string $prompt
   *   A String with the prompt.
   *
   * @return string
   *   The generated text.
   */
  public function request($prompt) {
    $url = 'https://api.openai.com/v1/chat/completions';
    $headers = [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $this->apiKey,
    ];
    $data = [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ]
    ];
    
    $response = $this->client->post($url, [
        'headers' => $headers,
        'json' => $data
    ]);

    return $response;
  }

}
