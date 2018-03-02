<?php

namespace Clamd\ServiceProvider;

use Bdf\Web\Application;
use Bdf\Web\Providers\ServiceProviderInterface;
use Clamd\Clamd;
use Clamd\NullClamd;

/**
 * ClamdServiceProvider
 */
class ClamdServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure(Application $app)
    {
        /**
         * @var NullClamd|Clamd
         */
        $app->set('clamd', function(Application $app) {
            $config = $app->subConfig('clamd');

            if (!$config->get('enable')) {
                return new NullClamd();
            }

            if (!$dsn = $config->get('dsn')) {
                if ($pipe = $config->get('pipe')) {
                    $dsn = 'unix://'.$pipe;
                } else {
                    $dsn = $config->get('host', '127.0.0.1').':'.$config->get('port', 3310);
                }
            }

            return new Clamd($dsn);
        });
    }
}
