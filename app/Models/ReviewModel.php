<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class ReviewModel extends BaseModel
{
    protected $table = 'review';

    public function getAllReviews()
    {
        $sql = "SELECT r.*, c.clt_fname, c.clt_lname
                FROM review r
                JOIN client c ON r.client_code = c.clt_code
                ORDER BY r.review_date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating()
    {
        $sql = "SELECT ROUND(AVG(rating), 1) AS average_rating, COUNT(*) AS total_reviews FROM review";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRatingDistribution()
    {
        $sql = "SELECT rating, COUNT(*) AS count FROM review GROUP BY rating ORDER BY rating DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addReview($client_code, $rating, $comment)
    {
        $sql = "INSERT INTO review (client_code, rating, comment) VALUES (:client_code, :rating, :comment)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'client_code' => $client_code,
            'rating' => $rating,
            'comment' => $comment
        ]);
    }
}
