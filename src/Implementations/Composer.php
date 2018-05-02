<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

class Composer
{
    public function __invoke(string $composer)
    {
        if (!is_file($composer)) {
            return [];
        }
        $project = [];
        $data = json_decode(file_get_contents($composer)?:'{}', true);
        if (isset($data['name']) && $data['name']) {
            $project['title'] = $data['name'];
        }
        if (isset($data['description']) && $data['description']) {
            $project['description'] = $data['description'];
        }
        return $project;
    }
}