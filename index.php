<?php

function parseVideos($videoString = null){
    
    // RETURN DATA
    $videos = array();
    if (!empty($videoString)) {
        // SPLIT ON LINE BREAKS
        $videoString = stripslashes(trim($videoString));
        $videoString = explode("\n", $videoString);
        $videoString = array_filter($videoString, 'trim');
        // CHECK EACH VIDEO FOR PROPER FORMATTING
        foreach ($videoString as $video) {
            // CHECK FOR IFRAME TO GET THE VIDEO URL
            if (strpos($video, 'iframe') !== FALSE) {
                // RETRIEVE THE VIDEO URL
                $anchorRegex = '/src="(.*)?"/isU';
                $results = array();
                if (preg_match($anchorRegex, $video, $results)) {
                    $link = trim($results[1]);
                }
            } else {
                // WE ALREADY HAVE A URL
                $link = $video;
            }
            // IF WE HAVE A URL, PARSE IT DOWN
            if (!empty($link)) {
                // INITIAL VALUES
                $video_id = NULL;
                $videoIdRegex = NULL;
                $results = array();
                // CHECK FOR TYPE OF YOUTUBE LINK
                if (strpos($link, 'youtu') !== FALSE) {
                    if (strpos($link, 'youtube.com') !== FALSE) {
                        if (strpos($link, 'youtube.com/watch') !== FALSE) {
                            $videoIdRegex = '/[\?\&]v=([^\?\&]+)/';
                        }else{
                            // works on:
                            // http://www.youtube.com/embed/VIDEOID
                            // http://www.youtube.com/embed/VIDEOID?modestbranding=1&rel=0
                            // http://www.youtube.com/v/VIDEO-ID?fs=1&hl=en_US
                            ///[\?\&]v=([^\?\&]+)/
                            $videoIdRegex = '/youtube.com\/(?:embed|v){1}\/([a-zA-Z0-9_]+)\??/i';
                        }
                    } else if (strpos($link, 'youtu.be') !== FALSE) {
                        // works on:
                        // http://youtu.be/daro6K6mym8
                        $videoIdRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
                    }
                    if ($videoIdRegex !== NULL) {
                        if (preg_match($videoIdRegex, $link, $results)) {
                            $video_str = 'https://www.youtube.com/embed/%s?autoplay=0';
                            $thumbnail_str = 'http://img.youtube.com/vi/%s/2.jpg';
                            $fullsize_str = 'http://img.youtube.com/vi/%s/0.jpg';
                            $video_id = $results[1];
                            $video_type = 'youtube';
                        }
                    }
                }
                // CHECK IF WE HAVE A VIDEO ID, IF SO, ADD THE VIDEO METADATA
                if (!empty($video_id)) {
                    // add to return
                    $videos = array(
                        'url' => sprintf($video_str, $video_id),
                        'thumbnail' => sprintf($fullsize_str, $video_id),
                        'video_type' => sprintf($video_type, $video_id),
                    );
                }else{
                    $videos = array(
                        'url' => null,
                        'thumbnail' => null,
                        'video_type' => '0',
                    );
                }
            }
        }
    }
    // RETURN ARRAY OF PARSED VIDEOS
    return $videos;
}

$response = parseVideos('https://www.youtube.com/watch?v=bn3PbZBaycY');

// RESPONS OUTPUT
print_r($response['thumbnail']);

file_put_contents('webappfix1.jpeg', file_get_contents($response['thumbnail']));
