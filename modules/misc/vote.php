<?php

$tags['Header'] = "Голосование";
$tags['nav_str'] .= "<span class=nav_next>{$tags['Header']}</span>";

if (is_array($input[vote])) {
    if (!$_COOKIE[$COOKIE_NAME . "_VOTE"]) {
        setcookie($COOKIE_NAME . "_VOTE", time(), time() + $settings['stats_cookie_hours'] * 3600);
        $dont_vote = 0;
    } else {
        $dont_vote = 1;
    }
    $query = "select id from vote_log where ip='" . $server["REMOTE_ADDR"] . "' and unix_timestamp(date)>unix_timestamp()-24*60*60";
    $result = my_query($query, true);
    if ($result->num_rows > 5) {
        $dont_vote = 1;
    }
//	$dont_vote=0;
    if ($dont_vote) {
        $content = my_msg_to_str("vote_deny");
    } else {
        foreach ($input['vote'] as $key => $value) {
            $query = "insert into vote_log(date,variant_id,ip) values(now(),'{$value}','" . $server["REMOTE_ADDR"] . "')";
            my_query($query, true);
        }
        $content = my_msg_to_str("vote_success");
    }
}

list($vote_id, $vote_title) = my_select_row("select id,title from vote_list where active=1", 1);
$tags['vote_title'] = $vote_title;

$query = "select title,count(vote_log.id) as hits from vote_variants 
left join vote_log on (vote_log.variant_id=vote_variants.id)
where vote_id='{$vote_id}' group by vote_variants.id order by num";
$result = my_query($query);

$total = 0;
while ($row = $result->fetch_array()) {
    $total += $row['hits'];
}
$result->data_seek(0);
while ($row = $result->fetch_array()) {
    if (!$row['hits']) {
        $row['hits'] = 0;
        $percent = 0;
    }
    if ($total) {
        $percent = round(100 * $row['hits'] / $total, 2);
    }
    $tags['vote_results'] .= "<tr valign=middle>
	<td nowrap align=left width=40%>$row[title]</td>
        <td nowrap width=40%><img src=../images/gr.gif width=" . round(300 * $percent / 100, 0) . " height=14 border=0> {$percent}%</td>
        <td nowrap width=20%>{$row['hits']} голосов</td>
	</tr>";
}

$content .= get_tpl_by_title('vote_results', $tags, $result);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
