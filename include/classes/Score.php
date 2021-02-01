<?php


namespace classes;

use classes\App;

/**
 * Add score to some content
 *
 * @return string Output string
 */

class Score
{
    private $target_type;
    private $table = 'score_log';

    /**
     * Set object params
     *
     * @param string $target_type
     * @param string $action_href Action HREF
     */
    public function __construct(string $target_type)
    {
        $this->target_type=$target_type;
    }

    /**
     * Return sum off score.
     *
     * @param integer $target_id ID of content
     *
     * @return int
     */
    public function getCount(int $target_id) : int
    {
        $query="select count(score) from {$this->table} where target_type=? and target_id=?";
        list($score) = App::$db->getRow($query, ['target_type' => $this->target_type, 'target_id' => $target_id]);
        return (int)$score;
    }

    /**
     * Return sum off score.
     *
     * @param integer $target_id ID of content
     *
     * @return int
     */
    public function getAverage(int $target_id) : int
    {
        $query="select sum(score)/count(score) from {$this->table} where target_type=? and target_id=?";
        list($score) = App::$db->getRow($query, ['target_type' => $this->target_type, 'target_id' => $target_id]);
        return (int)$score;
    }

    /**
     * Add score.
     *
     * @param integer $target_id ID of content
     * @param integer $score ID of content
     *
     * @return int
     */
    public function add(int $target_id, int $score) : int
    {
        $data = [];
        $data['date'] = 'now()';
        $data['target_type'] = $this->target_type;
        $data['target_id'] = $target_id;
        $data['uid'] = App::$user->id;
        $data['score'] = $score;
        $data['ip'] = App::$server['REMOTE_ADDR'];

        $query="select id from {$this->table} where target_type=? and target_id=? and ip=? and date > DATE_ADD(CURDATE(), INTERVAL -1 WEEK)";
        if (App::$db->getRow($query, ['target_type' => $this->target_type, 'target_id' => $target_id, 'ip' => App::$server['REMOTE_ADDR']]) == false) {
            App::$db->insertTable($this->table, $data);
        }
        return $score;
    }
}
