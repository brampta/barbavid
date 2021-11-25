$(document).ready(function() {
    
    console.log('dotdot fires');
    
    //$(".video_title_container").dotdotdot({
    //    // configuration goes here
    //});
    
    
    $('.video_title_container a').ellipsis({
        row: 2
    });
});
