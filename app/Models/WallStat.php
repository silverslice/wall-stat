<?php

namespace App\Models;

use Silverslice\EasyDb\Database;

class WallStat
{
    protected $db;
    protected $domain;

    public function __construct(Database $db, $domain)
    {
        $this->db = $db;
        $this->domain = $domain;
    }

    public function getLastUpdate()
    {
        return $this->db->getOne('SELECT updated_at FROM updates ORDER BY id DESC LIMIT 1');
    }

    public function getStatistics($query = '', $startDate = 0, $endDate = 0)
    {
        $cond = $this->buildSearchCondition($query, $startDate, $endDate);

        return $this->db->getAll('
            SELECT profiles.id, profiles.first_name, profiles.last_name, sum(likes_count) as likes_count
            FROM posts

            LEFT JOIN profiles
            ON profiles.id = posts.signer_id

            WHERE 1
            ?p

            GROUP BY profiles.id, profiles.first_name, profiles.last_name
            ORDER BY sum(likes_count) DESC
        ', $cond);
    }

    public function getUserPosts($userId, $query = '', $startDate = 0, $endDate = 0)
    {
        $cond = $this->buildSearchCondition($query, $startDate, $endDate);

        return $this->db->getAll('
            SELECT *
            FROM posts

            WHERE posts.signer_id = ?
            ?p

            ORDER BY posts.created_at DESC
        ', $userId, $cond);
    }

    public function update()
    {
        $parser = new WallParser();

        $params = [
            'domain' => $this->domain,
            'extended' => 1,
        ];

        $posts = [];
        $users = [];

        foreach ($parser->getPosts($params) as $res) {
            foreach ($res['items'] as $post) {
                if (isset($post['signer_id']) && $post['post_type'] == 'post') {
                    $posts[] = [
                        $post['id'],
                        $post['date'],
                        $post['signer_id'],
                        $post['text'],
                        $post['likes']['count'],
                    ];
                }
            }

            foreach ($res['profiles'] as $user) {
                $users[$user['id']] = [
                    $user['id'],
                    $user['first_name'],
                    $user['last_name'],
                ];
            }
        }

        try {
            $this->db->beginTransaction();

            $this->db->query('DELETE FROM posts');
            $this->db->query('DELETE FROM profiles');

            $this->db->multiInsert(
                'posts',
                ['id', 'created_at', 'signer_id', 'text', 'likes_count'],
                $posts
            );
            $this->db->multiInsert(
                'profiles',
                ['id', 'first_name', 'last_name'],
                $users
            );
            $this->db->insert('updates', ['updated_at' => time()]);

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw new \Exception('Unable to update statistics. Database error: ' . $e->getMessage());
        }
    }

    protected function buildSearchCondition($query = '', $startDate = 0, $endDate = 0)
    {
        if (!$endDate) {
            $endDate = time();
        }

        if (!is_int($startDate)) {
            $startDate = strtotime($startDate);
        }
        if (!is_int($endDate)) {
            $endDate = strtotime($endDate);
        }

        $queryPart = '';
        $periodPart = '';

        if ($query) {
            $queryPart = $this->db->prepare(" AND posts.text LIKE '%?e%'", $query);
        }

        if ($startDate || $endDate) {
            $periodPart = $this->db->prepare(" AND posts.created_at >= ? AND posts.created_at <= ?", $startDate, $endDate);
        }

        return $periodPart . $queryPart;
    }
}
