function sleep(ms) {
    ms = ms || 99;
    setTimeout(function () {}, parseInt(ms));
}

function setMediaSession() {
    if (!('mediaSession' in navigator)) {
        return;
    }

    navigator.mediaSession.metadata = new MediaMetadata(
        window.jMovie.session
    );
}

function isBetween(value, min, max)
{
    return value >= min && value <= max;
}

function getLastChar(str)
{
    return str.substr(str.length - 1)
}

function rmLastChar(str)
{
    return str.slice(0, -1);
}

function isDevOrStaging()
{
    var domain = window.location.host.split(':')[0];

    return domain === 'localhost';
}

(function ($) {
    'use strict';

    setMediaSession();

    var
        KEY_SPACE = 32,
        KEY_ENTER = 13,
        KEY_ESC = 23,

        isInFullScreen = function isInFullScreen() {
            return (document.hasOwnProperty('fullscreen') && document.fullscreen) ||
                (window.hasOwnProperty('fullScreen') && window.fullScreen) ||
                (window.innerWidth === screen.width && window.innerHeight === screen.height);

        },

        openFullscreen = function openFullscreen(elem) {
            if (isInFullScreen()) {
                return false;
            }

            try {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.mozRequestFullScreen) { /* Firefox */
                    elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) { /* IE/Edge */
                    elem.msRequestFullscreen();
                }

                return true;
            } catch (e) {
                console.warn('openFullscreen', e);
            }

            return false;
        },

        closeFullscreen = function closeFullscreen() {
            if (!isInFullScreen()) {
                return false;
            }

            try {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) { /* Firefox */
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) { /* IE/Edge */
                    document.msExitFullscreen();
                }
            } catch (e) {
                console.warn('closeFullscreen(1)', e);
            }

            try {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            } catch (e) {
                console.warn('cancelFullScreen(2)', e);
            }
        }
    ;

    function incStat(url, episode, season) {
        if (isDevOrStaging()) {
            console.log('Do not save statistics for now');

            return false;
        }

        try {
            $.ajax({
                type: 'POST',
                async: true,
                url: url,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    episode: episode,
                    season: season
                }
            });
        } catch (e) {
            console.warn(e);
        }
    }

    $(function () {
        var
            loadingDelta = 0,
            defaultSleepTime = 333,
            animationTransition = 350,
            infoTextDelay = 999,
            skipTime = 25,
            elem = document.getElementById('movie-wrapper'),
            elAudio = document.getElementById('movie-audio'),
            elMovie = document.getElementById('movie'),
            $body = $('body'),
            $movie = $('#movie'),
            $movieControls = $('#movie-controls'),
            $movieCurrentTime = $('#movie-current'),
            $movieDurationTime = $('#movie-duration'),
            $movieWrapper = $('#movie-wrapper'),
            $movieLoading = $('#movie-loading-overlay'),
            $movieInfoText = $('#movie-info-text'),
            $movieProgressBar = $('#movie-progress-bar'),

            mappingPath = $movieWrapper.data('mapping'),
            movieMapping,
            movieAliases,

            currentMovieName = $movie.data('movie-part-name'),
            currentFileName = $movie.data('movie-file-name'),
            statStartUrl = $movie.data('statistic-start'),
            statEndUrl = $movie.data('statistic-end'),
            season = $movie.data('season'),
            choiceOptions = {},
            isFullscreen = false,
            isPlaying = false,
            playPromise = undefined,
            audioPromise = undefined,

            $choice = $('#movie-choice'),
            $choiceContent = $('#movie-choice-content'),

            $btnFullScreenOn = $('#movie-fullscreen'),
            $btnFullScreenOff = $('#movie-normalscreen'),
            $btnPlay = $('#movie-play'),
            $btnPause = $('#movie-pause'),

            $dbgName = $('#movie-debug-name'),
            $dbgOptions = $('#movie-debug-options'),

            getElMovie = function getElMovie() {
                if ('object' !== typeof $movie) {
                    $movie = $('#movie');
                }
                
                if ('object' !== typeof elMovie) {
                    elMovie = document.getElementById('movie');
                }

                return elMovie;
            },

            updateTimeDelta = function updateTimeDelta(newDelta) {
                loadingDelta = parseInt((loadingDelta + newDelta) / 2);
            },

            getSleepingTime = function getSleepingTime(koef) {
                koef = koef || 1;
                var delay = loadingDelta;

                if (delay < 10 || delay > 4000) {
                    return defaultSleepTime;
                }

                if (delay > 99) {
                    delay = delay * 3.5;
                } else if (delay > 200) {
                    delay = delay * 2.8;
                } else if (delay > 500) {
                    delay = delay * 1.5;
                }

                return parseInt(delay & koef);
            },

            addStatStart = function addStatStart() {
                // if ('?' !== getLastChar(statStartUrl)) {
                //     console.log('Add statistic on START');
                //     incStat(statStartUrl, currentMovieName, season);
                //     statStartUrl += '?';
                // }
            },
            
            addStatEnd = function addStatEnd() {
                // if ('?' !== getLastChar(statEndUrl)) {
                //     console.log('Add statistic on END');
                //     incStat(statEndUrl, currentMovieName, season);
                //     statEndUrl += '?';
                // }
            },

            debugThePart = function debugPart() {
                var options = '';

                if (choiceOptions.hasOwnProperty('text')) {
                    options += '<br/>Текст: ' + choiceOptions.text;
                }

                if (choiceOptions.hasOwnProperty('textA')) {
                    options += '<br/>Текст для в. А: ' + choiceOptions.textA;
                }

                if (choiceOptions.hasOwnProperty('textB')) {
                    options += '<br/>Текст для в. В: ' + choiceOptions.textB;
                }

                if (choiceOptions.hasOwnProperty('A')) {
                    options += '<br/>Вариант А: ' + choiceOptions.A;
                }

                if (choiceOptions.hasOwnProperty('B')) {
                    options += '<br/>Вариант B: ' + choiceOptions.B;
                }

                $dbgName.html(currentMovieName);
                $dbgOptions.html(options);
            },

            hideLoading = function hideLoading() {
                $movieLoading.hide();
            },

            showLoading = function showLoading() {
                $movieLoading.show();
            },

            showInfoText = function (txt) {
                $movieInfoText.html('<p>' + txt + '</p>').fadeIn();

                setTimeout(function () {
                    $movieInfoText.fadeOut(animationTransition, function() {
                        $movieInfoText.html('');
                    });
                }, infoTextDelay);
            },

            pauseMovie = function pauseMovie() {
                getElMovie();
                $btnPause.hide();
                $btnPlay.show();

                try {
                    if (playPromise !== undefined) {
                        playPromise.then(_ => {
                            // Automatic playback started!
                            // Show playing UI.
                            // We can now safely pause video...
                            elMovie.pause();
                        }).catch(error => {
                            // Auto-play was prevented
                            // Show paused UI.
                            console.error(error);
                        });
                    } else {
                        elMovie.pause();
                    }
                } catch (e) {
                    console.warn(e);
                }

                $movieControls.removeClass('is-playing').addClass('is-paused');
                isPlaying = false;
                showInfoText('Пауза');
            },

            playMovie = function playMovie() {
                console.log('PlayMovie');
                getElMovie();
                
                $btnPlay.hide();
                $btnPause.show();
                $movieLoading.hide();

                try {
                    setTimeout(function() {
                        playPromise = elMovie.play();
                    }, 1);
                } catch (e) {
                    console.error(e);
                }

                $movieControls.addClass('is-playing').removeClass('is-paused');
                isPlaying = true;


            },

            toggleMovie = function toggleMovie() {
                if (isPlaying) {
                    pauseMovie();
                } else {
                    playMovie();
                }
            },

            seekForwardMovie = function seekForwardMovie() {
                getElMovie();
                // elMovie.currentTime = Math.min(elMovie.currentTime + skipTime, elMovie.duration - 1);
                elMovie.currentTime = elMovie.duration - 1;
            },

            seekBackwardMovie = function seekBackwardMovie() {
                getElMovie();
                elMovie.currentTime = Math.max(elMovie.currentTime - skipTime, 0);
            },

            showChoice = function showChoice() {
                $choice.show();
                audioPromise = elAudio.play();
                $btnPause.hide();
                $btnPlay.hide();
            },

            hideChoice = function hideChoice() {
                $choice.hide();

                try {
                    if (audioPromise !== undefined) {
                        audioPromise.then(_ => {
                            // Automatic playback started!
                            // Show playing UI.
                            // We can now safely pause video...
                            elAudio.pause();
                        }).catch(error => {
                            // Auto-play was prevented
                            // Show paused UI.
                            console.error(error);
                        });
                    } else {
                        elAudio.pause();
                    }
                } catch (e) {
                    console.warn(e);
                }
            },

            checkAndLoadMapping = function () {
                var attempt = 0;

                while (
                    'object' !== typeof movieMapping &&
                    'object' !== typeof movieAliases &&
                    ++attempt < 9
                    ) {
                    initMapping();
                    sleep(getSleepingTime());
                }
            },

            getChoiceFromMapping = function getChoiceFromMapping(movieName) {
                checkAndLoadMapping();

                var hasFile = movieMapping.hasOwnProperty(movieName),
                    hasAlias = movieAliases ? movieAliases.hasOwnProperty(movieName) : false;

                currentMovieName = movieName;
                currentFileName = movieName;

                if (hasAlias) {
                    currentFileName = movieAliases[movieName];
                } else if (hasFile) {
                    console.log('Use the main file for this part: ', movieName);
                } else {
                    console.error('That part does not exist as file and as alias', movieName);
                    return;
                }


                choiceOptions = movieMapping[movieName];

                debugThePart();

                var
                    hasChoiceA = choiceOptions.hasOwnProperty('textA'),
                    hasChoiceB = choiceOptions.hasOwnProperty('textB') && choiceOptions.hasOwnProperty('B'),

                    choiceB = hasChoiceB ?
                        '<a class="textB btn btn-choice" data-next-movie="' + choiceOptions.B + '">' + choiceOptions.textB + '</a>' :
                        '',
                    choiceA = hasChoiceA ?
                        '<a class="textA btn btn-choice" data-next-movie="' + choiceOptions.A + '">' + choiceOptions.textA + '</a>' :
                        '',

                    html = '';

                if (hasChoiceA && hasChoiceB) {
                    html =
                        '<div class="movie-choice-text">' +
                        '<p>' + choiceOptions.text + '</p>' +
                        '</div>' +

                        '<div class="movie-choice-buttons">' +
                        choiceA +
                        choiceB +
                        '</div>';
                }

                $choiceContent.html(html);
            },

            initMapping = function initMapping() {
                var startTime;

                if ('object' !== typeof movieMapping) {
                    startTime = (new Date()).getTime();

                    $.ajax({
                        async: false,
                        type: 'GET',
                        url: mappingPath,
                        success: function (data) {
                            movieMapping = data;
                            window.movieMapping = movieMapping;///tmp
                            updateTimeDelta((new Date()).getTime() - startTime);
                        }
                    });
                }

                if ('object' !== typeof movieAliases) {
                    mappingPath = mappingPath.replace('mapping', 'aliases');
                    startTime = (new Date()).getTime();

                    $.ajax({
                        async: false,
                        type: 'GET',
                        url: mappingPath,
                        success: function (data) {
                            movieAliases = data;
                            window.movieAliases = movieAliases;///tmp
                            updateTimeDelta((new Date()).getTime() - startTime);
                        }
                    });
                }
            },

            _reloadVideoSrc = function(fileName) {
                $movie.attr('src', fileName);
                $("#movie-wrapper video")[0].load();
                getElMovie();
                elMovie.load();
            },

            changeMovie = function changeMovie(fileName) {
                var hasFile = movieMapping.hasOwnProperty(fileName),
                    hasAlias = movieAliases.hasOwnProperty(fileName);

                $movie.data('movie-part-name', fileName);

                if (hasAlias) {
                    fileName = movieAliases[fileName];
                }

                $movie.data('movie-file-name', fileName);
                fileName = jMovie.uri + fileName + '.mp4';

                if ('?' === getLastChar(statStartUrl)) {
                    statStartUrl = rmLastChar(statStartUrl);
                }

                if ('?' === getLastChar(statEndUrl)) {
                    statEndUrl = rmLastChar(statEndUrl);
                }

                try {
                    _reloadVideoSrc(fileName);
                } catch (e) {
                    setTimeout(function () {
                        _reloadVideoSrc(fileName);
                    }, getSleepingTime(2));
                }
            },

            playNextMovie = function playNextMovie() {
                setTimeout(function () {
                    playMovie();
                }, getSleepingTime(3));
            },

            _hasChoiceScreen = function () {
                if (0 === Object.keys(choiceOptions).length) {
                    return true;
                }

                return choiceOptions.hasOwnProperty('B');
            },

            _isEndOfPlot = function () {
                if (choiceOptions.hasOwnProperty('A')) {
                    return choiceOptions.A.startsWith('end');
                }

                return false;
            },

            onTrackedVideoFrame = function onTrackedVideoFrame(currentTime, duration) {
                function formatTime(time) {
                    time = parseInt(time);

                    var out = ':00',
                        minutes = Math.floor(time / 60),
                        seconds = time % 60;

                    if (seconds < 10) {
                        seconds = '0' + seconds;
                    } else if (!seconds) {
                        seconds = '00';
                    }

                    out = seconds + out;

                    if (minutes > 0) {
                        out = minutes + ':' + out;
                    }

                    return out;
                }

                $movieCurrentTime.text(formatTime(currentTime));
                $movieDurationTime.text(formatTime(duration));

                if (parseFloat(duration) > 0) {
                    var percent = 100 * currentTime/duration;
                    $movieProgressBar
                        .css('width', percent + '%')
                        .show();
                } else {
                    $movieProgressBar.hide();
                }
            },

            canEnterFullScreen = false,
            canExitFullScreen = false,
            canDetectFullScreen = false,
            useFullScreen = false,

            fullScreeAnalitics = function fullScreeAnalitics() {
                if ('requestFullscreen' in elMovie) {
                    canEnterFullScreen = 'requestFullscreen'; // W3C proposal
                }
                else if ('requestFullScreen' in elMovie) {
                    canEnterFullScreen = 'requestFullScreen'; // mozilla proposal
                }
                else if ('webkitRequestFullScreen' in elMovie) {
                    canEnterFullScreen = 'webkitRequestFullScreen'; // webkit
                }
                else if ('mozRequestFullScreen' in elMovie) {
                    canEnterFullScreen = 'mozRequestFullScreen'; // firefox
                }

                // support for exiting fullscreen
                if ('exitFullscreen' in document) {
                    canExitFullScreen = 'exitFullscreen'; // W3C proposal
                }
                else if ('cancelFullScreen' in document) {
                    canExitFullScreen = 'cancelFullScreen'; // mozilla proposal
                }
                else if ('webkitCancelFullScreen' in document) {
                    canExitFullScreen = 'webkitCancelFullScreen'; // webkit
                }
                else if ('mozCancelFullScreen' in document) {
                    canExitFullScreen = 'mozCancelFullScreen'; // firefox
                }

                // support for detecting when in fullscreen
                if ('fullscreen' in document) {
                    canDetectFullScreen = 'fullscreen'; // W3C proposal
                }
                else if ('fullScreen' in document) {
                    canDetectFullScreen = 'fullScreen'; // mozilla proposal
                }
                else if ('webkitIsFullScreen' in document) {
                    canDetectFullScreen = 'webkitIsFullScreen'; // webkit
                }
                else if ('mozFullScreen' in document) {
                    canDetectFullScreen = 'mozFullScreen'; // firefox
                }

                if (false !== canEnterFullScreen && false !== canExitFullScreen) {
                    useFullScreen = canDetectFullScreen ? 1 : 2;
                }
            },

            initMovie = function initMovie() {
                console.log('Init movie');
                getElMovie();
                elMovie.load();
                currentMovieName = $movie.data('movie-part-name');
                currentFileName = $movie.data('movie-file-name');
                $movieInfoText.hide();

                elAudio.load();
                elAudio.pause();
                fullScreeAnalitics();

                elMovie.addEventListener('contextmenu', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);

                // hide the controls if they're visible
                if (elMovie.hasAttribute('controls')) {
                    elMovie.removeAttribute('controls');
                }

                function _openFullscreen() {
                    openFullscreen(elem);
                    $btnFullScreenOn.hide();
                    $btnFullScreenOff.show();
                    isFullscreen = true;
                    $body.addClass('full-scr');
                }

                function _closeFullscreen() {
                    closeFullscreen();
                    $btnFullScreenOn.show();
                    $btnFullScreenOff.hide();
                    isFullscreen = false;
                    $body.removeClass('full-scr');
                }

                if (false === useFullScreen) {
                    $btnFullScreenOn.hide();
                    $btnFullScreenOff.hide();
                } else {
                    $btnFullScreenOn.on('click', function (event) {
                        event.stopPropagation();
                        _openFullscreen();
                    });

                    $btnFullScreenOff.on('click', function (event) {
                        event.stopPropagation();
                        _closeFullscreen();
                    });
                }



                $btnPause.on('click', function (event) {
                    event.stopPropagation();
                    pauseMovie();
                });

                $btnPlay.on('click', function (event) {
                    event.stopPropagation();
                    playMovie();
                });

                $movie.on('touchend', function (event) {
                    event.stopPropagation();
                    event.preventDefault();
                    toggleMovie();
                });


                $('#movie-start').on('click', function (event) {
                    console.log('Movie start');
                    event.stopPropagation();
                    $(this).hide().remove();
                    $movieLoading.find('img').fadeOut(animationTransition, function () {
                        $(this).remove();
                    });
                    $movieLoading.find('svg').removeAttr('style');
                    $movie.removeAttr('poster');

                    setTimeout(function () {
                        playMovie();
                    }, getSleepingTime(3));
                });

                $('#movie-forward').on('click', function (event) {
                    event.stopPropagation();
                    seekForwardMovie();
                    showInfoText('Перемотка >>');
                });

                $('#movie-backward').on('click', function (event) {
                    event.stopPropagation();
                    seekBackwardMovie();
                    showInfoText('<< Перемотка');
                });


                $(window).keypress(function(e) {
                    if (e.which === KEY_SPACE) {
                        e.preventDefault();

                        if ($choice.is(':hidden')) {
                            toggleMovie();
                        }
                    } else if (e.which === KEY_ESC) {
                        if (isFullscreen) {
                            e.preventDefault();
                            _closeFullscreen();
                        }
                    } else if (e.which === KEY_ENTER) {
                        if (!isPlaying) {
                            playMovie();
                        }
                    }
                });

                /*document.addEventListener("fullscreenchange", function (event) {
                    isFullscreen = !! document.fullscreenElement;
                });*/



                $movie.on('loadstart', function () {
                    console.log('Video loadstart.');

                    if (isPlaying) {
                        showLoading();
                    }
                }).on('load', function () {
                    console.log('Video load.');

                    if (isPlaying) {
                        showLoading();
                    }
                }).on('loadeddata', function () {
                    console.log('Video loadeddata.');

                    if (isPlaying) {
                        hideLoading();
                    }
                }).on('ended', function () {
                    // $movieWrapper.on('ended', 'video', function () {
                    console.log('Video ended.');

                    if (_hasChoiceScreen()) {
                        pauseMovie();
                        showChoice();
                    } else if (_isEndOfPlot()) {
                        console.log('End', choiceOptions.A);

                        $movieWrapper.append('<a href="/movie" class="btn btn-lg btn-primary btn-start" title="Проиграть ещё раз"><i class="fa fa-play"></i> <span>Ещё раз</span></a>');
                        currentMovieName = '1';
                        currentFileName = '1';
                        $movie.data('movie-part-name', currentMovieName);
                        $movie.data('movie-file-name', currentFileName);
                        changeMovie(currentMovieName);
                        $btnPlay.hide();
                        $btnPause.hide();

                        if (isFullscreen) {
                            _closeFullscreen();
                        }

                        // initMovie();

                        $movieLoading.prepend('<img class="poster" src="/movie2020/poster.jpg" alt="">');

                        setTimeout(function () {
                            $movieLoading.removeAttr('style');
                            $movieLoading.show();
                        }, defaultSleepTime);

                    } else {
                        getChoiceFromMapping(choiceOptions.A);
                        changeMovie(currentMovieName);
                        playNextMovie();
                    }
                }).on('timeupdate', function () {
                    onTrackedVideoFrame(this.currentTime, this.duration);

                    if (isBetween(this.currentTime, 5, 6)) {
                        addStatStart();
                    } else if (isBetween(this.duration - this.currentTime, 5, 6)) {
                        addStatEnd();
                    }
                });

                $movieWrapper.on('click', '.btn-choice', function (event) {
                    event.stopPropagation();
                    showLoading();
                    getChoiceFromMapping($(this).data('next-movie'));
                    changeMovie(currentMovieName);
                    playNextMovie();
                    hideChoice();
                });
            };

        checkAndLoadMapping();
        initMovie();

        setTimeout(function() {
            getChoiceFromMapping(currentMovieName);
        }, getSleepingTime(4));

    });
})(jQuery);
