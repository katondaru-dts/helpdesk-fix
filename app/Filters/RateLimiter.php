<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimiter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = \Config\Services::throttler();

        $ip = $request->getIPAddress();
        $cacheKey = 'ratelimit_' . str_replace(':', '_', $ip);

        // Default: 10 requests per minute. Override via $arguments: [maxAttempts, period]
        $maxAttempts = $arguments[0] ?? 10;
        $period = $arguments[1] ?? MINUTE;

        if ($throttler->check($cacheKey, $maxAttempts, $period) === false) {
            $response = service('response');
            $response->setStatusCode(429);
            return $response->setBody(view('errors/html/error_429', [
                'message' => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.'
            ]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
