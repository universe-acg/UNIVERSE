/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

// css & scss

require('../css/app.scss');
require('../css/public.scss');
require('pace-progressbar/themes/orange/pace-theme-minimal.css');
require('fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('../vendor/owlcarousel/assets/owl.carousel.min.css');
require('../vendor/owlcarousel/assets/owl.theme.default.min.css');
require('../vendor/jssocials/jssocials.css');
require('../vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css');
require('pnotify/dist/PNotifyBrightTheme.css');
require('jquery-confirm/dist/jquery-confirm.min.css');
require('./select2/select2.css');
require('summernote/dist/summernote-bs4.css');
require('../vendor/select2/select2-bootstrap4.min.css');
require('../vendor/photoswipe/photoswipe.css');
require('../vendor/photoswipe/default-skin/default-skin.css');
require('nouislider/distribute/nouislider.min.css');
require('@adactive/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
require('jquery-datetimepicker/build/jquery.datetimepicker.min.css');
require('snazzy-info-window/dist/snazzy-info-window.min.css');
require('animate.css/animate.min.css');
require('material-scrolltop/dist/material-scrolltop.css');
require('../vendor/jquery.cookieBar/jquery.cookieBar.min.css');

// js

const $ = require('jquery');
global.$ = global.jQuery = $;
require('../js/bootstrap.bundle.min.js');
import PNotify from 'pnotify/dist/es/PNotify.js';
import 'jquery-confirm/dist/jquery-confirm.min.js';
import './select2/select2.js';
import 'summernote/dist/summernote-bs4.min.js';
import noUiSlider from 'nouislider/distribute/nouislider.min.js';
import wNumb from 'wnumb/wNumb.js';
import Readmore from 'readmore-js/dist/readmore.js';
import Blazy from 'blazy/blazy.min.js';
import Ouical from 'add-to-calendar-buttons/ouical.min.js';
import Bloodhound from 'typeahead.js/dist/bloodhound.min.js';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import KnpPaginatorAjax from '../vendor/knppaginator-ajax/knppaginator-ajax.js';
import caleandar from '../vendor/caleandar.js/caleandar.js';
import bootbox from '../vendor/bootboxjs/bootbox.min';

require('pace-progressbar');
require('fontawesome-iconpicker');
require('../vendor/owlcarousel/owl.carousel.min.js');
require('../vendor/jssocials/jssocials.min.js');
require('../vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js');
require('../vendor/morphext/morphext.min.js');
require('waypoints/lib/jquery.waypoints.min.js');
require('../vendor/jquery.countdown/jquery.countdown.min.js');
require('../vendor/counter-up/jquery.counterup.min.js');
require('../vendor/circle-progress/circle-progress.min.js');
global.PhotoSwipe = require('../vendor/photoswipe/photoswipe.min.js');
global.PhotoSwipeUI_Default = require('../vendor/photoswipe/photoswipe-ui-default.min.js');
require('../vendor/jq-photoswipe/jqPhotoSwipe.min.js');
require('../vendor/ninsuo-symfony-collection/jquery.collection.js');
require('../vendor/pugxautocompleter/autocompleter-select2.js');

require('@adactive/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
require('jquery-datetimepicker/build/jquery.datetimepicker.full.min.js');

require('typeahead.js/dist/typeahead.jquery.min.js');
const routes = require('../../public/bundles/fosjsrouting/js/fos_js_routes.json');

Routing.setRoutingData(routes);

require('moment-timezone/builds/moment-timezone-with-data.min.js');

require('jquery-parallax.js/parallax.min.js');

require('material-scrolltop/dist/material-scrolltop.js');
$(document).on('click', '.requires-confirmation-normal', function (e) {
    e.preventDefault();
    var $thisElement = $(this);

    if (typeof $thisElement.attr('data-target') !== typeof undefined && $thisElement.attr('data-target') !== false) {
        // Get the value of the data-target attribute
        var dataTarget = $thisElement.attr('data-target');

        // Check if the data-target attribute contains '/delete-permanently'
        if (dataTarget.indexOf('/delete-permanently') >= 0) {

            const $confirmDialog = $.confirm({
                title: 'Confirmation required',
                content: $thisElement.data('confirmation-text'),
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary btn-sm',
                        keys: ['enter'],
                        action: function () {
                            location.href = $thisElement.data('target');
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-default btn-sm',
                        keys: ['esc'],
                        action: function () {
                            $confirmDialog.close();
                        }
                    }
                }
            });
        }
    }
});


$(document).on('click', '.requires-confirmation-repeating', function (e) {
        e.preventDefault();
        var $thisElement = $(this);
        if (typeof $thisElement.attr('data-target') !== typeof undefined && $thisElement.attr('data-target') !== false) {
            // Get the value of the data-target attribute
            var dataTarget = $thisElement.attr('data-target');

            // Check if the data-target attribute contains '/delete-permanently'
            if (dataTarget.indexOf('/delete-permanently') >= 0) {
                const $confirmDialog = $.confirm({
                    title: 'Confirmation required',
                    content: $thisElement.data('confirmation-text'),
                    buttons: {
                        confirm: {
                            text: 'Confirm',
                            btnClass: 'btn-primary btn-sm',
                            keys: ['enter'],
                            action: function () {
                                location.href = $thisElement.data('target');
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            btnClass: 'btn-default btn-sm',
                            keys: ['esc'],
                            action: function () {
                                $confirmDialog.close();
                            }
                        }
                    }
                });
            }
        }
    }
)
$(document).ready(function () {
    // Declares utility functions

    // Sets the language for the date pickers
    // $.datetimepicker.setLocale('en');

    // Retreives a parameter value passed in the url
    function getURLParameter(paramatername) {
        return decodeURIComponent((new RegExp('[?|&]' + paramatername + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || null;
    }

    // Retreives an array parameter value passed in the url
    function getURLArrayParameter(paramatername) {
        paramatername = encodeURIComponent(paramatername);
        var match = window.location.href.match(/[^=&?]+\s*=\s*[^&#]*/g);
        var obj = {};
        for (var i = match.length; i--;) {
            var spl = match[i].split("=");
            var name = spl[0].replace("[]", "");
            var value = spl[1];
            obj[name] = obj[name] || [];
            obj[name].push(value);
        }
        if (obj[paramatername] !== undefined) {
            return obj[paramatername];
        } else {
            return null;
        }
    }

    // Checks if an email address is valid
    function isEmailValid(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    // Animates an element using the animate.css library
    function animateCSS(element, animationName, callback) {
        const node = document.querySelector(element)
        node.classList.add('animated', animationName)

        function handleAnimationEnd() {
            node.classList.remove('animated', animationName)
            node.removeEventListener('animationend', handleAnimationEnd)

            if (typeof callback === 'function')
                callback()
        }

        node.addEventListener('animationend', handleAnimationEnd)
    }

    global.getURLParameter = getURLParameter;
    global.getURLArrayParameter = getURLArrayParameter;

// Initializes bootstrap components
    $('[data-toggle="popover"], .has-popover').popover();
    $('[data-toggle="tooltip"], .has-tooltip').tooltip({
        trigger: 'hover'
    });

    // Sets input file name
    $(document).on('change', '.custom-file-input', function (e) {
        $(e.target).siblings('.custom-file-label').html(e.target.files[0].name);
    });

    // Resets forms
    $('button[type="reset"]').on('click', function (e) {
        $(this).closest('form').find('input[type="text"], input:password, input:file, textarea').val('');
        $(this).closest('form').find('input[type="text"], input:password, input:file, textarea').attr('value', '');
        $(this).closest('form').find('.autocomplete').each(function () {
            $('#fake_' + $(this).attr('id')).select2('data', {});
        });
        $(this).closest('form').find('select').val("all").trigger('change');
        $(this).closest('form').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
    });

    // Fixes disabled links with a tooltip
    $("a.btn.disabled.has-tooltip").click(function (e) {
        e.preventDefault();
        return false;
    });

// Prevents closing from click inside dropdown
    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
    });
    if ($(window).width() > 768) {
        $(window).scroll(function () {
// Fixes menu on scroll for desktop
            if ($('.navbar-landing').length) {
                if ($(this).scrollTop() > 125) {
                    $('.navbar-landing').addClass("fixed-top");
                } else {
                    $('.navbar-landing').removeClass("fixed-top");
                }
            }

// Sticky sidebar padding
            if ($('.sticky-sidebar').length) {
                if ($('.sticky-sidebar').offset().top <= $(this).scrollTop()) {
                    $('.sticky-sidebar').addClass('pt-lg-6');
                } else {
                    $('.sticky-sidebar').removeClass('pt-lg-6');
                }
            }
        });
    }

// Closes the Responsive Menu on Menu Item Click
    $('.navbar-collapse ul li a.page-scroll').click(function () {
        $('.navbar-toggler:visible').click();
    });

    // Handles top search form
    $(".header-main form.search-wrap").submit(function (e) {
        if ($(this).find(".top-search").val() == "") {
            e.preventDefault();
        }
    });
    $(".header-main form.search-wrap .input-icon > i").click(function () {
        $(".header-main form.search-wrap").submit();
    });

// Jssocials share
    if ($('.sharer').length) {
        $(".sharer").jsSocials({
            showLabel: false,
            showCount: false,
            shares: [{
                renderer: function () {
                    var $result = $("<div>");
                    var script = document.createElement("script");
                    script.text = "(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = \"//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.3\"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));";
                    $result.append(script);
                    $("<div>").addClass("fb-share-button")
                        .attr("data-layout", "button_count")
                        .appendTo($result);
                    return $result;
                }
            }, {
                renderer: function () {
                    var $result = $("<div>");
                    var script = document.createElement("script");
                    script.src = "https://apis.google.com/js/platform.js";
                    $result.append(script);
                    $("<div>").addClass("g-plus")
                        .attr({
                            "data-action": "share",
                            "data-annotation": "bubble"
                        })
                        .appendTo($result);
                    return $result;
                }
            }, {
                renderer: function () {
                    var $result = $("<div>");
                    var script = document.createElement("script");
                    script.text = "window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src=\"https://platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,\"script\",\"twitter-wjs\"));";
                    $result.append(script);
                    $("<a>").addClass("twitter-share-button")
                        .text("Tweet")
                        .attr("href", "https://twitter.com/share")
                        .appendTo($result);
                    return $result;
                }
            }, {
                renderer: function () {
                    var $result = $("<div>");
                    var script = document.createElement("script");
                    script.src = "//platform.linkedin.com/in.js";
                    $result.append(script);
                    $("<script>").attr({type: "IN/Share", "data-counter": "right"})
                        .appendTo($result);
                    return $result;
                }
            }]
        });
    }

// Bootstrap Touchspin
    if ($('.touchspin-integer').length) {
        $(".touchspin-integer").TouchSpin({
            verticalbuttons: true,
            min: $(this).data('min'),
            max: $(this).data('max')
        });
    }
    if ($('.touchspin-decimal').length) {
        $(".touchspin-decimal").TouchSpin({
            verticalbuttons: true,
            min: $(this).data('min'),
            max: $(this).data('max'),
            decimals: 2,
            step: 0.01,
            prefix: $('body').data('currency-symbol')
        });
    }

// Implements on hover trigger for bootstrap 4 dropdowns
    function toggleDropdown(e) {
        const _d = $(e.target).closest('.dropdown'),
            _m = $('.dropdown-menu', _d);
        setTimeout(function () {
            const shouldOpen = e.type !== 'click' && _d.is(':hover');
            _m.toggleClass('show', shouldOpen);
            _d.toggleClass('show', shouldOpen);
            $('[data-toggle="dropdown"]', _d).attr('aria-expanded', shouldOpen);
        }, e.type === 'mouseleave' ? 25 : 0);
    }

    $('body')
        .on('mouseenter mouseleave', '.dropdown-hover', toggleDropdown)
        .on('click', '.dropdown-menu a', toggleDropdown);
// Initializes word rotator
    if ($('.rotate-words').length) {
        $('.rotate-words').Morphext({
            animation: "fadeIn",
            separator: "|",
            speed: 2000,
        });
    }


// Initializes Event start countdown
    if ($('.countdown').length) {
        $('.countdown').each(function () {
            var $thiscountdown = $(this);
            var $countdownto = $thiscountdown.data('count-down-to');
            $thiscountdown.countdown($countdownto, function (event) {
                $(this).html(event.strftime(''
                    + '<span>%w</span> ' + 'weeks' + ' '
                    + '<span>%d</span> ' + 'days' + ' '
                    + '<span>%H</span> ' + 'hr' + ' '
                    + '<span>%M</span> ' + 'min' + ' '
                    + '<span>%S</span> ' + 'sec' + ' '));
            });
        });
    }

// PNotify notification ( success, notice, info, error )
    function showStackBarTop(type, title, text) {
        if (typeof window.stackBarTop === 'undefined') {
            window.stackBarTop = {
                'dir1': 'down',
                'firstpos1': 0,
                'spacing1': 0,
                'push': 'top'
            };
        }
        var opts = {
            type: type,
            title: title,
            text: text,
            addClass: 'stack-bar-top',
            cornerClass: 'ui-pnotify-sharp',
            shadow: true,
            width: '100%',
            stack: window.stackBarTop,
            styling: 'bootstrap4',
            icons: 'fontawesome5',
            hide: true,
            delay: 4000,
            mouseReset: true,
            modules: {
                Animate: {
                    animate: true,
                    inClass: "fadeInDown",
                    outClass: "fadeOutUp"
                },
            },
            Buttons: {
                closer: false,
                closerHover: true,
                sticker: false,
                stickerHover: true,
                labels: {
                    close: 'Close',
                    stick: 'Stick',
                    unstick: 'Unstick'
                }
            },
            Mobile: {
                swipeDismiss: true,
                styling: true
            }
        };
        PNotify.removeAll();
        PNotify.alert(opts);
    }

    global.showStackBarTop = showStackBarTop;

    if ($('.flash-message').length) {
        $('.flash-message').each(function () {
            showStackBarTop($(this).data('type'), '', $(this).data('message'));
        });
    }

// Initializes counters
    if ($('.counter').length) {
        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    }

// Initializes circular progress
    if ($('.chart-circle').length) {
        $('.chart-circle').each(function () {
            let $this = $(this);
            $this.circleProgress({
                fill: {
                    gradient: ["red", "green"]
                },
                size: $this.height(),
                startAngle: -Math.PI / 4 * 2,
                emptyFill: '#F4F4F4',
                lineCap: 'round'
            });
        });
    }

// Focuses on form errors
    var offsettop = 100;
    if ($(window).width() < 992) {
        offsettop = 170;
    }
    if ($('.form-control.is-invalid').length > 0) {
        $('html, body').animate({scrollTop: $($('.form-control.is-invalid')[0]).offset().top - offsettop}, 0);
    }

// Initializes Jquery Confirm
    $(document).on('click', '.requires-confirmation', function (e) {
        e.preventDefault();
        var $thisElement = $(this);

        // Check if the element has a data-target attribute
        if (typeof $thisElement.attr('data-target') !== typeof undefined && $thisElement.attr('data-target') !== false) {
            // Get the value of the data-target attribute
            var dataTarget = $thisElement.attr('data-target');

            // Check if the data-target attribute contains '/delete-permanently'
            if (dataTarget.indexOf('/delete-permanently') >= 0) {
                var $confirmDialog = $.confirm({
                    title: 'Confirmation required',
                    buttons: {
                        confirm: {
                            text: 'Confirm',
                            btnClass: 'btn-primary btn-sm',
                            keys: ['enter'],
                            action: function () {
                                location.href = $thisElement.data('target');
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            btnClass: 'btn-default btn-sm',
                            keys: ['esc'],
                            action: function () {
                                $confirmDialog.close();
                            }
                        }
                    }
                });
                return;
            }
        }

        // Check if the element has an href attribute
        if (typeof $thisElement.attr('href') !== typeof undefined && $thisElement.attr('href') !== false) {
            // Get the value of the href attribute
            var hrefValue = $thisElement.attr('href');

            // Check if the href attribute contains '/delete', '/disable', etc.
            if (
                hrefValue.indexOf('/delete') >= 0 ||
                hrefValue.indexOf('/disable') >= 0 ||
                hrefValue.indexOf('/featured') >= 0 ||
                hrefValue.indexOf('/notfeatured') >= 0 ||
                hrefValue.indexOf('/hide') >= 0
            ) {
                return false;
            }
        }
    });
    $(document).on('click', '.ajax-loading', function (e) {
        e.preventDefault();
        var $thisElement = $(this);
        var $confirmDialog = $.confirm({
            title: $thisElement.data('title'),
            columnClass: 'col-12',
            backgroundDismiss: true,
            content: function () {
                var self = this;
                return $.ajax({
                    url: $thisElement.data('url'),
                    dataType: 'html',
                    method: 'get'
                }).done(function (response) {
                    self.setContent(response);
                }).fail(function () {
                    self.setContent('An error has occured');
                });
            },
            onContentReady: function () {
                if ($('.fancybox').length) {
                    $(".fancybox").jqPhotoSwipe();
                }
                if ($('.gallery').length) {
                    $(".gallery a").jqPhotoSwipe({
                        forceSingleGallery: true
                    });
                }
            },
            buttons: {
                close: {
                    text: 'Close',
                    btnClass: 'btn-default btn-sm',
                    keys: ['esc'],
                    action: function () {
                        $confirmDialog.close();
                    }
                }
            }
        });
    });
    // Initializes Font Awesome picker
    if ($('.icon-picker').length) {
        $('.icon-picker').iconpicker({
            animation: false,
            inputSearch: true
        });
    }

    // Initializes select2
    $('.select2').each(function () {
        if ($(this).data("sort-options") == "1") {
            $(this).select2({
                theme: 'bootstrap4',
                allowClear: true,
                placeholder: 'Select an option',
                // sortResults: data => data.sort((a, b) => a.text.localeCompare(b.text, undefined, {sensitivity: 'base'}))
            });
        } else {
            $(this).select2({
                theme: 'bootstrap4',
                allowClear: true,
                placeholder: 'Select an option',
            });
        }
    });

    // Sortable select
    if ($('#sortable-select').length) {
        $('#sortable-select option').each(function () {
            if ($(this).data('direction') == getURLParameter('direction') && $(this).data('criteria') == getURLParameter('sort')) {
                $(this).prop('selected', true).trigger('change');
                $(this).prop('disabled', true).trigger('change');
                $('#slug').val(getURLParameter('sort')).change();
            }
        });
    }
    $('#sortable-select').on('select2-selecting', function (e) {
        window.location = e.val;
    });

// Initializes Photoswipe gallery
    if ($('.fancybox').length) {
        $(".fancybox").jqPhotoSwipe();
    }
    if ($('.gallery').length) {
        $(".gallery a").jqPhotoSwipe({
            forceSingleGallery: true
        });
    }

// Initializes wysiwyg editor
    if ($('.wysiwyg').length) {
        $('.wysiwyg').summernote({
            height: 500,
        });
    }

// Initializes form collection plugin
    if ($('.form-collection:not(.manual-init)').length) {
        $('.form-collection:not(.manual-init)').each(function () {
            $(this).collection({
                add_at_the_end: true,
                allow_add: true,
                allow_remove: true,
                allow_up: true,
                allow_down: true,
                add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm"><i class="fas fa-plus-square"></i> ' + 'Add' + '</a>',
                remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm"><i class="fas fa-minus-square"></i> ' + 'Remove' + '</a>',
                up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm"><i class="fas fa-caret-square-up"></i> ' + 'Move up' + '</a>',
                down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm"><i class="fas fa-caret-square-down"></i> ' + 'Move down' + '</a>',
                position_field_selector: '.form-collection-position'
            });
        });
    }

// Initializes range slider
    if ($('.range-slider').length) {
        $('.range-slider').each(function () {
            var $thisRangerSlider = $(this);
            noUiSlider.create($thisRangerSlider[0], {
                    start: [$thisRangerSlider.data('start-left'), $thisRangerSlider.data('start-right')],
                    direction: $('html').attr('lang') == 'ar' ? 'rtl' : 'ltr',
                    connect: true,
                    range: {
                        'min': $thisRangerSlider.data('min'),
                        'max': $thisRangerSlider.data('max')
                    },
                    format: wNumb({
                        decimals: 0
                    })
                }
            );
            $thisRangerSlider[0].noUiSlider.on('update', function (values, handle) {
                    $thisRangerSlider.siblings('.ranger-slider-inputs').find('.range-slider-min-input').val(values[0]);
                    $thisRangerSlider.siblings('.ranger-slider-inputs').find('.range-slider-max-input').val(values[1]);
                }
            );
            $thisRangerSlider.siblings('.ranger-slider-inputs').find('.range-slider-min-input').on('change', function () {
                    $thisRangerSlider[0].noUiSlider.set([this.value, null]);
                }
            );
            $thisRangerSlider.siblings('.ranger-slider-inputs').find('.range-slider-max-input').on('change', function () {
                    $thisRangerSlider[0].noUiSlider.set([null, this.value]);
                }
            );
        });
    }

// Select2 autocomplete
//     if ($('.autocomplete').length) {
//         $('.autocomplete').each(function () {
//             var $thisAutocomplete = $(this);
    // $thisAutocomplete.autocompleter({
    // url_list: $thisAutocomplete.data('url-list'),
    // otherOptions: {
    // minimumInputLength: typeof $thisAutocomplete.data("minimum-input-length") !== 'undefined' ? $thisAutocomplete.data("minimum-input-length") : 3,
    // formatNoMatches: 'No results found',
    // formatSearching: 'Searching ...',
    // formatInputTooShort: 'Insert at least 3 characters'
    // }
    // });
    // });
    // }

// Tags input
    if ($(".tags-input").length) {
        $(".tags-input").each(function () {
            $(this).tagsinput({
                tagClass: 'badge badge-primary'
            });
        });
        $('.bootstrap-tagsinput').each(function () {
            $(this).addClass('form-control');
        });
    }

// Datetimepickers
    if ($('.datetimepicker').length) {
        $('.datetimepicker').each(function () {
            $(this).datetimepicker({
                format: 'Y-m-d H:i'
            });
        });
    }

    if ($('.datepicker').length) {
        $('.datepicker').each(function () {
            $(this).datetimepicker({
                format: 'Y-m-d',
                timepicker: false
            });
        });
    }

    if ($('.publish-date-picker').length) {
        $('.publish-date-picker').each(function () {
            $(this).datetimepicker({
                format: 'Y-m-d H:i', // Include date and time
                timepicker: true // Enable timepicker
            });
        });
    }

    if ($('.publish-date-picker').length) {
        $('.publish-date-picker').each(function () {
            $(this).datetimepicker({
                format: 'Y-m-d H:i', // Include date and time
                timepicker: true // Enable timepicker
            });
        });
    }

// Event form dates collection
    if ($('.eventdates-collectionsimple').length) {
        $('.eventdates-collection').each(function () {
            $(this).collection({
                min: 1,
                prefix: 'eventdate',
                add_at_the_end: true,
                allow_add: true,
                allow_remove: true,
                allow_duplicate: false,
                allow_up: true,
                allow_down: true,
                add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-add-button"><i class="fas fa-plus-square"></i> ' + 'Add a new event date' + '</a>',
                remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-remove-button"><i class="fas fa-minus-square"></i> ' + 'Remove this event date' + '</a>',
                duplicate: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-duplicate-button"><i class="fas fa-clone"></i> ' + 'Duplicate this event date' + '</a>',
                up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-up-button"><i class="fas fa-caret-square-up"></i> ' + 'Move up' + '</a>',
                down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-down-button"><i class="fas fa-caret-square-down"></i> ' + 'Move down' + '</a>',
                children: [{
                    min: 1,
                    selector: '.eventtickets-collection',
                    prefix: 'eventticket',
                    add_at_the_end: true,
                    allow_add: true,
                    allow_remove: true,
                    allow_duplicate: false,
                    allow_up: true,
                    allow_down: true,
                    position_field_selector: '.event-date-ticket-position',
                    // add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-add-button"><i class="fas fa-plus-square"></i> ' + 'Add a new ticket' + '</a>',
                    // remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-remove-button"><i class="fas fa-minus-square"></i> ' + 'Remove this ticket' + '</a>',
                    // duplicate: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-duplicate-button"><i class="fas fa-clone"></i> ' + 'Duplicate this ticket' + '</a>',
                    // up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-up-button"><i class="fas fa-caret-square-up"></i> ' + 'Move up' + '</a>',
                    // down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-down-button"><i class="fas fa-caret-square-down"></i> ' + 'Move down' + '</a>',
                    after_add: function (eventticketscollection, eventticketelement) {

                        // Presets event date ticket radios
                        if ($('.section-content').hasClass("add-event")) {
                            eventticketelement.find(".is-ticket-active input:radio[value='1']").attr('checked', true);
                            eventticketelement.find(".is-ticket-free-radio input:radio[value='0']").attr('checked', true);
                        }

                        // Initializes event date ticket plugins
                        eventticketelement.find(".touchspin-integer").TouchSpin({
                            verticalbuttons: true,
                            min: $(this).data('min'),
                            max: $(this).data('max')
                        });
                        eventticketelement.find(".touchspin-decimal").TouchSpin({
                            verticalbuttons: true,
                            min: $(this).data('min'),
                            max: $(this).data('max'),
                            decimals: 2,
                            prefix: $('body').data('currency-symbol')
                        });
                        eventticketelement.find('.datetimepicker').each(function () {
                            $(this).datetimepicker({
                                format: 'Y-m-d H:i'
                            });
                        });
                    },
                    after_init: function (eventticketscollection, eventticketelement) {

                        // Hides ticket price and promotional price if not free checked
                        $(document).on("change", ".is-ticket-free-radio input[type=radio]", function () {
                            if (this.value == 1) {
                                $(this).closest('.form-group').next('.form-group').hide();
                                $(this).closest('.form-group').next('.form-group').next('.form-group').hide();
                                $(this).closest('.form-group').next('.form-group').find(".event-date-ticket-price").val('');
                                $(this).closest('.form-group').next('.form-group').find(".event-date-ticket-promotionalprice").val('');
                            } else {
                                $(this).closest('.form-group').next('.form-group').show();
                                $(this).closest('.form-group').next('.form-group').next('.form-group').show();
                            }
                        });
                        if ($('.section-content').hasClass("edit-event")) {
                            $(".is-ticket-free-radio input[type=radio]:checked").each(function () {
                                if (this.value == 1) {
                                    $(this).closest('.form-group').next('.form-group').hide();
                                    $(this).closest('.form-group').next('.form-group').next('.form-group').hide();
                                }
                            });
                        }

                        // Trick to hide the 0 or 0.00 numbers
                        eventticketscollection.find('.event-date-ticket-price').each(function () {
                            if ($(this).val() == "0" || $(this).val() == "0.00") {
                                $(this).trigger('change');
                                $(this).val("");
                            }
                        });
                        eventticketscollection.find('.event-date-ticket-promotionalprice').each(function () {
                            if ($(this).val() == "0" || $(this).val() == "0.00") {
                                $(this).trigger('change');
                                $(this).val("");
                            }
                        });
                    }
                }],
                after_add: function (eventdatecollection, eventdateelement) {

                    // Pre-enables the event date
                    if ($('.section-content').hasClass("add-event")) {
                        eventdateelement.find(".is-event-date-active input:radio[value='1']").attr('checked', true);
                        eventdateelement.find(".is-event-date-online input:radio[value='0']").attr('checked', true);
                    }

                    // Initializes event date plugins
                    eventdateelement.find('.datetimepicker').each(function () {
                        $(this).datetimepicker({
                            format: 'Y-m-d H:i'
                        });
                    });
                    eventdateelement.find('.select2').each(function () {
                        $(this).select2({
                            theme: 'bootstrap4',
                            allowClear: true,
                            placeholder: 'Select an option'
                        });
                    });
                },
                after_init: function (eventdatecollection, eventdateelement) {
                    // Hides the venue select if online is checked
                    $(document).on("change", ".is-event-date-online input[type=radio]", function () {
                        if (this.value == 1) {
                            $(this).closest('.form-group').next('.form-group').hide();
                            $(this).closest('.form-group').next('.form-group').find(".event-date-venue").val([]);
                        } else {
                            $(this).closest('.form-group').next('.form-group').show();
                        }
                    });
                    if ($('.section-content').hasClass("edit-event")) {
                        $(".is-event-date-online input[type=radio]:checked").each(function () {
                            if (this.value == 1) {
                                $(this).closest('.form-group').next('.form-group').hide();
                            }
                        });
                    }
                }
            });
        });
    }

    if ($('.eventdates-collection').length) {
        $('.eventdates-collection').each(function () {
            $(this).collection({
                min: 1,
                prefix: 'eventdate',
                add_at_the_end: true,
                allow_add: true,
                allow_remove: true,
                allow_duplicate: false,
                allow_up: true,
                allow_down: true,
                add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-add-button"><i class="fas fa-plus-square"></i> ' + 'Add a new event date' + '</a>',
                remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-remove-button"><i class="fas fa-minus-square"></i> ' + 'Remove this event date' + '</a>',
                duplicate: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-duplicate-button"><i class="fas fa-clone"></i> ' + 'Duplicate this event date' + '</a>',
                up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-up-button"><i class="fas fa-caret-square-up"></i> ' + 'Move up' + '</a>',
                down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-down-button"><i class="fas fa-caret-square-down"></i> ' + 'Move down' + '</a>',
                children: [{
                    min: 1,
                    selector: '.eventtickets-collection',
                    prefix: 'eventticket',
                    add_at_the_end: true,
                    allow_add: true,
                    allow_remove: true,
                    allow_duplicate: false,
                    allow_up: true,
                    allow_down: true,
                    position_field_selector: '.event-date-ticket-position',
                    // add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-add-button"><i class="fas fa-plus-square"></i> ' + 'Add a new ticket' + '</a>',
                    // remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-remove-button"><i class="fas fa-minus-square"></i> ' + 'Remove this ticket' + '</a>',
                    // duplicate: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-duplicate-button"><i class="fas fa-clone"></i> ' + 'Duplicate this ticket' + '</a>',
                    // up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-up-button"><i class="fas fa-caret-square-up"></i> ' + 'Move up' + '</a>',
                    // down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-ticket-action-button event-ticket-down-button"><i class="fas fa-caret-square-down"></i> ' + 'Move down' + '</a>',
                    after_add: function (eventticketscollection, eventticketelement) {
                        // Presets event date ticket radios
                        if ($('.section-content').hasClass("add-event")) {
                            eventticketelement.find(".is-ticket-active input:radio[value='1']").attr('checked', true);
                            eventticketelement.find(".is-ticket-free-radio input:radio[value='0']").attr('checked', true);
                        }

                        // Initializes event date ticket plugins
                        eventticketelement.find(".touchspin-integer").TouchSpin({
                            verticalbuttons: true,
                            min: $(this).data('min'),
                            max: $(this).data('max')
                        });
                        eventticketelement.find(".touchspin-decimal").TouchSpin({
                            verticalbuttons: true,
                            min: $(this).data('min'),
                            max: $(this).data('max'),
                            decimals: 2,
                            prefix: $('body').data('currency-symbol')
                        });
                        eventticketelement.find('.datetimepicker').each(function () {
                            $(this).datetimepicker({
                                format: 'Y-m-d H:i'
                            });
                        });
                    },
                    after_init: function (eventticketscollection, eventticketelement) {

                        // Hides ticket price and promotional price if not free checked
                        $(document).on("change", ".is-ticket-free-radio input[type=radio]", function () {
                            if (this.value == 1) {
                                $(this).closest('.form-group').next('.form-group').hide();
                                $(this).closest('.form-group').next('.form-group').next('.form-group').hide();
                                $(this).closest('.form-group').next('.form-group').find(".event-date-ticket-price").val('');
                                $(this).closest('.form-group').next('.form-group').find(".event-date-ticket-promotionalprice").val('');
                            } else {
                                $(this).closest('.form-group').next('.form-group').show();
                                $(this).closest('.form-group').next('.form-group').next('.form-group').show();
                            }
                        });
                        if ($('.section-content').hasClass("edit-event")) {
                            $(".is-ticket-free-radio input[type=radio]:checked").each(function () {
                                if (this.value == 1) {
                                    $(this).closest('.form-group').next('.form-group').hide();
                                    $(this).closest('.form-group').next('.form-group').next('.form-group').hide();
                                }
                            });
                        }

                        // Trick to hide the 0 or 0.00 numbers
                        eventticketscollection.find('.event-date-ticket-price').each(function () {
                            if ($(this).val() == "0" || $(this).val() == "0.00") {
                                $(this).trigger('change');
                                $(this).val("");
                            }
                        });
                        eventticketscollection.find('.event-date-ticket-promotionalprice').each(function () {
                            if ($(this).val() == "0" || $(this).val() == "0.00") {
                                $(this).trigger('change');
                                $(this).val("");
                            }
                        });
                    }
                }],
                after_add: function (eventdatecollection, eventdateelement) {

                    // Pre-enables the event date
                    if ($('.section-content').hasClass("add-event")) {
                        eventdateelement.find(".is-event-date-active input:radio[value='1']").attr('checked', true);
                        eventdateelement.find(".is-event-date-online input:radio[value='0']").attr('checked', true);
                    }

                    // Initializes event date plugins
                    eventdateelement.find('.datetimepicker').each(function () {
                        $(this).datetimepicker({
                            format: 'Y-m-d H:i'
                        });
                    });
                    eventdateelement.find('.select2').each(function () {
                        $(this).select2({
                            theme: 'bootstrap4',
                            allowClear: true,
                            placeholder: 'Select an option'
                        });
                    });
                },
                after_init: function (eventdatecollection, eventdateelement) {
                    // Hides the venue select if online is checked
                    $(document).on("change", ".is-event-date-online input[type=radio]", function () {
                        if (this.value == 1) {
                            $(this).closest('.form-group').next('.form-group').hide();
                            $(this).closest('.form-group').next('.form-group').find(".event-date-venue").val([]);
                        } else {
                            $(this).closest('.form-group').next('.form-group').show();
                        }
                    });
                    if ($('.section-content').hasClass("edit-event")) {
                        $(".is-event-date-online input[type=radio]:checked").each(function () {
                            if (this.value == 1) {
                                $(this).closest('.form-group').next('.form-group').hide();
                            }
                        });
                    }
                }
            });
        });
    }

    if ($('.month-collection').length) {
        $('.month-collection').each(function () {
            $(this).collection({
                min: 1,
                prefix: 'month',
                add_at_the_end: true,
                allow_add: true,
                allow_remove: true,
                allow_duplicate: false,
                allow_up: true,
                allow_down: true,
                add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-add-button"><i class="fas fa-plus-square"></i> ' + 'Add a new month' + '</a>',
                remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-remove-button"><i class="fas fa-minus-square"></i> ' + 'Remove this month' + '</a>',
                duplicate: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-duplicate-button"><i class="fas fa-clone"></i> ' + 'Duplicate this month' + '</a>',
                up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-up-button"><i class="fas fa-caret-square-up"></i> ' + 'Move up' + '</a>',
                down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-date-down-button"><i class="fas fa-caret-square-down"></i> ' + 'Move down' + '</a>',
                // Add a callback when a new month is added
                after_add: function (collection, element) {
                    // Find the weeklySchedule form within the newly added month
                    var hours = $(element).find('.hour-collection');

                    // Initialize the WeeklyScheduleType form
                    hours.each(function () {
                        $(this).collection({
                            min: 1,
                            prefix: 'hour',
                            add_at_the_end: true,
                            allow_add: true,
                            allow_remove: true,
                            allow_duplicate: false,
                            allow_up: true,
                            allow_down: true,
                            add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-add-button"><i class="fas fa-plus-square"></i> ' + 'Add' + '</a>',
                            remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-remove-button"><i class="fas fa-minus-square"></i> ' + 'Remove' + '</a>',
                            duplicate: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-duplicate-button"><i class="fas fa-clone"></i> ' + 'Duplicate' + '</a>',
                            up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-up-button"><i class="fas fa-caret-square-up"></i> ' + 'Move' + '</a>',
                            down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-down-button"><i class="fas fa-caret-square-down"></i> ' + 'Move' + '</a>',
                        });
                    });
                }
            });

            $('.hour-collection').each(function () {
                $(this).collection({
                    min: 1,
                    prefix: 'hour',
                    add_at_the_end: true,
                    allow_add: true,
                    allow_remove: true,
                    allow_duplicate: false,
                    allow_up: true,
                    allow_down: true,
                    add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-add-button"><i class="fas fa-plus-square"></i> ' + 'Add' + '</a>',
                    remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-remove-button"><i class="fas fa-minus-square"></i> ' + 'Remove' + '</a>',
                    duplicate: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-duplicate-button"><i class="fas fa-clone"></i> ' + 'Duplicate' + '</a>',
                    up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-up-button"><i class="fas fa-caret-square-up"></i> ' + 'Move' + '</a>',
                    down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-down-button"><i class="fas fa-caret-square-down"></i> ' + 'Move' + '</a>',

                });
            });
        });
    }

    if ($('.hour-collection').length) {
        $('.hour-collection').each(function () {
            $(this).collection({
                min: 1,
                prefix: 'hour',
                add_at_the_end: true,
                allow_add: true,
                allow_remove: true,
                allow_duplicate: false,
                allow_up: true,
                allow_down: true,
                add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-add-button"><i class="fas fa-plus-square"></i> ' + 'Add' + '</a>',
                remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-remove-button"><i class="fas fa-minus-square"></i> ' + 'Remove' + '</a>',
                duplicate: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-duplicate-button"><i class="fas fa-clone"></i> ' + 'Duplicate' + '</a>',
                up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-up-button"><i class="fas fa-caret-square-up"></i> ' + 'Move' + '</a>',
                down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm event-hour-down-button"><i class="fas fa-caret-square-down"></i> ' + 'Move' + '</a>',

            });
        });
    }

    // Event favorites ajax add and remove
    $(document).on("click", ".event-favorites-add, .event-favorites-remove", function () {
        var $thisButton = $(this);
        if ($thisButton.attr("data-action-done") == "1") {
            $thisButton.unbind("click");
            return false;
        }
        $.ajax({
            type: "GET",
            url: $thisButton.data('target'),
            beforeSend: function () {
                $thisButton.attr("data-action-done", "1");
                $thisButton.html("<i class='fas fa-spinner fa-spin'></i>");
            },
            success: function (response) {
                if (response.hasOwnProperty('success')) {
                    if ($thisButton.hasClass('event-favorites-add')) {
                        $thisButton.html('<i class="fas fa-heart"></i>');
                    } else {
                        $thisButton.html('<i class="far fa-heart"></i>');
                    }
                    $thisButton.attr("title", response.success).tooltip("_fixTitle");
                    showStackBarTop('success', '', response.success);
                } else if (response.hasOwnProperty('error')) {
                    $thisButton.html('<i class="far fa-heart"></i>');
                    $thisButton.attr("title", response.error).tooltip("_fixTitle");
                    showStackBarTop('error', '', response.error);
                } else {
                    $thisButton.html('<i class="far fa-heart"></i>');
                    $thisButton.attr("title", 'An error has occured').tooltip("_fixTitle");
                    showStackBarTop('error', '', 'An error has occured');
                }
            }
        });
    });

    // Event favorites ajax add and remove
    $(document).on("click", ".bigbtn-event-favorites-add, .bigbtn-event-favorites-remove", function () {
        var $thisButton = $(this);
        if ($thisButton.attr("data-action-done") === "1") {
            $thisButton.unbind("click");
            return false;
        }
        $.ajax({
            type: "GET",
            url: $thisButton.data('target'),
            beforeSend: function () {
                $thisButton.attr("data-action-done", "1");
                $thisButton.html("<i class='fas fa-spinner fa-spin'></i>");
            },
            success: function (response) {
                if (response.hasOwnProperty('success')) {
                    if ($thisButton.hasClass('bigbtn-event-favorites-add')) {
                        $thisButton.html('<i class="fas fa-heart"></i> REMOVE FROM FAVORITES');
                    } else {
                        $thisButton.html('<i class="far fa-heart"></i> ADD TO FAVORITES');
                    }
                    $thisButton.attr("title", response.success).tooltip("_fixTitle");
                    showStackBarTop('success', '', response.success);
                } else if (response.hasOwnProperty('error')) {
                    $thisButton.html('<i class="far fa-heart"></i>');
                    $thisButton.attr("title", response.error).tooltip("_fixTitle");
                    showStackBarTop('error', '', response.error);
                } else {
                    $thisButton.html('<i class="far fa-heart"></i>');
                    $thisButton.attr("title", 'An error has occured').tooltip("_fixTitle");
                    showStackBarTop('error', '', 'An error has occured');
                }
            }
        });
    });


    //  Lazy loading images
    var bLazy = new Blazy({
        selector: '.img-lazy-load',
        success: function (ele) {
            $(ele).find('.loader').remove();
        },
        error: function (ele, msg) {
            $(ele).find('.loader').remove();
            if (document.body.classList.contains('universe-public')) {
                if (ele.tagName === 'IMG') ele.src = '/images/universe-placeholder.svg';
                else ele.style.backgroundImage = 'url(/images/universe-placeholder.svg)';
            }
        }
    });

    //  Owl Carousel
    if ($('.owl-init').length) {

        $(".owl-init").each(function () {
            var owlcarousel = $(this);
            owlcarousel.owlCarousel({
                rtl: ($("html").attr("lang") == "ar" ? true : false),
                loop: owlcarousel.data('loop'),
                margin: owlcarousel.data('margin'),
                nav: owlcarousel.data('nav'),
                dots: owlcarousel.data('dots'),
                autoplay: owlcarousel.data('autoplay'),
                items: owlcarousel.data('items'),
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    1000: {
                        items: owlcarousel.data('items')
                    }
                }
            });
            $('.' + owlcarousel.data('custom-nav') + '.owl-custom-next').click(function () {
                $('#' + owlcarousel.attr('id')).trigger('next.owl.carousel');
            });
            $('.' + owlcarousel.data('custom-nav') + '.owl-custom-prev').click(function () {
                    $('#' + owlcarousel.attr('id')).trigger('prev.owl.carousel');
                }
            );
            // TODO ventic: recheck this
            owlcarousel.on('changed.owl.carousel', bLazy.revalidate);
        });
    }

// Readmore
    if ($('.readmore').length) {
        $('.readmore').each(function () {
            new Readmore($(this), {
                speed: 200,
                lessLink: '<a href="#">' + 'Close' + '</a>',
                moreLink: '<a href="#">' + 'Read more' + '</a>',
                collapsedHeight: parseInt($(this).data('collapsed-height')),
                heightMargin: parseInt($(this).data('height-margin')),
                embedCSS: true,
                afterToggle: function (trigger, element, expanded) {
                    if (!expanded) { // The "Close" link was clicked
                        $(element).removeClass("expanded");
                    } else {
                        $(element).addClass("expanded");
                    }
                },
                blockProcessed: function (element, collapsable) {
                    if (collapsable) {
                        $(element).addClass("collapsable");
                    }
                }
            });
        });
    }

// Add to calendar
    if ($('#add-to-calendar').length) {
        var myCalendar = Ouical.createCalendar({
            data: {
                title: $('#add-to-calendar-link').data('title'),
                start: $('#add-to-calendar-link').data('start') ? new Date($('#add-to-calendar-link').data('start')) : '',
                duration: '',
                end: $('#add-to-calendar-link').data('end') ? new Date($('#add-to-calendar-link').data('end')) : '',
                address: $('#add-to-calendar-link').data('address') ? $('#add-to-calendar-link').data('address') : '',
                description: $('#add-to-calendar-link').data('description') + '...'
            },
            services: {
                yahoo: false, // Hide from Yahoo Calendar
            }
        });
        document.querySelector('#add-to-calendar').appendChild(myCalendar);
    }

    $(document).on("click", ".organizer-follow, .organizer-unfollow", function () {
        const $thisButton = $(this);
        if ($thisButton.attr("data-action-done") == "1") {
            // Prevent multiple clicks
            return false;
        }

        const targetUrl = $thisButton.data('target'); // Get the URL from data attribute

        $.ajax({
            type: "GET",
            url: targetUrl, // Use the extracted URL
            beforeSend: function () {
                // Set the data-action-done attribute to prevent multiple clicks
                $thisButton.attr("data-action-done", "1");

                // Update button text with spinner and action description
                if ($thisButton.hasClass('organizer-follow')) {
                    $thisButton.html('<i class="fas fa-spinner fa-spin"></i>');
                } else {
                    $thisButton.html('<i class="fas fa-spinner fa-spin"></i>');
                }
            },
            success: function (response) {
                // Restore the original button text
                if ($thisButton.hasClass('organizer-follow')) {
                    $thisButton.html('<i class="fas fa-bell-slash"></i>');
                } else {
                    $thisButton.html('<i class="fas fa-bell"></i>');
                }

                if (response.hasOwnProperty('success')) {
                    // Show success message
                    showStackBarTop('success', '', response.success);
                } else if (response.hasOwnProperty('error')) {
                    // Show error message
                    showStackBarTop('error', '', response.error);
                } else {
                    // Show a generic error message
                    showStackBarTop('error', '', 'An error has occurred');
                }
            },
            error: function () {
                // Restore the original button text on error
                if ($thisButton.hasClass('organizer-follow')) {
                    $thisButton.html('<i class="fas fa-bell"></i>');
                } else {
                    $thisButton.html('<i class="fas fa-bell-slash"></i>');
                }

                // Show a generic error message
                showStackBarTop('error', '', 'An error has occurred');
            }
        });
    });
    $(document).on("click", ".attendee-follow, .attendee-unfollow", function () {
        const $thisButton = $(this);
        if ($thisButton.attr("data-action-done") == "1") {
            // Prevent multiple clicks
            return false;
        }

        const targetUrl = $thisButton.data('target'); // Get the URL from data attribute

        $.ajax({
            type: "GET",
            url: targetUrl, // Use the extracted URL
            beforeSend: function () {
                // Set the data-action-done attribute to prevent multiple clicks
                $thisButton.attr("data-action-done", "1");

                // Update button text with spinner and action description
                if ($thisButton.hasClass('attendee-follow')) {
                    $thisButton.html('<i class="fas fa-spinner fa-spin"></i>');
                } else {
                    $thisButton.html('<i class="fas fa-spinner fa-spin"></i>');
                }
            },
            success: function (response) {
                // Restore the original button text
                if ($thisButton.hasClass('attendee-follow')) {
                    $thisButton.html('<i class="fas fa-bell-slash"></i>');
                } else {
                    $thisButton.html('<i class="fas fa-bell"></i>');
                }

                if (response.hasOwnProperty('success')) {
                    // Show success message
                    showStackBarTop('success', '', response.success);
                } else if (response.hasOwnProperty('error')) {
                    // Show error message
                    showStackBarTop('error', '', response.error);
                } else {
                    // Show a generic error message
                    showStackBarTop('error', '', 'An error has occurred');
                }
            },
            error: function () {
                // Restore the original button text on error
                if ($thisButton.hasClass('attendee-follow')) {
                    $thisButton.html('<i class="fas fa-bell"></i>');
                } else {
                    $thisButton.html('<i class="fas fa-bell-slash"></i>');
                }

                // Show a generic error message
                showStackBarTop('error', '', 'An error has occurred');
            }
        });
    });

    $(document).on("click", ".organizer-join, .organizer-leave, .organizer-cancel, .organizer-reject, .organizer-accept", function () {
        const $thisButton = $(this);
        if ($thisButton.attr("data-action-done") == "1") {
            // Prevent multiple clicks
            return false;
        }

        const targetUrl = $thisButton.data('target'); // Get the URL from data attribute

        $.ajax({
            type: "GET",
            url: targetUrl, // Use the extracted URL
            beforeSend: function () {
                // Set the data-action-done attribute to prevent multiple clicks
                $thisButton.attr("data-action-done", "1");

                // Update button text with spinner and action description
                if ($thisButton.hasClass('organizer-join')) {
                    $thisButton.html('<i class="fas fa-spinner fa-spin"></i> Requesting');
                } else if ($thisButton.hasClass('organizer-leave')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Leaving');
                } else if ($thisButton.hasClass('organizer-accept')) {
                    $thisButton.html('<i class="fas fa-folder-plus"></i> Accepting');
                } else if ($thisButton.hasClass('organizer-reject')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Rejecting');
                } else {
                    $thisButton.html('<i class="fas fa-spinner fa-spin"></i> Canceling');
                }
            },
            success: function (response) {
                // Restore the original button text
                if ($thisButton.hasClass('organizer-join')) {
                    $thisButton.html('<i class="fas fa-folder-plus"></i> Requested');
                } else if ($thisButton.hasClass('organizer-leave')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Left');
                } else if ($thisButton.hasClass('organizer-accept')) {
                    $thisButton.html('<i class="fas fa-folder-plus"></i> Accepted');
                    $('.organizer-reject').hide();
                } else if ($thisButton.hasClass('organizer-reject')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Rejected');
                    $('.organizer-accept').hide();
                } else {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Canceled');
                }

                if (response.hasOwnProperty('success')) {
                    // Show success message
                    showStackBarTop('success', '', response.success);
                } else if (response.hasOwnProperty('error')) {
                    // Show error message
                    showStackBarTop('error', '', response.error);
                } else {
                    // Show a generic error message
                    showStackBarTop('error', '', 'An error has occurred');
                }
            },
            error: function () {
                // Restore the original button text on error
                if ($thisButton.hasClass('organizer-join')) {
                    $thisButton.html('<i class="fas fa-folder-plus"> Join</i>');
                } else if ($thisButton.hasClass('organizer-leave')) {
                    $thisButton.html('<i class="fas fa-folder-minus"> Leave</i>');
                } else if ($thisButton.hasClass('organizer-accept')) {
                    $thisButton.html('<i class="fas fa-folder-plus"> Accept</i>');
                } else if ($thisButton.hasClass('organizer-reject')) {
                    $thisButton.html('<i class="fas fa-folder-minus"> Reject</i>');
                } else {
                    $thisButton.html('<i class="fas fa-folder-minus"> Cancel</i>');
                }

                // Show a generic error message
                showStackBarTop('error', '', 'An error has occurred');
            }
        });
    });


    $(document).on("click", ".asorganizer-join, .asorganizer-kick, .asorganizer-cancel, .asorganizer-reject, .asorganizer-accept", function () {
        const $thisButton = $(this);
        if ($thisButton.attr("data-action-done") == "1") {
            // Prevent multiple clicks
            return false;
        }

        const targetUrl = $thisButton.data('target'); // Get the URL from data attribute

        $.ajax({
            type: "GET",
            url: targetUrl, // Use the extracted URL
            beforeSend: function () {
                // Set the data-action-done attribute to prevent multiple clicks
                $thisButton.attr("data-action-done", "1");

                // Update button text with spinner and action description
                if ($thisButton.hasClass('asorganizer-join')) {
                    $thisButton.html('<i class="fas fa-spinner fa-spin"></i> Inviting');
                } else if ($thisButton.hasClass('asorganizer-kick')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Kicking');
                } else if ($thisButton.hasClass('asorganizer-reject')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Rejecting');
                } else if ($thisButton.hasClass('asorganizer-accept')) {
                    $thisButton.html('<i class="fas fa-folder-plus"></i> Accepting');
                } else {
                    $thisButton.html('<i class="fas fa-spinner fa-spin"></i> Canceling');
                }
            },
            success: function (response) {
                // Restore the original button text
                if ($thisButton.hasClass('asorganizer-join')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Invited');
                } else if ($thisButton.hasClass('asorganizer-kick')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Kicked');
                } else if ($thisButton.hasClass('asorganizer-reject')) {
                    $thisButton.html('<i class="fas fa-folder-minus"></i> Rejected');
                    $('.asorganizer-accept').hide();
                } else if ($thisButton.hasClass('asorganizer-accept')) {
                    $thisButton.html('<i class="fas fa-folder-plus"></i> Accepted');
                    $('.asorganizer-reject').hide();
                } else {
                    $thisButton.html('<i class="fas fa-folder-plus"></i> Canceled');
                }

                if (response.hasOwnProperty('success')) {
                    // Show success message
                    showStackBarTop('success', '', response.success);
                } else if (response.hasOwnProperty('error')) {
                    // Show error message
                    showStackBarTop('error', '', response.error);
                } else {
                    // Show a generic error message
                    showStackBarTop('error', '', 'An error has occurred');
                }
            },
            error: function () {
                // Restore the original button text on error
                if ($thisButton.hasClass('asorganizer-join')) {
                    $thisButton.html('<i class="fas fa-folder-plus"> Invite</i>');
                } else if ($thisButton.hasClass('asorganizer-kick')) {
                    $thisButton.html('<i class="fas fa-folder-minus"> Kick</i>');
                } else if ($thisButton.hasClass('asorganizer-reject')) {
                    $thisButton.html('<i class="fas fa-folder-minus"> Reject</i>');
                } else if ($thisButton.hasClass('asorganizer-accept')) {
                    $thisButton.html('<i class="fas fa-folder-plus"> Accept</i>');
                } else {
                    $thisButton.html('<i class="fas fa-folder-minus"> Cancel</i>');
                }

                // Show a generic error message
                showStackBarTop('error', '', 'An error has occurred');
            }
        });
    });


// Initializes Bloodhound Search Engine

    var eventsForTopSearch = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: location.protocol + '//' + location.host + Routing.generate('get_events', {'_locale': 'en'}, false) + "?q=%QUERY",
            wildcard: '%QUERY'
        },
    });

    // $('.top-search').typeahead({
    //     hint: false,
    //     highlight: true,
    //     minLength: 0,
    //     limit: 3
    // }, {
    //     name: 'top-search',
    //     display: 'text',
    //     source: eventsForTopSearch,
    // templates: {
    //     empty: [
    //         '<div class="dropdown-menu show">',
    //         'No results found',
    //         '</div>'
    //     ].join('\n'),
    //     suggestion: Handlebars.compile($("#top-search-result-template").html())
    // }
    // });

// Initializes Ajax Pagination
    if ($('.ajax-pagination').length) {
        new KnpPaginatorAjax().init({
            'loadMoreText': 'Load more',
            'loadingText': 'Loading...',
            'elementsSelector': '.ajax-pagination',
            'paginationSelector': 'ul.pagination'
        });
    }

// Refund Request Dialog
    $('#refund-request-button').click(function () {

        var $thisButton = $('#refund-request-button');
        var $refundRequestDialog = $.confirm({
            title: 'Confirmation required',
            content: $thisButton.data('confirmation-text') +
                '<textarea id="refund-request-message" rows="5" class="form-control mt-3 refund-request-message" placeholder="' + 'Message' + '"></textarea>',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary btn-sm',
                    keys: ['enter'],
                    action: function () {
                        if ($('#refund-request-message').val().length < 10 || $('#refund-request-message').val().length > 255) {
                            animateCSS('.refund-request-message', 'flash');
                            return false;
                        } else {
                            location.href = $thisButton.data('target') + "?message=" + $('#refund-request-message').val();
                        }
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-default btn-sm',
                    keys: ['esc'],
                    action: function () {
                        $refundRequestDialog.close();
                    }
                }
            }
        });
    });

// Refund Request Dialog
    $('.refund-reply-button').click(function () {

        var $thisButton = $('.refund-reply-button');
        var $refundReplyDialog = $.confirm({
            title: 'Confirmation required',
            content: $thisButton.data('confirmation-text') +
                '<textarea id="refund-reply-message" rows="5" class="form-control mt-3 refund-reply-message" placeholder="' + 'Message' + '"></textarea>',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary btn-sm',
                    keys: ['enter'],
                    action: function () {
                        location.href = $thisButton.data('target') + "?message=" + $('#refund-reply-message').val();
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-default btn-sm',
                    keys: ['esc'],
                    action: function () {
                        $refundReplyDialog.close();
                    }
                }
            }
        });
    });

    // Order cancel Dialog
    $('.order-cancel-button').click(function () {
        var $thisButton = $(this);
        var $orderCancelDialog = $.confirm({
            title: 'Confirmation required',
            // content: $thisButton.data('confirmation-text') +
            //     '<textarea id="order-cancel-note" rows="5" class="form-control mt-3" placeholder="' + 'Note' + '"></textarea>',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary btn-sm',
                    keys: ['enter'],
                    action: function () {
                        location.href = $thisButton.data('target') + "?note=" + $('#order-cancel-note').val();
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-default btn-sm',
                    keys: ['esc'],
                    action: function () {
                        $orderCancelDialog.close();
                    }
                }
            }
        });
    });

    // Resend order confirmation email dialog
    $('.resend-confirmation-email-button').click(function () {
        var $thisButton = $(this);
        var $resendOrderConfirmationDialog = $.confirm({
            title: 'Confirmation required',
            content: $thisButton.data('confirmation-text') +
                '<input id="order-confirmation-email-address" type="email" class="form-control mt-3" value="' + $thisButton.data("initial-email-address") + '" placeholder="' + 'Email address' + '">',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary btn-sm',
                    keys: ['enter'],
                    action: function () {
                        if (!isEmailValid($('#order-confirmation-email-address').val())) {
                            showStackBarTop('error', '', 'Make sure to enter a valid email address');
                            return false;
                        } else {
                            location.href = $thisButton.data('target') + "?email=" + $('#order-confirmation-email-address').val();
                        }
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-default btn-sm',
                    keys: ['esc'],
                    action: function () {
                        $resendOrderConfirmationDialog.close();
                    }
                }
            }
        });
    });

    // Handles the point of sale presubmit button order check
    $('#pos-save-order-button').click(function () {
        var qte = 0;
        $(".pos-eventdate-ticket-qte").each(function () {
            if ($(this).val() != "") {
                qte += $(this).val();
            }
        });
        if (qte == 0) {
            showStackBarTop('error', '', 'Please select the tickets quantity for this order');
        } else {
            $("#pos-order-form").submit();
        }
    });

    // Multi event dates calendar
    if ($('#event-dates-calendar').length) {

        // $('#add-to-cart-form .event-eventDate-wrapper:not(:first-child)').hide();

        // Initializes events passed from the template as a json array
        const eventsDatesJsonArray = $('#event-dates-calendar').data("event-dates");

        if (eventsDatesJsonArray.length) {
            let eventArrayLength = eventsDatesJsonArray.length;
            for (let i = 0; i < eventArrayLength; i++) {

                for (let j = 0; j < eventArrayLength; j++) {
                    if (i !== j && eventsDatesJsonArray[i].Date === eventsDatesJsonArray[j].Date) {
                        console.dir(eventsDatesJsonArray[j]);
                        console.dir(eventsDatesJsonArray[i]);
                        eventsDatesJsonArray[i].Title += ", " + eventsDatesJsonArray[j].Title;
                        eventsDatesJsonArray[i].Link += "|" + eventsDatesJsonArray[j].Link;
                        eventsDatesJsonArray.splice(j, 1);
                        eventArrayLength = eventsDatesJsonArray.length;
                    }
                }

                const eventDateArray = eventsDatesJsonArray[i].Date.split("-");
                eventsDatesJsonArray[i].Date = new Date(eventDateArray[0], eventDateArray[1] - 1, eventDateArray[2]);
            }
            const settings = {
                EventClick: '', //(function) a function that should instantiate on the click of any event. parameters passed in via data link attribute.
                EventTargetWholeDay: false, //(bool) clicking on the whole date will trigger event action, as opposed to just clicking on the title.
                DisabledDays: [], //(array of numbers) days of the week to be slightly transparent. ie: [1,6] to fade Sunday and Saturday.
            };

            caleandar(document.getElementById('event-dates-calendar'), eventsDatesJsonArray, settings);

            // Hides the calendar back button if current month is displayed
            $('.cld-rwd').css('visibility', 'hidden');
            var nextMonthButtonClickCount = 0;
            $(document).on("click", ".cld-fwd", function () {
                nextMonthButtonClickCount++;
                if (nextMonthButtonClickCount) {
                    $('.cld-rwd').css('visibility', 'visible');
                }
            });
            $(document).on("click", ".cld-rwd", function () {
                nextMonthButtonClickCount--;
                if (nextMonthButtonClickCount <= 0) {
                    $('.cld-rwd').css('visibility', 'hidden');
                }
            });

            // Disables the click on the time tooltip
            $(document).on("click", ".cld-title", function (e) {
                e.preventDefault();
                return false;
            });

            // Switches the calendar to the first event date month
            var currentMonthEventsCount = $(".cld-days .eventday").length;
            while (currentMonthEventsCount == 0) {
                $(".cld-fwd").trigger("click");
                currentMonthEventsCount = $(".cld-days .eventday").length;
            }

            // Opens the respective event date pop up
            $(document).on("click", ".cld-number.eventday", function () {
                const eventDateReference = $(this).find(".cld-title > a").attr("href");
                const eventDateHour = $(this).find(".cld-title > a").text();

                console.dir($(this).find(".cld-number.eventday"));

                if (eventDateReference.indexOf("|") == -1) {
                    $('.event-eventDate-wrapper').hide()
                    $("#eventDate-" + eventDateReference + "-wrapper").show();
                    $('#buy-tickets-modal-eventDate-' + eventDateReference).modal('show');
                } else {
                    var eventDateHoursDialogContent = "";
                    var eventDatesReferences = eventDateReference.split("|");
                    var eventDatesHours = eventDateHour.split(", ");
                    for (var i = 0; i < eventDatesReferences.length; i++) {
                        console.dir($('#add-to-calendar-link'));
                        console.dir($('#add-to-calendar-link').data());
                        var ticket = $('#add-to-calendar-link').data('ticket'); // Get the event.name value
                        // var eventName = $('#eventDate-' + eventDateReference).find('.modal-body h6').text();
                        // var eventName = eventDatesReferences[i]; // Get the ticket name for the current event date
                        eventDateHoursDialogContent += '<div>' +
                            '<b class="float-left">' + ticket + ' @ ' + eventDatesHours[i] + ' </b>' +
                            '<button class="eventDate-hours-modal-tickets-button float-right btn btn-primary btn-sm" data-eventDate-reference="' + eventDatesReferences[i] + '" data-dismiss="modal">' + 'Book' + '</button>' +
                            '<div class="clearfix"></div>' +
                            '</div>';
                        if (i != eventDatesReferences.length - 1) {
                            eventDateHoursDialogContent += "<hr>";
                        }
                    }
                    bootbox.dialog({
                        title: 'There are multiple events at this date',
                        message: eventDateHoursDialogContent,
                        size: 'medium',
                        onEscape: true,
                        backdrop: true,
                        className: ' modal-dialog-centered modal-dialog-scrollable',
                        buttons: {
                            fee: {
                                label: '<i class="far fa-window-close"></i> ' + 'Close',
                                className: 'btn btn-outline-primary',
                                callback: function () {

                                }
                            }
                        },
                        onShown: function (e) {
                            $(document).on("click", ".eventDate-hours-modal-tickets-button", function () {
                                var eventDateReference = $(this).data("eventdate-reference");
                                $('.event-eventDate-wrapper').hide()
                                $("#eventDate-" + eventDateReference + "-wrapper").show();
                                setTimeout(function () {
                                    $("#buy-tickets-modal-eventDate-" + eventDateReference).modal('show');
                                }, 2050);
                            });
                        }
                    });
                }
            });
        }
    }

    // Dashboard sidenav
    var dashboardMenuReturnFocus = null;
    function openDashboardSideNav() {
        var menu = document.getElementById("dashboard-sidenav");
        if (!menu) return;
        dashboardMenuReturnFocus = document.activeElement;
        var account = $('[aria-label="Account menu"]');
        if (account.attr('aria-expanded') === 'true') account.dropdown('toggle');
        menu.inert = false;
        menu.setAttribute('aria-hidden', 'false');
        menu.style.left = "0";
        document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
        $('[data-dashboard-menu-toggle]').attr('aria-expanded', 'true');
        menu.querySelector('.dashboard-sidenav-close').focus();
    }

    function closeDashboardSideNav() {
        var menu = document.getElementById("dashboard-sidenav");
        if (!menu) return;
        menu.style.left = "-250px";
        document.body.style.backgroundColor = "white";
        if (dashboardMenuReturnFocus && dashboardMenuReturnFocus.offsetParent !== null) dashboardMenuReturnFocus.focus();
        else $('[aria-label="Account menu"]').trigger('focus');
        menu.inert = true;
        menu.setAttribute('aria-hidden', 'true');
        $('[data-dashboard-menu-toggle]').attr('aria-expanded', 'false');
    }

    global.openDashboardSideNav = openDashboardSideNav;
    global.closeDashboardSideNav = closeDashboardSideNav;
    document.addEventListener('keydown', function (event) {
        var menu = document.getElementById('dashboard-sidenav');
        if (event.key === 'Escape' && menu && menu.getAttribute('aria-hidden') === 'false') closeDashboardSideNav();
    });

    // Back to top button
    if ($('.material-scrolltop').length) {
        $('body').materialScrollTop();
    }

    // Menu elements form collection
    if ($('.menuelements-collection').length) {
        $('.menuelements-collection').each(function () {
            $(this).collection({
                    min: 0,
                    // Remove immediately so a quick Save cannot submit a deleted row.
                    fade_out: false,
                    prefix: 'menuelement',
                    add_at_the_end: true,
                    allow_add: true,
                    allow_remove: true,
                    allow_duplicate: false,
                    allow_up: true,
                    allow_down: true,
                    add: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm"><i class="fas fa-plus-square"></i> ' + 'Add' + '</a>',
                    remove: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm"><i class="fas fa-minus-square"></i> ' + 'Remove' + '</a>',
                    up: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm"><i class="fas fa-caret-square-up"></i> ' + 'Move up' + '</a>',
                    down: '<a href="#" class="mr-3 btn btn-outline-dark btn-sm"><i class="fas fa-caret-square-down"></i> ' + 'Move down' + '</a>',
                    position_field_selector: '.menuelement-position',

                    after_add: function (menuElementsCollection, menuElement) {
                        menuElement.find('.icon-picker').iconpicker({
                            animation: false,
                            inputSearch: true
                        });
                        menuElement.find('.select2').each(function () {
                            if ($(this).data("sort-options") === "1") {
                                $(this).select2({
                                    theme: 'bootstrap4',
                                    allowClear: true,
                                    placeholder: 'Select an option',
                                    // sortResults: data => data.sort((a, b) => a.text.localeCompare(b.text, undefined, {sensitivity: 'base'})),
                                });
                            } else {
                                $(this).select2({
                                    theme: 'bootstrap4',
                                    allowClear: true,
                                    placeholder: 'Select an option',
                                });
                            }
                        });
                    }
                    ,
                    after_init: function (menuElementsCollection, menuElement) {
                    }
                }
            );
        });
    }

    // Send a test email to check the mail server configuration
    $('.send-test-email-button').click(function () {
        const $thisButton = $(this);
        const $sendTestEmailDialog = $.confirm({
            title: 'Mail server test email',
            content: $thisButton.data('confirmation-text') +
                '<input id="mail-test-email-address" type="email" class="form-control mt-3" placeholder="' + 'Email address' + '">',
            buttons: {
                confirm: {
                    text: 'Send',
                    btnClass: 'btn-primary btn-sm',
                    keys: ['enter'],
                    action: function () {
                        if (!isEmailValid($('#mail-test-email-address').val())) {
                            showStackBarTop('error', '', 'Make sure to enter a valid email address');
                            return false;
                        } else {
                            location.href = $thisButton.data('target') + "?email=" + $('#mail-test-email-address').val();
                        }
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-default btn-sm',
                    keys: ['esc'],
                    action: function () {
                        $sendTestEmailDialog.close();
                    }
                }
            }
        });
    });
});
