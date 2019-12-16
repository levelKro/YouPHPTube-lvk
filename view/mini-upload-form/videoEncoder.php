<?php
$configFile = dirname(__FILE__).'/../../videos/configuration.php';
require_once $configFile;
require_once $global['systemRootPath'] . 'objects/video.php';
$videoResolution = $config->getVideo_resolution();

header('Content-Type: application/json');
$videoConverter = array();
//$videoConverter['mp4'] = ' -vf scale=' . $videoResolution . ' -vcodec h264 -acodec aac -strict -2 -y ';
//$videoConverter['webm'] = '-vf scale=' . $videoResolution . ' -f webm -c:v libvpx -b:v 1M -acodec libvorbis -y';
$videoConverter['mp4'] = ($config->getEncode_mp4())?$config->getFfmpegMp4():false;
$videoConverter['webm'] = ($config->getEncode_webm())?$config->getFfmpegWebm():false;

$audioConverter = array();
//$audioConverter['mp3'] = ' -acodec libmp3lame -y ';
//$audioConverter['ogg'] = ' -acodec libvorbis -y ';
$audioConverter['mp3'] = $config->getFfmpegMp3();
$audioConverter['ogg'] = $config->getFfmpegOgg();

$filename = $argv[1];
$original_filename = "original_{$filename}";
$videoId = $argv[2];
$type = @$argv[3];
$status = 'a';

$video = new Video(null, null, $videoId);

if ($type == 'audio' || $type == 'mp3' || $type == 'ogg') {
    foreach ($audioConverter as $key => $value) {
        if ($type !== 'audio' && $type != $key) {
            continue;
        }

        // convert video
        echo "\n\n--Converting audio {$key} \n";
        $pathFileName = "{$global['systemRootPath']}videos/{$original_filename}";
        $destinationFile = "{$global['systemRootPath']}videos/{$filename}.{$key}";
        eval('$ffmpeg ="' . $value . '";');
        $cmd = "rm -f {$global['systemRootPath']}videos/{$filename}.{$key} && rm -f {$global['systemRootPath']}videos/{$filename}_progress_{$key}.txt && {$ffmpeg}";
        echo "** executing command {$cmd}\n";
        exec($cmd . "  < /dev/null 1> {$global['systemRootPath']}videos/{$filename}_progress_{$key}.txt  2>&1", $output, $return_val);
        if ($return_val !== 0) {
            echo "\\n **AUDIO ERROR**\n", print_r($output, true);
            error_log($cmd . "\n" . print_r($output, true));
            if ($status == 'a') {
                $status = 'x' . $key;
            } else {
                $status = 'x';
            }
        } else {
            echo "\n {$key} Ok\n";
            if ($key == 'mp3' && $config->getEncode_mp3spectrum()) {                
                echo "Try FFMPEG Spectrum\n";
                $destinationFile = "{$global['systemRootPath']}videos/{$filename}.mp4";
                eval('$ffmpeg ="' . $config->getFfmpegSpectrum() . '";');
                //$ffmpeg = "ffmpeg -i {$pathFileName} -filter_complex \"[0:a]showwaves=s=858x480:mode=line,format=yuv420p[v]\" -map \"[v]\" -map 0:a -c:v libx264 -c:a copy {$destinationFile}";
                $cmd = "rm -f $destinationFile && rm -f {$global['systemRootPath']}videos/{$filename}_progress_mp4.txt && {$ffmpeg}";
                echo "** executing command {$cmd}\n";
                exec($cmd . "  < /dev/null 1> {$global['systemRootPath']}videos/{$filename}_progress_mp4.txt  2>&1", $output, $return_val);
                if ($return_val !== 0) {
                    echo "\\n **Spectrum ERROR**\n", print_r($output, true);
                    error_log($cmd . "\n" . print_r($output, true));
                } else {
                    echo "FFMPEG Spectrum MP4 Success\n";
                    echo "FFMPEG Spectrum WEBM Start\n";
                    $pathFileName = $destinationFile;
                    $destinationFile = "{$global['systemRootPath']}videos/{$filename}.webm";
                    eval('$ffmpeg ="' . $videoConverter['webm'] . '";');
                    $cmd = "rm -f $destinationFile && rm -f {$global['systemRootPath']}videos/{$filename}_progress_webm.txt && {$ffmpeg}";
                    echo "** executing command {$cmd}\n";
                    exec($cmd . "  < /dev/null 1> {$global['systemRootPath']}videos/{$filename}_progress_webm.txt  2>&1", $output, $return_val);
                    if ($return_val !== 0) {
                        echo "\\n **VIDEO ERROR**\n", print_r($output, true);
                        error_log($cmd . "\n" . print_r($output, true));
                        if ($status == 'a') {
                            $status = 'x' . $key;
                        } else {
                            $status = 'x';
                        }
                    } else {
                        echo "FFMPEG Spectrum WEBM Success\n";
                    }
                }
            }
        }
    }
}


