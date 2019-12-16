# YouPHPTube-lvk
**About levelKro version**
Only add few fix and little feature upgrade to work better in standalone. All the rest is the same of YouPHPTube 3.4.1-master.


YouPHPTube! is an video-sharing website, It is an open source solution that is freely available to everyone. With YouPHPTube you can create your own video sharing site, YouPHPTube will help you import and encode videos from other sites like Youtube, Vimeo, etc. and you can share directly on your website. In addition, you can use Facebook or Google login to register users on your site. The service was created in march 2017.

# Server Requirements

In order for you to be able to run YouPHPTube, there are certain tools that need to be installed on your server. Don't worry, they are all FREE. To have a look at complete list of required tools, click the link below.

- Linux (Kernel 2.6.32+)
- PHP 5.3+ (Below 7.2)
- MySQL 5.0+
- Apache web server 2.x (with mod_rewrite enabled)

If you are not sure how to install one of these tools take a look on this http://tutorials.youphptube.com/video/how-to-install-youphptube-in-a-fresh-ubuntu-server


# Versions details

## Ver 3.4.1-lvk
- Limiting instances of FFMpeg, use with `cpulimit --lazy --quiet --foreground  -l 60 -- ` before the `ffmpeg command` (in Advanced settings) for limit _at 2 ffmpeg running at 60% of the cpu in the same time_. (config for dual core server). Edit `/view/mini-upload-form/videoEncoder.php` for customizing this settings.
- Fix special font in the wrong directory
- Background image for Audio Waveform, use this command for ffmpeg (in Advanced settings); `ffmpeg -loop 1 -i <your_path>/view/img/audio_wave_bg.jpg -i {$pathFileName} -filter_complex '[1:a]showwaves=s=1280x720:mode=line:colors=SteelBlue:split_channels=1,colorkey=0x000000:0.01:0.1,format=rgba[v];[0:v][v]overlay[outv]' -map '[outv]' -pix_fmt yuv420p  -map 1:a -c:v libx264 -shortest -c:a copy {$destination}`. This make also the audio visual blue (previous was red).

_Know issues_ When a video is waitting for encoding (2 ffmpeg running), the icon about the state of encoding flash 0%-100% (when is re-encode). It's normal. No output file was created for the encoding and the PHP code is not updated for that, but this flash effect are usefull for make it 'on standby'.


**LVK-branch** This is the same version from 3.4.1, but with few upgrade and fix.

## Ver 3.4.1
- Latest official standalone version

## Ver 3.4
- Add YouTube Video Upload Support.
Video Tutorial https://tutorials.youphptube.com/video/enable-google-auth-and-youtube-uploader-ver-3-4

## Ver 3.3
- Add SMTP Mail Support

## Ver 3.2
- Add Playlists Support
- Add Channels Support

## Ver 3.1
- Add Themes Support
https://tutorials.youphptube.com/video/how-to-change-the-theme-of-youphptube-ver-3-1

## Ver 3.0
- Add the Subscription Option

## Ver 2.9
*Special Thanks for Frank de Lange. He is responsible for the creation of these functionalities*
- Add video rotate command.
- Add command line interface to upload videos. Use php -f upload.php -- -h in view/mini-upload-form for instructions.

## Ver 2.8
- Option to enable and disable features such as: (Coding formats, MP4, WEBM and creation of Spectrum for MP3)
- Added Thumbs from the videos to the Ads management area
- Displays static image while the video is not encoded and the image was not generated
- Option to specify how long the user session will expire
- Choose if the video to start will play automatically
- Option on the main screen for the video to continue playing the next videos

## Ver 2.7
- Independent Video Advertising System to Avoid Meddling by Google (Thanks for the idea Simon Christopher)
- Choose the fist style, Youtube video Play or Gallery
- Now MP3 files create a spectrum video, audio now will play like a video, watch an sample: http://demo.youphptube.com/video/mark-ronson-uptown-funk-ft-bruno-mars

## Ver 2.6
**We will help you make money with google AdSense** watch this video http://tutorials.youphptube.com/video/make-money-with-youphptube-enable-google-adsense

## Ver 2.4
- User and Videos Groups
- Private Videos

**Private videos since version 2.4** YouPHPTube allows you to determine if the videos published on the page will be public or private.
By default all videos are public, but you can create groups and link them to videos and users, so only users in the group can view videos from the same group. And the linked video will not be public any more.
We have made a video to help you: http://tutorials.youphptube.com/video/enable-facebook-login-and-making-a-video-private

## Ver 2.0
- Change Comments Layout (Show user Picture now)
- You can download and encode videos from other web sites (Youtube, Vimeo, etc), to share on yours
- You can enable Facebook or Google login on configuration menu, also set up what logged users can do (Comment and Upload/Download videos).
