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
        $cacheKey = str_replace(':', '_', $ip); // Avoid reserved characters in IPv6

        // Membatasi 5 upaya dalam 1 menit
        if ($throttler->check($cacheKey, 5, MINUTE) === false) {
            $response = service('response');
            $response->setStatusCode(429);
            return $response->setBody(view('errors/html/error_429', [
                'message' => 'Terlalu banyak percobaan login. Silakan tunggu beberapa saat.'
            ]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    // Do nothing
    }
}
