/*const videoPlayer = document.querySelector("#video-player"),
mainVideo = videoPlayer.querySelector("#main-video"),
progressAreaTime = videoPlayer.querySelector(".progressAreaTime"),
controls = videoPlayer.querySelector(".controls"),
progressArea = videoPlayer.querySelector(".progress-area"),
progressBar = videoPlayer.querySelector(".progress-bar"),
fastRewind = videoPlayer.querySelector(".fast-rewind"),
playPause = videoPlayer.querySelector(".play_pause"),
fastForward = videoPlayer.querySelector(".fast-forward"),
volume = videoPlayer.querySelector(".volume"),
volumeRange = videoPlayer.querySelector(".volume_range"),
timer = videoPlayer.querySelector(".timer"),
current = videoPlayer.querySelector(".current"),
totalDuration = videoPlayer.querySelector(".duration"),
autoPlay = videoPlayer.querySelector(".auto-play"),
settingsBtn = videoPlayer.querySelector(".settingsBtn"),
pictureInPicutre = videoPlayer.querySelector(".picture_in_picutre"),
fullscreen = videoPlayer.querySelector(".fullscreen"),
settings = videoPlayer.querySelector("#settings"),
playBack = videoPlayer.querySelectorAll(".playback li");
//let pause = document.getElementById("pause");*/

/*mainVideo.addEventListener('click', ()=>{
    const isVideoPaused = videoPlayer.classList.contains("paused");

    if (isVideoPaused) {
        pauseVideo()
    }else{
        playVideo()
    }    
});

function playVideo() {
    playPause.innerHTML = "pause";
    playPause.title = "pause";
    videoPlayer.classList.add("paused");
    mainVideo.play()
}
function pauseVideo() {
    playPause.innerHTML = "play_arrow";
    playPause.title = "play";
    videoPlayer.classList.remove("paused");
    mainVideo.pause()
}
playPause.addEventListener('click', ()=>{
    const isVideoPaused = videoPlayer.classList.contains("paused");

    if (isVideoPaused) {
        pauseVideo()
    }else{
        playVideo()
    }
    
});

mainVideo.addEventListener('play', ()=>{
    if (videoPlayer.classList.contains('openFullScreen')) {
        controls.classList.remove("active");
    }
    playVideo()
});
mainVideo.addEventListener('pause', ()=>{
    if (videoPlayer.classList.contains('openFullScreen')) {
        controls.classList.add("active");
    }
    pauseVideo()
});

fastRewind.addEventListener('click', ()=>{
    mainVideo.currentTime -= 10 
});
fastForward.addEventListener('click', ()=>{
    mainVideo.currentTime += 10 
});

mainVideo.addEventListener('loadeddata', (e)=>{
    let videoDuration = e.target.duration;
    let totalMin = Math.floor(videoDuration / 60);
    let totalSec = Math.floor(videoDuration % 60);

    totalSec < 10 ? totalSec = "0"+totalSec:totalSec;
    totalDuration.innerHTML = totalMin+":"+totalSec;
});

mainVideo.addEventListener('timeupdate', (e)=>{
    let currentVideoTime = e.target.currentTime;
    let currentMin = Math.floor(currentVideoTime / 60);
    let currentSec = Math.floor(currentVideoTime % 60);

    currentSec < 10 ? currentSec = "0"+currentSec:currentSec;
    current.innerHTML = currentMin+":"+currentSec;
    let videoDuration = e.target.duration;

    //ProgressBar change
    let progress = (currentVideoTime / videoDuration)*100;
    progressBar.style.width = progress+"%"

    //ajax
    const video_id = $("#video_id").attr("data-videoId");
    const student_id = $("#student_id").attr("data-student");
    const course_id = $("#course_id").attr("data-course");


    /*$.ajax({
        method: "POST",
        url: '',
        data:{
          ajax:true,
          time_whatched: currentVideoTime,
          video_id: video_id,
          student_id: student_id,
          course_id: course_id,
          time: videoDuration,
        }
      }).
      done((response)=>{
        //console.log(response);
      }).
      fail(function (response){
        console.log("Error: ");
        console.log(response);
      })*/
//});

/*progressArea.addEventListener('click', (e)=>{
    let videoDuration = mainVideo.duration;
    let progressWidthVal = progressArea.clientWidth;
    let clickOffsetX = e.offsetX;
    mainVideo.currentTime = (clickOffsetX / progressWidthVal)*videoDuration;
});

function changeVolume() {
    mainVideo.volume = volumeRange.value / 100;
    if (volumeRange.value == 0) {
        volume.innerHTML = "volume_off";
    }else if (volumeRange.value < 40){
        volume.innerHTML = "volume_down";
    }else{
        volume.innerHTML = "volume_up";
    }
}*/

//const video_id = $("#video_id").attr("data-href");
$(".video_link").on("click",function (e) {

    const video_link = $(this).attr("data-href");
    const test_link = $(this).attr("data-test");
    const video_id = $(this).attr("data-videoId");
    const course_id = $(this).attr("data-course");
    const student_id = $(this).attr("data-studentId");
    const beforeVideoId = $(this).attr("data-beforeVideoId");

    $.ajax({
        method: "GET",
        url: '',
        data:{
          ajax_verif_vid:true,
          video_id: video_id,
          student_id: student_id,
          course_id: course_id,
          beforeVideoId: beforeVideoId,
        }
    }).
      done(function(response){
        //console.log(response);
        if(parseInt(response) == 1) {
            window.location.href=video_link;
        }else if (parseInt(response) == 2) {
            alert("Aula bloqueiada, termine de assistir as aulas anteriores e resolver seus testes");
        }else if(parseInt(response) == 0){
            window.location.href=test_link;
            localStorage.setItem('route', video_link);
            localStorage.setItem('video_id', video_id);
            localStorage.setItem('course_id', course_id);
            localStorage.setItem('student_id', student_id);
        }else{
            alert("Aula bloqueiada, termine de assistir as aulas anteriores e resolver seus testes!");
        }
      }).
      fail(function (response){
        console.log("Error: ");
        console.log(response);
      })
});

