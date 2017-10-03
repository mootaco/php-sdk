<?php namespace Moota\SDK;

use Moota\SDK\Exceptions\MootaUnathorizedException;

/**
 * Class Auth
 * 
 * @method check
 * 
 * @package Moota\SDK
 */
class Auth
{
    private $optMode;
    private $optApiKey;
    private $basicAuth;
    private $token;
    private static $instance;

    /**
     * Check Moota Authorization
     *
     * @return bool|null
     */
    public function check($pleaseDie)
    {
        if (empty($this->optMode)) {
            $this->optMode = env('MOOTA_MODE', 'testing');
            $this->optApiKey = env('MOOTA_API_KEY');
            $this->basicAuth = Util::getAuthHeader();
        }

        if (!empty($this->basicAuth)) {
            $hasBasicAuth = strpos(
                strtolower($this->basicAuth), 'basic'
            ) === 0;

            if ($hasBasicAuth) {
                list($this->token, $other) = explode(
                    ':', substr($this->basicAuth, 6)
                );

                if (
                    $this->optMode == 'production'
                    && $this->optApiKey == $this->token
                ) {
                    return true;
                }
            }

            if (
                $this->optMode == 'testing' && $this->optApiKey == $this->token
            ) {
                return true;
            }
        }

        if ( !empty( $this->token = Util::getApiKey() ) ) {
            if (
                $this->optMode == 'production'
                && $this->optApiKey == $this->token
            ) {
                return true;
            }

            if (
                $this->optMode == 'testing' && $this->optMode == $this->token
            ) {
                return true;
            }
        }

        if ($pleaseDie) {
            // 401: Unauthorized
            http_response_code(401);
            die("Moota SDK User is Not Authorized");
        }

        return false;
    }

    /**
     * @throws MootaUnathorizedException
     * @return bool
     */
    public function checkOrFail()
    {
        if ($this->check()) {
            return true;
        }

        
    }
}