$pathFileName = "{$global['systemRootPath']}videos/{$original_filename}";
exec("ffmpeg -i {$pathFileName}  2>&1", $videoInfo, $return_val);
echo("Original video info: " . print_r($videoInfo));
foreach ($videoConverter as $key => $value) {
    $destinationFile = "{$global['systemRootPath']}videos/{$filename}.{$key}";

    //first, check whether the file is already encoded in the specified convertion
    if ( ($key == "webm" && strpos($videoInfo[12], "matroska,webm") !== false) 
        || ($key == "mp4" && strpos($videoInfo[12], "mov,mp4,m4a,3gp,3g2,mj2") !== false) ) {

        //if the file is already encoded, it just copy the file, bypassing the convertion phase for this converter
        exec("cp {$pathFileName} {$destinationFile}", $output, $return_val);

        if ($return_val !== 0) {
            echo "\\n **VIDEO ERROR**\n", print_r($output, true);        
            error_log($cmd . "\n" . print_r($output, true));
            if ($status == 'a') {
                $status = 'x' . $key;
            } else {
                $status = 'x';
            }
        } else {
            //it also copy a 100% TXT progress file to update the view
            exec("cp {$global['systemRootPath']}videos/FinishedProgressSample.{$key}.txt {$global['systemRootPath']}videos/{$filename}_progress_{$key}.txt", $output, $return_val);
        
            echo "Updating Duration .{$key}";
            $video->updateDurationIfNeed(".{$key}");
            echo "\n {$key} Ok\n";
        
        }
        continue;
    }

    if ((!empty($type) && $type != $key) || empty($value)) {
        continue;
    }
    // convert video
    echo "\n\n--Converting video {$key} \n";
    // check if is portrait video
    if (!Video::isLandscape($pathFileName)) {
        eval('$value = $config->getFfmpeg' . ucfirst($key) . 'Portrait();');
    }

    
    eval('$ffmpeg ="' . $value . '";');
    $cmd = "rm -f {$$destinationFile} && rm -f {$global['systemRootPath']}videos/{$filename}_progress_{$key}.txt && {$ffmpeg}";
    echo "** executing command {$cmd}\n";
    exec($cmd . " < /dev/null 1> {$global['systemRootPath']}videos/{$filename}_progress_{$key}.txt  2>&1", $output, $return_val);
    if ($return_val !== 0) {
        echo "\\n **VIDEO ERROR**\n", print_r($output, true);
        error_log($cmd . "\n" . print_r($output, true));
        if ($status == 'a') {
            $status = 'x' . $key;
        } else {
            $status = 'x';
        }
    } else {
        // update duration again
        echo "Updating Duration .{$key}";
        $video->updateDurationIfNeed(".{$key}");
        echo "\n {$key} Ok\n";
    }
}

if (empty($type) || $type == 'img') {
    //capture image
    echo "\n\n--Capture Image \n";
    $pathFileName = "{$global['systemRootPath']}videos/{$filename}.mp4";
    if (!file_exists($pathFileName)) {
        $pathFileName = "{$global['systemRootPath']}videos/{$filename}.webm";
    }
    if (!file_exists($pathFileName)) {
        $pathFileName = "{$global['systemRootPath']}videos/{$original_filename}";
    }

    $destinationFile = "{$global['systemRootPath']}videos/{$filename}.jpg";
    eval('$ffmpeg ="' . $config->getFfmpegImage() . '";');

    $cmd = "rm -f {$global['systemRootPath']}videos/{$filename}.jpg && {$ffmpeg}";
    echo "** executing command {$cmd}\n";
    exec($cmd . " < /dev/null 2>&1", $output, $return_val);
    if ($return_val !== 0) {
        echo "\\n**IMG ERROR**\n", print_r($output, true);
        error_log($cmd . "\n" . print_r($output, true));
    } else {
        echo "\nImage Ok\n";
    }
}

// remove original file
//echo "Remove Original File\n";
//$cmd = "rm -f {$global['systemRootPath']}videos/{$original_filename}";
//exec($cmd);
echo "\n\n--Save Status\n";

// save status
$id = $video->setStatus($status);