function muteVolume() {

    if (volumeRange.value == 0) {
        volumeRange.value = 80;
        mainVideo.volume = 0.8;
        volume.innerHTML = "volume_up";
    }else{
        volumeRange.value = 0;
        mainVideo.volume = 0;
        volume.innerHTML = "volume_off";
    }
}

/*volumeRange.addEventListener('change', ()=>{
    changeVolume();
});

volume.addEventListener('click', ()=>{
    muteVolume();
});

progressArea.addEventListener('mousemove', (e)=>{
    let progressWidthVal = progressArea.clientWidth;
    let x = e.offsetX;
    progressAreaTime.style.setProperty('--x',(x+"px"));
    progressAreaTime.style.display = "block";

    let videoDuration = mainVideo.duration;
    let progressTime = Math.floor((x / progressWidthVal)*videoDuration);
    let currentMin = Math.floor(progressTime / 60);
    let currentSec = Math.floor(progressTime % 60);

    currentSec < 10 ? currentSec = "0"+currentSec:currentSec;
    progressAreaTime.innerHTML = currentMin+":"+currentSec;
});

progressArea.addEventListener('mouseleave', (e)=>{
    progressAreaTime.style.display = "none";
});

autoPlay.addEventListener('click', ()=>{
    autoPlay.classList.toggle('active');
    if (autoPlay.classList.contains('active')) {
        autoPlay.title = 'AutoPlay está ligado'
    }else {
           autoPlay.title = 'AutoPlay não está ligado'
    }
});

mainVideo.addEventListener('ended', ()=>{
    const video_id = $("#video_id").attr("data-videoId");
    const student_id = $("#student_id").attr("data-student");
    const course_course_id = $("#course_course_id").attr("data-course");

    if (autoPlay.classList.contains('active')) {
        playVideo()
    }else {
        playPause.innerHTML = 'replay'
        playPause.title = 'Repetir'
    }
});

pictureInPicutre.addEventListener('click', ()=>{
    mainVideo.requestPictureInPicture();
});

fullscreen.addEventListener('click', ()=>{

    if (!videoPlayer.classList.contains('openFullScreen')) {
        videoPlayer.classList.add('openFullScreen');
        fullscreen.innerHTML = 'fullscreen_exit';
        videoPlayer.requestFullscreen();
        setTimeout(()=>{
            controls.classList.remove('active');
        },5000);
    }else{
        videoPlayer.classList.remove('openFullScreen');
        fullscreen.innerHTML = 'fullscreen';
        document.exitFullscreen();
    }
    
});

settingsBtn.addEventListener('click', ()=>{
    settings.classList.toggle('active');
    settingsBtn.classList.toggle('active');

});

playBack.forEach(event => {
    event.addEventListener('click', ()=>{
        removeActiveClasses();
        event.classList.add('active');
        let speed = event.getAttribute('data-speed');
        mainVideo.playbackRate = speed;
    });
});

function removeActiveClasses() {
    playBack.forEach(event => {
        event.classList.remove('active');
    });
}*/

/*window.addEventListener('unload', ()=>{
    let myId = $("#myId").attr("data-myId");
    localStorage.setItem('duration'+myId, (mainVideo.currentTime+""));
    localStorage.setItem('src'+myId, mainVideo.getAttribute('src')+"");
    
});

window.addEventListener('load', ()=>{
    let myId = $("#myId").attr("data-myId");
    let getDuration = localStorage.getItem('duration'+myId);
    let getSrc = localStorage.getItem('src'+myId);
    if (getSrc) {
        if(mainVideo.getAttribute('src') == getSrc){
            mainVideo.src = getSrc;
            mainVideo.currentTime = getDuration;
        }
    }
    const video_link = window.location.href;
});

mainVideo.addEventListener('contextmenu', (e)=>{
    e.preventDefault();
});

videoPlayer.addEventListener('mouseover', ()=>{
    controls.classList.add('active');
        
});

videoPlayer.addEventListener('mousemove', ()=>{
    
    if (videoPlayer.classList.contains('openFullScreen')) {
        //controls.classList.remove("active");
        controls.classList.add('active');
    }
});
controls.addEventListener('mouseleave', ()=>{

    if (videoPlayer.classList.contains('openFullScreen')) {
        setTimeout(()=>{
            controls.classList.remove('active');
        },5000);
    }

});

videoPlayer.addEventListener('mouseleave', ()=>{

    if (videoPlayer.classList.contains('paused')) {
        if (settingsBtn.classList.contains('active')) {
            controls.classList.add("active");
        }else{
            controls.classList.remove("active");
        }
    }else{
        controls.classList.add('active');
    }

});

if (videoPlayer.classList.contains('paused')) {
    if (settingsBtn.classList.contains('active')) {
        controls.classList.add("active");
    }else{
        controls.classList.remove("active");
    }
}else{
    controls.classList.add('active');
}

//Control para mobile
videoPlayer.addEventListener('touchstart', ()=>{
    controls.classList.add('active');
    setTimeout(()=>{
        controls.classList.remove('active');
    },8000);
});

videoPlayer.addEventListener('touchmove', ()=>{

    if (videoPlayer.classList.contains('paused')) {
        controls.classList.remove("active");
    }else{
        controls.classList.add('active');
    }
});
*/