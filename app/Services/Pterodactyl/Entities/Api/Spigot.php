<?php

namespace App\Services\Pterodactyl\Entities\Api;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Spigot
{
    protected string $apiUrl;

    /**
     * Spigot constructor.
     *
     * @param string $apiUrl
     */
    public function __construct(string $apiUrl = 'https://api.spiget.org/v2')
    {
        $this->apiUrl = rtrim($apiUrl, '/');
    }

    /**
     * Get all plugins (resources) from Spigot with pagination.
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getPlugins(int $page = 1, int $size = 20): array
    {
        return $this->getRequestWithPagination('/resources', $page, $size);
    }

    /**
     * Get details of a specific plugin by ID.
     *
     * @param string $pluginId
     * @return array
     */
    public function getPlugin(string $pluginId): array
    {
        return $this->getRequest("/resources/{$pluginId}");
    }

    /**
     * Get all categories.
     *
     * @return array
     */
    public function getCategories(): array
    {
        $parents = [
            2 => [5, 6, 7, 8],
            3 => [9, 10, 11, 12, 13],
            4 => [14, 15, 16, 17, 18, 22, 23, 24, 25, 26, 28]
        ];

        $categories = $this->getRequest('/categories', ['size' => 100]);
        $childrenIds = [];
        foreach ($categories['data'] as $key => $category) {
            if ($category['id'] == "29" || $category['id'] == "27") {
                unset($categories['data'][$key]);
                continue;
            }
            if (array_key_exists($category['id'], $parents)) {
                $children = collect($categories['data'])->whereIn('id', $parents[$category['id']])->values()->all();
                $categories['data'][$key]['children'] = $children;
                $childrenIds = array_merge($childrenIds, array_column($children, 'id'));
            }
        }
        $categories['data'] = collect($categories['data'])->reject(fn($category) => in_array($category['id'], $childrenIds))->values()->all();
        return $categories;
    }

    /**
     * Get resources from a specific category with pagination.
     *
     * @param int $categoryId
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getResourcesByCategory(int $categoryId, int $page = 1, int $size = 20): array
    {
        return $this->getRequestWithPagination("/categories/{$categoryId}/resources", $page, $size);
    }

    /**
     * Get resources by author with pagination.
     *
     * @param int $authorId
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getResourcesByAuthor(int $authorId, int $page = 1, int $size = 20): array
    {
        return $this->getRequestWithPagination("/authors/{$authorId}/resources", $page, $size);
    }

    /**
     * Download a specific resource by ID.
     *
     * @param string $resourceId
     * @param null $versionId
     * @return string|array
     */
    public function downloadResource(string $resourceId, $versionId = null): string|array
    {
        ini_set('memory_limit', '512M');
        if ($versionId === null) {
            $response = Http::get("{$this->apiUrl}/resources/{$resourceId}/download");
        } else {
            $response = Http::get("{$this->apiUrl}/resources/{$resourceId}/versions/{$versionId}/download");
        }
        if ($response->successful()) {
            return $response->body(); // Return raw data (content of the file)
        }

        return $this->handleError($response);
    }

    /**
     * Search for a resource with pagination.
     *
     * @param string $query
     * @param int $page
     * @param int $size
     * @return array
     */
    public function searchResources(string $query, int $page = 1, int $size = 20): array
    {
        return $this->getRequestWithPagination('/search/resources/' . urlencode($query), $page, $size);
    }

    /**
     * Get versions of a specific resource.
     *
     * @param string $resourceId
     * @return array
     */
    public function getResourceVersions(string $resourceId): array
    {
        return $this->getRequest("/resources/{$resourceId}/versions");
    }

    /**
     * Get download link for a specific resource version.
     *
     * @param string $resourceId
     * @param int $versionId
     * @return array
     */
    public function getVersionDownloadLink(string $resourceId, int $versionId): array
    {
        return $this->getRequest("/resources/{$resourceId}/versions/{$versionId}/download");
    }

    public function getDownloadInfo(string $resourceId): array
    {
        $resourceInfo = $this->getPlugin($resourceId);
        if ($resourceInfo['error']) {
            return $resourceInfo;
        }
        $resourceInfo = $resourceInfo['data'];
        $fileName = PluginModsHelper::getJarName($resourceInfo['name']);
        $downloadUrl = $this->getFileUrl($resourceId);
        return array_merge([
            'fileName' => $fileName,
            'downloadUrl' => $downloadUrl,
        ], $resourceInfo);
    }

    public function getFileUrl($id): string
    {
        $host = "{$this->apiUrl}/resources/{$id}/download";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.spigotmc.org/');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $response = curl_exec($ch);
        curl_close($ch);
        if (preg_match('/Location: (.*)/i', $response, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }


    // Protected methods
    /**
     * Perform a GET request to the Spigot API.
     *
     * @param string $endpoint
     * @param array $queryParams
     * @return array
     */
    protected function getRequest(string $endpoint, array $queryParams = []): array
    {
        $response = Http::get("{$this->apiUrl}{$endpoint}", $queryParams);

        return $this->processResponse($response);
    }

    /**
     * Perform a GET request with pagination to the Spigot API.
     *
     * @param string $endpoint
     * @param int $page
     * @param int $size
     * @return array
     */
    protected function getRequestWithPagination(string $endpoint, int $page, int $size): array
    {
        $queryParams = $this->getPaginationParams($page, $size);

        return $this->getRequest($endpoint, $queryParams);
    }

    /**
     * Get pagination parameters.
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    protected function getPaginationParams(int $page, int $size): array
    {
        return [
            'page' => $page,
            'size' => $size,
            'sort' => '-downloads',
        ];
    }

    /**
     * Handle API errors and process response.
     *
     * @param Response $response
     * @return array
     */
    protected function processResponse(Response $response): array
    {
        if ($response->successful()) {
            return [
                'error' => false,
                'data' => $response->json(),
                'pagination' => $this->getPaginationInfo($response),
            ];
        }

        return $this->handleError($response);
    }

    /**
     * Extract pagination info from response.
     *
     * @param Response $response
     * @return array|null
     */
    protected function getPaginationInfo(Response $response): ?array
    {
        $headers = $response->headers();

        if (isset($headers['X-Total'][0], $headers['X-Page-Size'][0], $headers['X-Page-Index'][0], $headers['X-Page-Count'][0])) {
            return [
                'total' => (int)$headers['X-Total'][0],
                'per_page' => (int)$headers['X-Page-Size'][0],
                'current_page' => (int)$headers['X-Page-Index'][0],
                'total_pages' => (int)$headers['X-Page-Count'][0],
            ];
        }

        return null;
    }

    /**
     * Handle API errors.
     *
     * @param Response $response
     * @return array
     */
    protected function handleError(Response $response): array
    {
        return [
            'error' => true,
            'status' => $response->status(),
            'message' => $response->body(),
        ];
    }
}
