<?php

namespace App\Controllers;

use App\Models\ReviewModel;

class ReviewController
{
    protected $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
    }

    public function getAll()
    {
        header('Content-Type: application/json');
        echo json_encode($this->reviewModel->getAllReviews());
    }

    public function getAverage()
    {
        header('Content-Type: application/json');
        echo json_encode($this->reviewModel->getAverageRating());
    }

    public function getDistribution()
    {
        header('Content-Type: application/json');
        echo json_encode($this->reviewModel->getRatingDistribution());
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client_code = $_POST['client_code'] ?? null;
            $rating = $_POST['rating'] ?? null;
            $comment = $_POST['review'] ?? '';

            if ($client_code && $rating) {
                $success = $this->reviewModel->addReview($client_code, $rating, $comment);
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Missing data']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
    }
}
