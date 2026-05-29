<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RateLimitFilter implements FilterInterface
{
    protected $limit = 60; // Max requests per minute
    protected $window = 60; // Time window in seconds

    public function before(RequestInterface $request, $arguments = null)
    {
        $cache = service('cache');
        $ip = $request->getIPAddress();
        
        // Build cache key based on IP
        $cacheKey = "ratelimit:" . md5($ip);

        // Get current request count
        $current = $cache->get($cacheKey);

        if ($current === null) {
            // First request in this window
            $cache->save($cacheKey, 1, $this->window);
        } else {
            if ($current >= $this->limit) {
                // Return 429 Too Many Requests response
                $response = service('response');
                $response->setStatusCode(429);

                if ($request->isAJAX() || strpos($request->getHeaderLine('Accept'), 'application/json') !== false) {
                    return $response->setJSON([
                        'status'  => 'error',
                        'message' => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.'
                    ]);
                }

                return $response->setBody('<h1>429 Too Many Requests</h1><p>Terlalu banyak permintaan. Silakan tunggu beberapa saat sebelum mencoba kembali.</p>');
            }

            // Increment request count
            // Note: Keep the remaining TTL if possible, or just renew for simplicity
            $cache->save($cacheKey, $current + 1, $this->window);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
