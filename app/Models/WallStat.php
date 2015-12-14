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
}
