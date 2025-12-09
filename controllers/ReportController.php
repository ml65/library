<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * ReportController реализует отчеты системы.
 */
class ReportController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['top-authors'],
                        'allow' => true,
                        'roles' => ['?', '@'], // guest и user
                    ],
                ],
            ],
        ];
    }

    /**
     * Отчет ТОП-10 авторов по количеству книг за год
     *
     * @param int|null $year Год для фильтрации (null = все годы)
     * @return string
     */
    public function actionTopAuthors($year = null)
    {
        // Нормализовать параметр year: если пустой или не число, то null
        if ($year !== null && $year !== '') {
            $year = (int) $year;
            if ($year <= 0) {
                $year = null;
            }
        } else {
            $year = null;
        }

        // SQL-запрос для получения ТОП-10 авторов
        $sql = "SELECT 
                    author.id,
                    author.full_name,
                    COUNT(book.id) AS books_count
                FROM {{%author}} author
                INNER JOIN {{%book_author}} book_author ON author.id = book_author.author_id
                INNER JOIN {{%book}} book ON book_author.book_id = book.id
                WHERE (:year IS NULL OR book.year = :year)
                GROUP BY author.id, author.full_name
                ORDER BY books_count DESC
                LIMIT 10";

        $authors = Yii::$app->db->createCommand($sql, [':year' => $year])
            ->queryAll();

        Yii::info("Top authors report viewed: year=" . ($year ?? 'all'), __METHOD__);

        return $this->render('top-authors', [
            'authors' => $authors,
            'year' => $year,
        ]);
    }
}

