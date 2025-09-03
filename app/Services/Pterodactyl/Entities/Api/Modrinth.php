<?php

namespace App\Services\Pterodactyl\Entities\Api;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Modrinth
{
    protected string $apiUrl;
    public ?string $personalToken = null;

    public function __construct(string $apiUrl = 'https://api.modrinth.com/v2')
    {
        $this->apiUrl = rtrim($apiUrl, '/');
    }

    public function setClientPersonalToken($token): void
    {
        $this->personalToken = $token;
    }

    /**
     * Get mods with optional categories and search term.
     *
     * @param int $page
     * @param int $size
     * @param array $categories
     * @param string|null $search
     * @return array
     */
    public function getMods(int $page = 1, int $size = 20, array $categories = [], string $search = null): array
    {
        $extraCategoryFilters = array_map(fn($category) => "categories:{$category}", $categories);
        $defaultCategories = array_map(fn($category) => "categories:{$category}", $this->getModCategories());
        if (!empty($extraCategoryFilters)){
            $facets = [
                $extraCategoryFilters,
                $defaultCategories,
                ["project_type:mod"],
            ];
        } else {
            $facets = [
                $defaultCategories,
                ["project_type:mod"],
            ];
        }

        return $this->getRequestWithPagination('/search', $page, $size, [
            'facets' => json_encode($facets),
            'index' => 'relevance',
            'query' => $search,
        ]);
    }

    /**
     * Get plugins with optional categories and search term.
     *
     * @param int $page
     * @param int $size
     * @param array $categories
     * @param string|null $search
     * @return array
     */
    public function getPlugins(int $page = 1, int $size = 20, array $categories = [], string $search = null): array
    {
        $extraCategoryFilters = array_map(fn($category) => "categories:{$category}", $categories);
        $defaultCategories = array_map(fn($category) => "categories:{$category}", $this->getPluginCategories());
        if (!empty($extraCategoryFilters)){
            $facets = [
                $extraCategoryFilters,
                $defaultCategories,
                ["project_type:mod"],
            ];
        } else {
            $facets = [
                $defaultCategories,
                ["project_type:mod"],
            ];
        }
        return $this->getRequestWithPagination('/search', $page, $size, [
            'facets' => json_encode($facets),
            'index' => 'relevance',
            'query' => $search,
        ]);
    }

    /**
     * Get a specific project by ID.
     *
     * @param string $project_id
     * @return array
     */
    public function getProject(string $project_id): array
    {
        return $this->getRequest("/project/{$project_id}");
    }

    /**
     * Get versions for a project with optional filtering by type (plugin or mod) and version ID.
     *
     * @param string $project_id
     * @param string $type 'plugin' or 'mod'
     * @param string|null $version_id Optional specific version ID to retrieve.
     * @return array
     */
    public function getVersions(string $project_id, string $type = 'plugin', string $version_id = null): array
    {
        if ($version_id) {
            return $this->getRequest("/project/{$project_id}/version/{$version_id}");
        }

        $allVersions = $this->getRequest("/project/{$project_id}/version");
        if ($allVersions['error']) {
            return $allVersions;
        }
        $categories = ($type === 'plugin') ? $this->getPluginCategories() : $this->getModCategories();
        $filteredVersions = [];
        foreach ($allVersions['data'] as $res) {
            foreach ($res['loaders'] as $loader) {
                if (in_array($loader, $categories, true)) {
                    $filteredVersions[] = $res;
                    break;
                }
            }
        }

        return [
            'error' => false,
            'data' => $filteredVersions
        ];
    }


    /**
     * Get download URL for a specific project by ID with an optional version ID.
     *
     * @param string $projectId
     * @param string|null $versionId
     * @return array
     */
    public function getDownloadUrl(string $projectId, ?string $versionId = null): array
    {
        $endpoint = $versionId ? "/project/{$projectId}/version/{$versionId}" : "/project/{$projectId}/version";
        $response = $this->getRequest($endpoint);

        if (!$response['error'] && isset($response['data'])) {
            if ($versionId) {
                return [
                    'error' => false,
                    'url' => $response['data']['files'][0]['url'] ?? 'No file URL available for this version',
                ];
            } else {
                $latestVersionId = $response['data'][0]['id'] ?? null;
                if ($latestVersionId) {
                    return $this->getDownloadUrl($projectId, $latestVersionId);
                }
            }
        }

        return [
            'error' => true,
            'message' => 'Failed to retrieve download URL for project',
        ];
    }

    /**
     * Get available categories for mods and plugins.
     *
     * @return array
     */
    public function getCategories(): array
    {
        return [
            'mobs',
            'worldgen',
            'utility',
            'transportation',
            'technology',
            'storage',
            'social',
            'optimization',
            'minigame',
            'management',
            'magic',
            'library',
            'game-mechanics',
            'food',
            'equipment',
            'economy',
            'decoration',
            'cursed',
            'adventure',
        ];
    }

    /**
     * Get default mod categories.
     *
     * @return array
     */
    protected function getModCategories(): array
    {
        return [
            'forge', 'fabric', 'quilt', 'liteloader', 'modloader', 'rift', 'neoforge'
        ];
    }

    /**
     * Get default plugin categories.
     *
     * @return array
     */
    protected function getPluginCategories(): array
    {
        return [
            'bukkit', 'spigot', 'paper', 'purpur', 'sponge', 'bungeecord', 'waterfall', 'velocity', 'folia'
        ];
    }

    protected function getRequest(string $endpoint, array $queryParams = []): array
    {
        $response = Http::withHeaders($this->getAuthHeaders())->get("{$this->apiUrl}{$endpoint}", $queryParams);
        return $this->processResponse($response);
    }

    protected function getRequestWithPagination(string $endpoint, int $page, int $size, array $additionalParams = []): array
    {
        $queryParams = array_merge($this->getPaginationParams($page, $size), $additionalParams);
        return $this->getRequest($endpoint, $queryParams);
    }

    protected function getPaginationParams(int $page, int $size): array
    {
        return [
            'limit' => $size,
            'offset' => ($page - 1) * $size,
        ];
    }

    protected function getAuthHeaders(): array
    {
        $headers = ['User-Agent' => 'Mozilla/5.0'];
        if ($this->personalToken) {
            $headers['Authorization'] = "Bearer {$this->personalToken}";
        }
        return $headers;
    }

    protected function processResponse(Response $response): array
    {
        if ($response->successful()) {
            return ['error' => false, 'data' => $response->json()];
        }
        return $this->handleError($response);
    }

    protected function handleError(Response $response): array
    {
        return [
            'error' => true,
            'status' => $response->status(),
            'message' => $response->body(),
        ];
    }
}
