<?php
namespace Leochenftw\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use Leochenftw\Debugger;
use SilverStripe\Core\Config\Config;

/**
 *
 */
class APIBaseController extends Controller
{
    protected static $allowed_request_methods = [];

    public function isAuthenticated()
    {
        return true;
    }

    public function index()
    {
        $request        =   $this->request;
        $header         =   $this->getResponse();
        $method         =   strtolower($this->request->httpMethod());
        $this->shuvel_headers();

        $this->can_proceed();

        if ($request->isAjax()) {
            $header->addHeader('Content-type', 'application/json');
            return json_encode($this->$method($this->request));
        } else if ($methd = 'options') {
            return $this->$method($this->request);
        }

        return $this->httpError(400, 'ajax request only');
    }

    private function can_proceed()
    {
        $method         =   strtolower($this->request->httpMethod());
        if (isset(static::$allowed_request_methods[$method])) {
            $allowed    =   static::$allowed_request_methods[$method];

            if (is_bool($allowed)) {
                if ($allowed) {
                    return true;
                }

                return $this->httpError(400, 'method is not allowed');
            }

            $allowed    =   str_replace('->', '', $allowed);

            if (method_exists($this, $allowed)) {

                if ($this->$allowed()) {
                    return true;
                }

                return $this->httpError(400, 'method is not allowed');
            }

            return $this->httpError(400, 'method does not exist');
        }

        return $this->httpError(400, 'method is not allowed');
    }

    protected function shuvel_headers()
    {
        $header         =   $this->getResponse();
        $method         =   strtolower($this->request->httpMethod());

        if (!Director::isLive()) {
            $allowed_origins    =   Config::inst()->get(__class__, 'AccessControlAllowOrigin');
            $header->addHeader('Access-Control-Allow-Origin', $allowed_origins);
            $header->addHeader('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $header->addHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $header->addHeader('Access-Control-Allow-Credentials', "true");
        }
    }
}
