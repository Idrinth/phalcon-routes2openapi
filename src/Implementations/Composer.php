<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

class Composer
{
    /**
     * @param string $path
     * @return array
     */
    public function __invoke(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }
        $project = [];
        $data = json_decode(file_get_contents($path)?:'{}', true);
        if (isset($data['name']) && $data['name']) {
            $project['title'] = $data['name'];
        }
        if (isset($data['description']) && $data['description']) {
            $project['description'] = $data['description'];
        }
        return $project;
    }
}
