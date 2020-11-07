<?php

namespace FacebookAnonymousPublisher\GraphApi;

use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\FileUpload\FacebookFile;

class GraphApi
{
    /**
     * @var Facebook
     */
    protected $fb;

    /**
     * @var FacebookResponse
     */
    protected $response;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->fb = new Facebook(array_merge($config, [
            'http_client_handler' => 'guzzle',
        ]));
    }

    /**
     * Create a status.
     *
     * @param string $message
     * @param null|string $link
     *
     * @return $this
     */
    public function status($message, $link = null)
    {
        $this->response = $this->fb->post('/me/feed', [
            'message' => $message,
            'link' => $link,
        ]);

        return $this;
    }

    /**
     * Create a photo.
     *
     * @param string $source
     * @param null|string $caption
     *
     * @return $this
     */
    public function photo($source, $caption = null)
    {
        $this->response = $this->fb->post('/me/photos', [
            'source' => new FacebookFile($source),
            'caption' => $caption,
        ]);

        return $this;
    }

    /**
     * Create multiple photos.
     *
     * @param array $sources
     * @param null|string $caption
     *
     * @return $this
     */
    public function photos(array $sources, $caption = null)
    {
        $images = $this->unpublishedPhotos($sources);

        $this->response = $this->fb->post('/me/feed', array_merge([
            'message' => $caption,
        ], $images));

        return $this;
    }

    /**
     * Create unpublished photos.
     *
     * @param array $sources
     *
     * @return array
     */
    protected function unpublishedPhotos(array $sources)
    {
        $images = [];

        foreach ($sources as $index => $source) {
            $response = $this->fb
                ->post('/me/photos', [
                    'source' => new FacebookFile($source),
                    'published' => false,
                ])
                ->getDecodedBody();

            $images["attached_media[{$index}]"] = sprintf('{"media_fbid":"%d"}', $response['id']);
        }

        return $images;
    }

    /**
     * Get facebook response id.
     *
     * @return array|bool
     */
    public function getId()
    {
        if (is_null($this->response)) {
            return false;
        }

        return $this->explodeId($this->response->getDecodedBody());
    }

    /**
     * Explode facebook response id field.
     *
     * @param array $body
     *
     * @return array|bool
     */
    protected function explodeId(array $body)
    {
        if (isset($body['post_id'])) {
            $key = 'post_id';
        } elseif (isset($body['id'])) {
            $key = 'id';
        } else {
            return false;
        }

        return array_combine(['id', 'fbid'], explode('_', $body[$key]));
    }

    /**
     * @param Facebook $fb
     *
     * @return GraphApi
     */
    public function setFb($fb)
    {
        $this->fb = $fb;

        return $this;
    }

    /**
     * @return FacebookResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param FacebookResponse $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }
}
