<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Http;

class RedirectResponse extends Response
{
    public function __construct($url, $status = 302, $headers = array())
    {
        parent::__construct('', $status, $headers);

        $this->setTargetUrl($url);
    }

    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * Sets the redirect target of this response.
     *
     * @param string $url The URL to redirect to
     *
     * @return RedirectResponse The current response.
     *
     * @throws \InvalidArgumentException
     */
    public function setTargetUrl($url)
    {
        $this->targetUrl = $url;

        $this->setContent(
                sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="1;url=%1$s" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        $this->headers->set('Location', $url);

        return $this;
    }
}
