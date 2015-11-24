/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

function createBackgroundSlider(path) {
    var sizes = [ [1000, 0, 1000], [1280, 1001, 1280], [1680, 1281, 1680], [1920, 1681, 5000] ];
    var imagesCount = 5;
    var width = $('body').width();

    for(var s in sizes)
    {
        if(width >= sizes[s][1] && width <= sizes[s][2])
        {
            for(var i = 1; i <= imagesCount; i++)
            {
                $('.background-images').append('<div style="background-image:url(\'http://cdn.veronecrm.com/crm/login-page/backgrounds/' + sizes[s][0] + '/' + i + '.jpg\');opacity:0;"></div>');
            }

            var img = new Image;

            img.onload = function() {
                $('.background-images div:first-child').addClass('active').fadeTo(0, 0).fadeTo(1000, 1);

                $('.unslpash-credits').css('display', 'block').fadeTo(400, 1);

                setInterval(function() {
                    var next = $('.background-images div.active').next();

                    if(next.length == 0)
                    {
                        next = $('.background-images div:first-child');
                    }

                    $('.background-images div.active').fadeTo(400, 0).removeClass('active');
                    next.fadeTo(400, 1).addClass('active');
                }, 5000);
            };

            img.src = 'http://cdn.veronecrm.com/crm/login-page/backgrounds/' + sizes[s][0] + '/1.jpg';
        }
    }
}
