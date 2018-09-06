<?php
/**
 * @Author: Jax Wang
 * @Date:   2018-09-07 01:14:32
 * @Last Modified by:   Jax Wang
 * @Last Modified time: 2018-09-07 01:33:38
 */
require './lib/config.php';

$args = getopt('n:a:s:i:');

if($args){
    $song_name = __val($args,'n');
    $action = __val($args,'a','all');
    $source = __val($args,'s','qq');
    $cur_index = (int)__val($args,'i',0); // 当 `-a` 不等于 `show` 时生效

    if(!$song_name){
        __echo('缺少"-n"参数.');
        exit;
    }
    if(!in_array($action, ['all','show','audio','cover'])){
        __echo('"-a"参数不合法, 只能使用[all,show,audio,cover]中的其中一个.');
        exit;
    }
    if(!in_array($source, __sourcelist(true))){
        __echo('"-s"参数不合法, 请指定音乐源, 不传则使用QQ音乐, 参数列表: ');
        __echo(__sourcelist());
        exit;
    }
    if(!is_int($cur_index) || $cur_index >19 || $cur_index < 0){
        $cur_index = 0;
    }

     __echo('当前使用的音乐源: '.__sourcelist()[$source]);
    $audl = new AudioDL($source);
    $song_list = $audl -> search($song_name, $action);
    if(!$song_list){
        __echo('未能找到"'.$song_name.'", 请再试一次.');
        exit;
    }
    $songmid = $song_list[$cur_index]['songmid'];

    switch ($action) {
        case 'all':
        case 'audio':
        case 'cover':
            $audl -> getSong($songmid,$action);
            break;
        case 'show':
            __echo($song_list);
            break;
        default:
            # code...
            break;
    }
}



