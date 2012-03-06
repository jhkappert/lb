/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function(){$widgetkit.lazyloaders["gallery-slider"]=function(b,a){var d=b.find(".slides:first"),c=d.children(),e=a.total_width=="auto"?b.width():a.total_width;c.css({width:e/c.length-a.spacing,"margin-right":a.spacing});d.width(c.eq(0).width()*c.length*2);b.css({width:e,height:a.height});$widgetkit.load(WIDGETKIT_URL+"/widgets/gallery/js/slider.js").done(function(){b.galleryslider(a)})}})(jQuery);
