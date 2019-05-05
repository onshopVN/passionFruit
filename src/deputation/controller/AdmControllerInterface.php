<?php
/**
 * Created by PhpStorm.
 * Profile: macos
 * Date: 2/15/19
 * Time: 2:28 PM
 */

namespace App\deputation\controller;

use App\deputation\repository\RepositoryInterface;

interface AdmControllerInterface
{
    public function getObjectRepository(): ?RepositoryInterface;
    public function setObjectRepository(?RepositoryInterface $repository);

    public function list($paginator): ?array;
    public function create(?string $route, ?array $routeArray);
    public function update(?int $id, ?string $route, ?array $routeArray);
    public function delete(?int $id, ?string $route, ?array $routeArray);
    public function read(?int $id): ?array;

}