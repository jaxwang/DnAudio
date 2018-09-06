<?php
/**
 * @Author: Jax Wang
 * @Date:   2018-09-07 01:03:17
 * @Last Modified by:   Jax Wang
 * @Last Modified time: 2018-09-07 01:33:53
 */

Class AudioDL{
    private $host = '687474703a2f2f6d6f7265736f756e642e746b';
    private $api_search;
    private $api_get_song;
    private $cookies = '';
    private $cover_path = '';
    private $audio_lib_path = '';

    public function __construct($source ='qq'){
       $this->host = hex2bin($this->host);
       $this->cover_path = '/Users/'.whoami().'/JaxMusic/AudioCovers';
       $this->audio_lib_path = '/Users/'.whoami().'/JaxMusic/AudioLib';
       $this->setAudioSource($source);
    }

    public function setAudioSource($source){

        if(!in_array($source, ['qq','kw','xm','kg','bd','wy'])){
            $source = 'qq';
        }
        $this->api_search = $this->host .'/music/api.php?search=' . $source;
        $this->api_get_song = $this->host .'/music/api.php?get_song=' . $source;
    }

    public function setCookies(){
        $rsa = new RSAHack();
        $this->cookies = 'encrypt_data='.$rsa -> encrypt();
    }

    public function search($song_name,$type=''){
        $this->setCookies();
        $req_header = ['Cookie: '.$this->cookies];
        $csh = new Libcurl($this->api_search,$req_header,20);
        $args = ['w' => $song_name,'p' => 1 ,'n' => 20];
        $csh -> doPOST($args);
        $body = $csh->getBody();
        $body = json_decode($body,true);
        $body = $body['song_list'];
        if($body && $type == 'show'){
            foreach($body as $key=>$v){
                $body[$key]['songname'] = __killslash($v['songname']);
                $body[$key]['albumname'] = __killslash($v['albumname']);

                foreach ($v['singer'] as $vkey => $vva) {
                    $singer_name = '';
                    $singer_name .= __killslash($vva['name']). ' ';
                }
                $body[$key]['singer'] = $singer_name;
                $songInfo = $this->getSong($v['songmid'],$type);
                $body[$key]['info'] = isset($songInfo['url'])?$songInfo['url']:'';
            }
            krsort($body);
        }
        return $body;
    }

    /**
     * [getSong description]
     * @param  $songmid string
     * @param  $type string [all, audio, cover]
     *         all: 默认值, 下载音频和专辑封面
     *         audio: 只下载音频, 优先下载无损格式flac,ape 其次是压缩音频格式MP3 AAC
     *         cover: 只下载专辑封面
     * @return $songObj
     * @author jaxwang.com 2018-09-04
     */
    public function getSong($songmid,$type='all'){
        $this->setCookies();
        $req_header = ['Cookie: '.$this->cookies];
        $csh = new Libcurl($this->api_get_song,$req_header,20);
        $args = ['mid' => $songmid];
        $csh -> doPOST($args);
        $body = $csh->getBody();
        $body = json_decode($body,true);

        if($body && isset($body['url']) && $type != 'show'){
            $body['singer'] = __killslash($body['singer']);
            $body['song'] = __killslash($body['song']);
            $body['album'] = __killslash($body['album']);

            $song_name = $body['singer'] . ' - ' . $body['song'];
            $cover_name = $body['singer'] . ' - ' . $body['album'];
            //$album_path = $this->audio_lib_path . '/' . $body['singer'] .'/'. $body['album'];
            $this->exec('mkdir -p "' . $this->audio_lib_path . '"');
            //$this->exec('mkdir -p "' . $album_path . '"');
           // $song_full_path = $album_path .'/'.$song_name;
            $song_full_path = $this->audio_lib_path .'/'.$song_name;

            if(in_array($type, ['all','audio'])){
                // download lossless audio
                if(isset($body['url']['FLAC'])){
                    $this->download_audio($body['url']['FLAC'], $song_full_path);
                }else if(isset($body['url']['APE'])){
                    $this->download_audio($body['url']['APE'], $song_full_path);
                    __echo($song_name . " lossless audio not found");
                } else if(isset($body['url']['320MP3'])){
                    // download compressed audio
                    $this->download_audio($body['url']['320MP3'], $song_full_path);
                }else if(isset($body['url']['128MP3'])){
                    $this->download_audio($body['url']['128MP3'], $song_full_path);
                }else if(isset($body['url']['24AAC'])){
                    $this->download_audio($body['url']['24AAC'] , $song_full_path);
                }else{
                     __echo($song_name . " audio not found");
                }
            }
            if(in_array($type, ['all','cover'])){
                //download cover img
                if(isset($body['url']['专辑封面'])){
                    $this->download_img($body['url']['专辑封面'], $cover_name . '.jpg');
                }else{
                    __echo($song_name . " cover img not found");
                }
            }
        }

        return $body;
    }

    public function download_audio($url,$filename){
        $this->setCookies();
        $req_header = ['Cookie: '.$this->cookies];
        $csh = new Libcurl($this->host . '/music/' . $url ,$req_header,20);
        $csh -> doGET();
        $body = $csh->getBody();
        $body = json_decode($body,true);
        if(isset($body['url'])){
            $filename = $filename . '.'.$body['suffix'];
            __echo('start to download audio '.$filename);
            file_put_contents($filename, file_get_contents($body['url']));
            //$this->exec('curl -o "'. $filename . '"  ' . $body['url']);
        }else{
            print_r($body);
        }
    }

    public function download_img($url,$filename){
        __echo('start to download cover '.$filename);
        $img = file_get_contents($url);
        file_put_contents( $this->cover_path . '/' . $filename , $img);
    }

    public function exec($shell){
        system($shell);
    }
}
