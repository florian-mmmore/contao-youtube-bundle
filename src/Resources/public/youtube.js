var videoReplaceList = {};

// function to loop through all the video containers and show the actual video
var toggleVideos = function (show = true) {
    var containers = document.getElementsByClassName('js-video-replace-container');
    for (var i = 0; i < containers.length; i++) {
        var accept = containers[i].querySelector('.js-video-accept-container');
        var link = containers[i].querySelector('.js-video-link');

        // show the video
        if (show) {
            createIframe(containers[i], accept, link);
        } else {
            showAcceptContainer(containers[i]);
        }
    }
};

// function to replace the videoAcceptContainter with an iframe
var createIframe = function (container, accept, link) {
    if (!container || !accept || !link) {
        return;
    }

    // save the accept container
    videoReplaceList[container.id] = accept;

    var iframe = document.createElement('iframe');
    iframe.src = link.href;
    iframe.width = link.dataset.width;
    iframe.height = link.dataset.height;
    iframe.setAttribute('allowfullscreen', '');
    container.replaceChild(iframe, accept);
};

// function to replace the iframe with a videoAcceptContainer
var showAcceptContainer = function (container) {
    if (!container) {
        return;
    }
    
    var accept = videoReplaceList[container.id];
    var iframe = container.querySelector('iframe');
    if (!accept || !iframe) {
        return;
    }
    
    // show the accept container instead the iframe
    container.replaceChild(accept, iframe);
};

(function () {
    var videoLinks = document.getElementsByClassName('js-video-link');

    // stop the code if there are no video links
    if (!videoLinks) {
        return;
    }

    // check if the variable in the session storage is set
    if (window.sessionStorage.getItem('acceptVideoLoad') === 'accepted') {
        toggleVideos(true);
    }

    // global click event on document and check if clicked element is an accept link
    document.addEventListener('click', function (e) {
        if (!e.target || !e.target.classList.contains('js-video-link')) {
            return;
        }

        e.preventDefault();

        // save in session storage that the videos are allowed to be loaded
        window.sessionStorage.setItem('acceptVideoLoad', 'accepted');
        toggleVideos(true);
    });
})(); // call the function
