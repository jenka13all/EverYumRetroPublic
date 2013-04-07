<?php

namespace EverYum;

/**
 * This is the everyum application
 *
 * @package EverYum
 * @author Armin Hackmann
 */
class Application {

    public $config;

    /**
     * Initializes the application.
     *
     * This class should receive an array with configuration data.
     * Sample configuration can be found in the conf/ directory.
     *
     * @param array $config
     */
    public function __construct(array $config) {

        foreach($config as $key=>$value) {
            $this->config[$key] = $value;
        }

        $this->initServices();

    }

    /**
     * Initializes services
     *
     * @return void
     */
    protected function initServices() {

        $this->service['yummly'] = new \EverYum\Service\Yummly($this->config);
        $this->service['evernote'] = new \EverYum\Service\Evernote($this->config);
        $this->service['tropo'] = new \EverYum\Service\Tropo($this->config);

    }

    }

}
