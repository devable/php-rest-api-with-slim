<?php

namespace App\Storage\CRM;

use Doctrine\DBAL\Connection;
use App\Helper\UtilsHelper;

class Newsfeed
{
    private const DB_FIELDS = [
        'id',
        'title',
        'content',
        'images',
        'dateCreated as created_at'
    ];

    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $type
     * @return integer
     */
    public function total(string $type): int
    {
        $status = $this->statusByType($type);
        $query = "SELECT count(`id`) FROM `newsfeed` WHERE `status` = :status AND `published_ec` = 1";
        $statement = $this->db->prepare($query);
        $statement->bindValue('status', $status);
        $statement->execute();
        return $statement->fetchColumn(0);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param string $type
     * @return array
     */
    public function list(string $type, int $limit, int $page, int $contentLength, bool $withImages): ?array
    {
        $excludeDbFields = [];
        if ($contentLength === 0) {
            $excludeDbFields[] = 'content';
        }
        if (!$withImages) {
            $excludeDbFields[] = 'images';
        }

        $fields = isset($excludeDbFields[0]) ? array_diff(self::DB_FIELDS, $excludeDbFields) : self::DB_FIELDS;

        $list = $this->db->createQueryBuilder()
            ->select(implode(',', $fields))
            ->from('newsfeed')
            ->where('published_ec = 1')
            ->andWhere('status = :status')
            ->setParameter('status', $this->statusByType($type))
            ->setMaxResults($limit)
            ->setFirstResult(($page-1)*$limit)
            ->orderBy('id', 'DESC')
            ->execute()
            ->fetchAll();

        if ($list === []) {
            return null;
        }

        return array_map(function ($item) use ($contentLength) {
            if (isset($item['content'])) {
                $item['content'] = UtilsHelper::excerpt($item['content'], $contentLength);
            }
            if (isset($item['images'])) {
                $item['images'] = self::parseImages($item['images']);
            }
            return UtilsHelper::omitNullValues($item);
        }, $list);
    }

    /**
     * @param integer $type
     * @param integer $id
     * @return array|null
     */
    public function item(string $type, int $id): ?array
    {
        $item = $this->db->createQueryBuilder()
                ->select(self::DB_FIELDS)
                ->from('newsfeed')
                ->where('published_ec = 1')
                ->andWhere('id = :id')
                ->andWhere('status = :status')
                ->setParameters([
                    'id' => $id,
                    'status' => $this->statusByType($type)
                ])
                ->execute()->fetch();

        if (!$item) {
            return null;
        }

        $item['content'] = UtilsHelper::cleanContent($item['content']);
        $item['images'] = self::parseImages($item['images']);

        return $item;
    }

    /**
     * @param string $type
     * @return integer
     */
    private function statusByType(string $type): int
    {
        return $type === 'articles' ? 1 : 2;
    }

    private static function parseImages($images): ?array
    {
        $newImages = [];
        $images = json_decode($images, true);
        if (is_array($images) && isset($images[0])) {
            foreach ($images as $image) {
                $newImage = [];
                $image['sizes'][] = '_original';
                foreach ($image['sizes'] as $size) {
                    $newImage[$size] = $image['path'] . '/' . $size . '/' . $image['name'];
                }
                $newImages[] = $newImage;
            }
        }

        return $newImages ?: null;
    }
}
