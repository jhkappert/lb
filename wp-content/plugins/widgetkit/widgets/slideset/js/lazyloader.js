/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function(d){$widgetkit.lazyloaders.slideset=function(a,b){var c=a.find("ul.set").show();gwidth=b.width=="auto"?a.width():b.width;d.browser.msie&&d.browser.version<8&&c.children().css("display","inline");var e=b.height=="auto"?c.eq(0).outerHeight(!0):b.height;c.eq(0).parent().css({height:e});a.css({width:gwidth});c.css({height:e});$widgetkit.load(WIDGETKIT_URL+"/widgets/slideset/js/slideset.js").done(function(){a.slideset(b).css("visibility","visible");a.find("img[data-src]").each(function(){var a=
d(this),b=a.data("src");setTimeout(function(){a.attr("src",b)},1)})})}})(jQuery);
